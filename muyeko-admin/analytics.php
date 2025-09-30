<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Analytics - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [];

// Total properties
$stmt = $db->query("SELECT COUNT(*) as total FROM properties");
$stats['total_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Available properties
$stmt = $db->query("SELECT COUNT(*) as total FROM properties WHERE status = 'AVAILABLE'");
$stats['available_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Sold properties
$stmt = $db->query("SELECT COUNT(*) as total FROM properties WHERE status = 'SOLD'");
$stats['sold_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total files
$stmt = $db->query("SELECT COUNT(*) as total FROM file_uploads");
$stats['total_files'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Images count
$stmt = $db->query("SELECT COUNT(*) as total FROM file_uploads WHERE file_type = 'image'");
$stats['image_files'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Videos count
$stmt = $db->query("SELECT COUNT(*) as total FROM file_uploads WHERE file_type = 'video'");
$stats['video_files'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Properties by type
$stmt = $db->query("SELECT type, COUNT(*) as count FROM properties GROUP BY type ORDER BY count DESC");
$properties_by_type = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent activity
$stmt = $db->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 10");
$recent_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <h1 class="text-3xl font-bold text-white mb-8">Analytics Dashboard</h1>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="home" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-green-400 text-sm font-semibold">+12%</span>
                    </div>
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['total_properties']; ?></div>
                    <div class="text-gray-400 text-sm">Total Properties</div>
                </div>
                
                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-green-400 text-sm font-semibold">+8%</span>
                    </div>
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['available_properties']; ?></div>
                    <div class="text-gray-400 text-sm">Available Properties</div>
                </div>
                
                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-green-400 text-sm font-semibold">+15%</span>
                    </div>
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['sold_properties']; ?></div>
                    <div class="text-gray-400 text-sm">Properties Sold</div>
                </div>
                
                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="folder" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-green-400 text-sm font-semibold">+23%</span>
                    </div>
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['total_files']; ?></div>
                    <div class="text-gray-400 text-sm">Total Files</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Properties by Type -->
                <div class="glass-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-white mb-6">Properties by Type</h3>
                    <div class="space-y-4">
                        <?php foreach($properties_by_type as $type): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300"><?php echo $type['type']; ?></span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-700 rounded-full h-2 mr-3">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo ($type['count'] / $stats['total_properties']) * 100; ?>%"></div>
                                </div>
                                <span class="text-white font-semibold"><?php echo $type['count']; ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- File Statistics -->
                <div class="glass-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-white mb-6">File Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Images</span>
                            <span class="text-blue-300 font-semibold"><?php echo $stats['image_files']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Videos</span>
                            <span class="text-purple-300 font-semibold"><?php echo $stats['video_files']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Total Files</span>
                            <span class="text-white font-semibold"><?php echo $stats['total_files']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Properties -->
            <div class="glass-card p-6 rounded-xl">
                <h3 class="text-xl font-bold text-white mb-6">Recent Properties</h3>
                <div class="space-y-4">
                    <?php foreach($recent_properties as $property): ?>
                    <div class="flex items-center justify-between py-3 border-b border-white/10">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                            <span class="text-gray-300"><?php echo htmlspecialchars($property['title']); ?></span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-400 text-sm">₦<?php echo number_format($property['price']); ?></span>
                            <span class="text-gray-400 text-sm"><?php echo date('M j, Y', strtotime($property['created_at'])); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
