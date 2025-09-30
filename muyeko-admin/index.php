<?php
ob_start();
session_start();

// Simple authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Admin Dashboard - " . ($site_settings['site_name'] ?? 'Premium Real Estate');
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Get stats
$stats = [];

// Total properties
$stmt = $db->query("SELECT COUNT(*) as total FROM properties");
$stats['total_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Available properties
$stmt = $db->query("SELECT COUNT(*) as total FROM properties WHERE status = 'AVAILABLE'");
$stats['available_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total properties by status
$stmt = $db->query("SELECT COUNT(*) as total FROM properties WHERE status = 'SOLD'");
$stats['sold_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent properties
$stmt = $db->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 5");
$recent_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">
                    Admin <span class="gradient-text">Dashboard</span>
                </h1>
                <p class="text-gray-300">Welcome back, Prince Ademuyiwa Edward Ojo</p>
                <p class="text-sm text-gray-400">Senior Real Estate Consultant • <?php echo date('F j, Y'); ?></p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
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
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
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
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <a href="properties.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="home" class="w-8 h-8 text-blue-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Manage Properties</h3>
                    <p class="text-gray-400 text-sm">View and edit properties</p>
                </a>
                
                <a href="add-property.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="plus" class="w-8 h-8 text-green-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Add Property</h3>
                    <p class="text-gray-400 text-sm">Create new property listing</p>
                </a>
                
                <a href="manage-files.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="folder" class="w-8 h-8 text-purple-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">File Manager</h3>
                    <p class="text-gray-400 text-sm">Manage uploaded files</p>
                </a>
                
                <a href="settings.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="settings" class="w-8 h-8 text-orange-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Settings</h3>
                    <p class="text-gray-400 text-sm">System configuration</p>
                </a>
            </div>

            <!-- Additional Management -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <a href="../properties.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="eye" class="w-8 h-8 text-cyan-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">View Public Site</h3>
                    <p class="text-gray-400 text-sm">See how properties appear to visitors</p>
                </a>
                
                <a href="file-manager.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="folder" class="w-8 h-8 text-pink-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">File Manager</h3>
                    <p class="text-gray-400 text-sm">Manage uploaded files</p>
                </a>
                
                <a href="analytics.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="bar-chart" class="w-8 h-8 text-yellow-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Analytics</h3>
                    <p class="text-gray-400 text-sm">View site statistics</p>
                </a>
                
                <a href="about-content.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="file-text" class="w-8 h-8 text-indigo-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">About Content</h3>
                    <p class="text-gray-400 text-sm">Manage about page content</p>
                </a>
            </div>
            
            <!-- Profile Management -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <a href="realtor-profile.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="user" class="w-8 h-8 text-emerald-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Realtor Profile</h3>
                    <p class="text-gray-400 text-sm">Manage Prince Ademuyiwa Edward Ojo's profile</p>
                </a>
                
                <a href="mailing-list.php" class="glass-card p-6 rounded-xl hover:bg-white/10 transition-all duration-300 text-center">
                    <i data-lucide="mail" class="w-8 h-8 text-pink-400 mx-auto mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Mailing List</h3>
                    <p class="text-gray-400 text-sm">Manage subscribers & send campaigns</p>
                </a>
            </div>

            <!-- Recent Properties -->
            <div class="glass-card p-6 rounded-xl">
                <h3 class="text-xl font-bold text-white mb-4">Recent Properties</h3>
                <div class="space-y-4">
                    <?php foreach($recent_properties as $property): ?>
                    <div class="flex items-center justify-between py-3 border-b border-white/10">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                            <span class="text-gray-300"><?php echo htmlspecialchars($property['title']); ?></span>
                        </div>
                        <span class="text-gray-400 text-sm"><?php echo date('M j, Y', strtotime($property['created_at'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
