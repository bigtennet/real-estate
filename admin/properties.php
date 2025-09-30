<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Manage Properties - " . ($site_settings['site_name'] ?? 'Admin');
include '../includes/header.php';

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Delete associated files
    $stmt = $db->prepare("SELECT file_path FROM file_uploads WHERE property_id = ?");
    $stmt->execute([$id]);
    $files = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($files as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete file records
    $stmt = $db->prepare("DELETE FROM file_uploads WHERE property_id = ?");
    $stmt->execute([$id]);
    
    // Delete property
    $stmt = $db->prepare("DELETE FROM properties WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: properties.php');
    exit;
}

// Handle file deletion
if (isset($_GET['delete_file'])) {
    $file_id = $_GET['delete_file'];
    $stmt = $db->prepare("SELECT file_path, property_id FROM file_uploads WHERE id = ?");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($file) {
        // Delete physical file
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
        
        // Delete database record
        $stmt = $db->prepare("DELETE FROM file_uploads WHERE id = ?");
        $stmt->execute([$file_id]);
        
        // Update property images if it was an image
        $stmt = $db->prepare("SELECT file_path FROM file_uploads WHERE property_id = ? AND file_type = 'image' ORDER BY is_primary DESC, created_at ASC");
        $stmt->execute([$file['property_id']]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $db->prepare("UPDATE properties SET images = ? WHERE id = ?");
        $stmt->execute([json_encode($images), $file['property_id']]);
    }
    
    header('Location: properties.php');
    exit;
}

// Get properties with file counts - handle both old and new database structure
$query = "SELECT p.*,
          (SELECT COUNT(*) FROM file_uploads WHERE property_id = p.id AND file_type = 'image') as image_count,
          (SELECT COUNT(*) FROM file_uploads WHERE property_id = p.id AND file_type = 'video') as video_count
          FROM properties p 
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white">Manage Properties</h1>
                <a href="add-property.php" class="btn-primary">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Add Property
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-4 px-2">Property</th>
                            <th class="text-left py-4 px-2">Location</th>
                            <th class="text-left py-4 px-2">Price</th>
                            <th class="text-left py-4 px-2">Type</th>
                            <th class="text-left py-4 px-2">Status</th>
                            <th class="text-left py-4 px-2">Files</th>
                            <th class="text-left py-4 px-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($properties as $property): ?>
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="py-4 px-2">
                                <div class="flex items-center">
                                    <?php 
                                    $images = json_decode($property['images'], true);
                                    $main_image = $images[0] ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=100&h=100&fit=crop';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($main_image); ?>" alt="" class="w-12 h-12 rounded-lg object-cover mr-3">
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($property['title']); ?></div>
                                        <div class="text-gray-400 text-sm"><?php echo $property['bedrooms']; ?> bed, <?php echo $property['bathrooms']; ?> bath</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-2"><?php echo htmlspecialchars($property['location']); ?></td>
                            <td class="py-4 px-2">₦<?php echo number_format($property['price']); ?></td>
                            <td class="py-4 px-2"><?php echo $property['type']; ?></td>
                            <td class="py-4 px-2">
                                <span class="px-2 py-1 rounded-full text-xs <?php 
                                    echo $property['status'] === 'AVAILABLE' ? 'bg-green-500/20 text-green-300' : 
                                        ($property['status'] === 'SOLD' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300');
                                ?>">
                                    <?php echo $property['status']; ?>
                                </span>
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex space-x-2">
                                    <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded-full text-xs">
                                        <i data-lucide="image" class="w-3 h-3 inline mr-1"></i>
                                        <?php echo $property['image_count']; ?>
                                    </span>
                                    <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded-full text-xs">
                                        <i data-lucide="video" class="w-3 h-3 inline mr-1"></i>
                                        <?php echo $property['video_count']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex space-x-2">
                                    <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="p-2 bg-green-500/20 text-green-300 rounded-lg hover:bg-green-500/30 transition-colors" title="View Details">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="edit-property.php?id=<?php echo $property['id']; ?>" class="p-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <a href="manage-files.php?id=<?php echo $property['id']; ?>" class="p-2 bg-purple-500/20 text-purple-300 rounded-lg hover:bg-purple-500/30 transition-colors" title="Manage Files">
                                        <i data-lucide="folder" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?delete=<?php echo $property['id']; ?>" onclick="return confirm('Are you sure you want to delete this property?')" class="p-2 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
