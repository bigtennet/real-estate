<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Manage Files - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    header('Location: properties.php');
    exit;
}

// Get property details
$stmt = $db->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header('Location: properties.php');
    exit;
}

// Get settings
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$max_images = intval($settings['max_images_per_property'] ?? 8);
$max_video_size = intval($settings['max_video_size_mb'] ?? 100);
$enable_videos = ($settings['enable_video_uploads'] ?? 'true') === 'true';

// Get files for this property
$stmt = $db->prepare("SELECT * FROM file_uploads WHERE property_id = ? ORDER BY file_type, is_primary DESC, created_at ASC");
$stmt->execute([$property_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate images and videos
$images = array_filter($files, function($file) { return $file['file_type'] === 'image'; });
$videos = array_filter($files, function($file) { return $file['file_type'] === 'video'; });
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">Manage Files</h1>
                    <p class="text-gray-300 mt-2"><?php echo htmlspecialchars($property['title']); ?></p>
                </div>
                <a href="properties.php" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                    Back to Properties
                </a>
            </div>

            <!-- Upload Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Image Upload -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-4">Upload Images</h2>
                    <p class="text-gray-300 text-sm mb-4">
                        Upload up to <?php echo $max_images; ?> images (<?php echo count($images); ?>/<?php echo $max_images; ?> used)
                    </p>
                    
                    <form id="image-upload-form" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                        <input type="hidden" name="file_type" value="image">
                        
                        <div class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                            <input type="file" id="image-input" name="file" accept="image/*" multiple class="hidden">
                            <label for="image-input" class="cursor-pointer">
                                <i data-lucide="upload" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-gray-300">Click to select images</p>
                                <p class="text-gray-400 text-sm">JPG, PNG, GIF, WebP supported</p>
                            </label>
                        </div>
                        
                        <button type="submit" class="w-full btn-primary" <?php echo count($images) >= $max_images ? 'disabled' : ''; ?>>
                            <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                            Upload Images
                        </button>
                    </form>
                </div>

                <!-- Video Upload -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-4">Upload Video</h2>
                    <p class="text-gray-300 text-sm mb-4">
                        Upload 1 video (<?php echo count($videos); ?>/1 used)
                    </p>
                    
                    <?php if (!$enable_videos): ?>
                    <div class="bg-yellow-500/20 text-yellow-300 p-4 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-2"></i>
                        Video uploads are disabled in settings
                    </div>
                    <?php else: ?>
                    <form id="video-upload-form" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                        <input type="hidden" name="file_type" value="video">
                        
                        <div class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-purple-400 transition-colors">
                            <input type="file" id="video-input" name="file" accept="video/*" class="hidden">
                            <label for="video-input" class="cursor-pointer">
                                <i data-lucide="video" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-gray-300">Click to select video</p>
                                <p class="text-gray-400 text-sm">MP4, AVI, MOV, WMV, WebM supported</p>
                                <p class="text-gray-400 text-sm">Max size: <?php echo $max_video_size; ?>MB</p>
                            </label>
                        </div>
                        
                        <button type="submit" class="w-full btn-primary" <?php echo count($videos) >= 1 ? 'disabled' : ''; ?>>
                            <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                            Upload Video
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Current Files -->
            <div class="space-y-8">
                <!-- Images -->
                <?php if (!empty($images)): ?>
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Current Images (<?php echo count($images); ?>)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <?php foreach ($images as $image): ?>
                        <div class="relative group">
                            <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="" class="w-full h-32 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <div class="flex space-x-2">
                                    <a href="<?php echo htmlspecialchars($image['file_path']); ?>" target="_blank" class="p-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?delete_file=<?php echo $image['id']; ?>" onclick="return confirm('Delete this image?')" class="p-2 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                            <?php if ($image['is_primary']): ?>
                            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                                Primary
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Videos -->
                <?php if (!empty($videos)): ?>
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Current Videos (<?php echo count($videos); ?>)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($videos as $video): ?>
                        <div class="relative group">
                            <video class="w-full h-48 object-cover rounded-lg" controls>
                                <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="<?php echo htmlspecialchars($video['mime_type']); ?>">
                                Your browser does not support the video tag.
                            </video>
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <div class="flex space-x-2">
                                    <a href="<?php echo htmlspecialchars($video['file_path']); ?>" target="_blank" class="p-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?delete_file=<?php echo $video['id']; ?>" onclick="return confirm('Delete this video?')" class="p-2 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (empty($images) && empty($videos)): ?>
                <div class="text-center py-12">
                    <i data-lucide="folder-open" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-300 text-lg">No files uploaded yet</p>
                    <p class="text-gray-400">Upload images and videos to showcase this property</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Image upload
document.getElementById('image-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 mr-2 animate-spin"></i>Uploading...';
    
    fetch('upload-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="upload" class="w-5 h-5 mr-2"></i>Upload Images';
        }
    })
    .catch(error => {
        alert('Upload failed: ' + error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="upload" class="w-5 h-5 mr-2"></i>Upload Images';
    });
});

// Video upload
document.getElementById('video-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 mr-2 animate-spin"></i>Uploading...';
    
    fetch('upload-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="upload" class="w-5 h-5 mr-2"></i>Upload Video';
        }
    })
    .catch(error => {
        alert('Upload failed: ' + error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="upload" class="w-5 h-5 mr-2"></i>Upload Video';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
