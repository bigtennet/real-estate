<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Get settings
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$max_images = intval($settings['max_images_per_property'] ?? 8);
$max_video_size = intval($settings['max_video_size_mb'] ?? 100) * 1024 * 1024; // Convert to bytes
$enable_videos = ($settings['enable_video_uploads'] ?? 'true') === 'true';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$property_id = $_POST['property_id'] ?? null;
$file_type = $_POST['file_type'] ?? null; // 'image' or 'video'

if (!$property_id || !$file_type) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Validate file type
if (!in_array($file_type, ['image', 'video'])) {
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Check if video uploads are enabled
if ($file_type === 'video' && !$enable_videos) {
    echo json_encode(['error' => 'Video uploads are disabled']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$file_size = $file['size'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_mime = mime_content_type($file_tmp);

// Validate file size
if ($file_type === 'video' && $file_size > $max_video_size) {
    echo json_encode(['error' => "File too large. Maximum size: " . ($max_video_size / 1024 / 1024) . "MB"]);
    exit;
}

// Validate MIME type
$allowed_types = [
    'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    'video' => ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm']
];

if (!in_array($file_mime, $allowed_types[$file_type])) {
    echo json_encode(['error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types[$file_type])]);
    exit;
}

// Check existing files for this property
$stmt = $db->prepare("SELECT COUNT(*) as count FROM file_uploads WHERE property_id = ? AND file_type = ?");
$stmt->execute([$property_id, $file_type]);
$existing_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Check limits
if ($file_type === 'image' && $existing_count >= $max_images) {
    echo json_encode(['error' => "Maximum {$max_images} images allowed per property"]);
    exit;
}

if ($file_type === 'video' && $existing_count >= 1) {
    echo json_encode(['error' => 'Only one video allowed per property']);
    exit;
}

// Generate unique filename
$extension = pathinfo($file_name, PATHINFO_EXTENSION);
$unique_name = uniqid() . '_' . time() . '.' . $extension;

// Set upload directory
$upload_dir = $file_type === 'image' ? '../uploads/images/' : '../uploads/videos/';
$upload_path = $upload_dir . $unique_name;

// Create directory if it doesn't exist
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Move uploaded file
if (!move_uploaded_file($file_tmp, $upload_path)) {
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

// Save to database
$stmt = $db->prepare("INSERT INTO file_uploads (property_id, file_name, file_path, file_type, file_size, mime_type, is_primary) VALUES (?, ?, ?, ?, ?, ?, ?)");
$is_primary = ($file_type === 'image' && $existing_count === 0) ? 1 : 0; // First image is primary
$stmt->execute([$property_id, $file_name, $upload_path, $file_type, $file_size, $file_mime, $is_primary]);

$file_id = $db->lastInsertId();

// Update property images/video if needed
if ($file_type === 'image') {
    // Get all images for this property
    $stmt = $db->prepare("SELECT file_path FROM file_uploads WHERE property_id = ? AND file_type = 'image' ORDER BY is_primary DESC, created_at ASC");
    $stmt->execute([$property_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Update property images JSON
    $stmt = $db->prepare("UPDATE properties SET images = ? WHERE id = ?");
    $stmt->execute([json_encode($images), $property_id]);
} elseif ($file_type === 'video') {
    // Update property video URL
    $stmt = $db->prepare("UPDATE properties SET video_url = ? WHERE id = ?");
    $stmt->execute([$upload_path, $property_id]);
}

echo json_encode([
    'success' => true,
    'file_id' => $file_id,
    'file_name' => $file_name,
    'file_path' => $upload_path,
    'file_type' => $file_type,
    'file_size' => $file_size
]);
?>
