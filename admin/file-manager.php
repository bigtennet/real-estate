<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "File Manager - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Get all files with property information
$query = "SELECT f.*, p.title as property_title FROM file_uploads f 
          LEFT JOIN properties p ON f.property_id = p.id 
          ORDER BY f.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle file deletion
if (isset($_GET['delete'])) {
    $file_id = $_GET['delete'];
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
    
    header('Location: file-manager.php');
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white">File Manager</h1>
                <a href="properties.php" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                    Back to Properties
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-4 px-2">File</th>
                            <th class="text-left py-4 px-2">Type</th>
                            <th class="text-left py-4 px-2">Size</th>
                            <th class="text-left py-4 px-2">Property</th>
                            <th class="text-left py-4 px-2">Uploaded</th>
                            <th class="text-left py-4 px-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($files as $file): ?>
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="py-4 px-2">
                                <div class="flex items-center">
                                    <?php if ($file['file_type'] === 'image'): ?>
                                    <img src="<?php echo htmlspecialchars($file['file_path']); ?>" alt="" class="w-12 h-12 rounded-lg object-cover mr-3">
                                    <?php else: ?>
                                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mr-3">
                                        <i data-lucide="video" class="w-6 h-6 text-purple-300"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($file['file_name']); ?></div>
                                        <div class="text-gray-400 text-sm"><?php echo htmlspecialchars($file['mime_type']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-2">
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $file['file_type'] === 'image' ? 'bg-blue-500/20 text-blue-300' : 'bg-purple-500/20 text-purple-300'; ?>">
                                    <?php echo ucfirst($file['file_type']); ?>
                                </span>
                            </td>
                            <td class="py-4 px-2"><?php echo number_format($file['file_size'] / 1024, 1); ?> KB</td>
                            <td class="py-4 px-2">
                                <?php if ($file['property_title']): ?>
                                <a href="property-detail.php?id=<?php echo $file['property_id']; ?>" class="text-blue-300 hover:text-blue-200">
                                    <?php echo htmlspecialchars($file['property_title']); ?>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-400">No property</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-2"><?php echo date('M j, Y', strtotime($file['created_at'])); ?></td>
                            <td class="py-4 px-2">
                                <div class="flex space-x-2">
                                    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" class="p-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors" title="View">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?delete=<?php echo $file['id']; ?>" onclick="return confirm('Delete this file?')" class="p-2 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($files)): ?>
            <div class="text-center py-12">
                <i data-lucide="folder-open" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-300 text-lg">No files uploaded yet</p>
                <p class="text-gray-400">Upload files when adding or editing properties</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
