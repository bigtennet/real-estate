<?php
ob_start();
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Mailing List - " . ($site_settings['site_name'] ?? 'Admin');
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_subscriber':
                $email = $_POST['email'] ?? '';
                $name = $_POST['name'] ?? '';
                if ($email) {
                    try {
                        $stmt = $db->prepare("INSERT INTO subscribers (email, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
                        $stmt->execute([$email, $name]);
                        $success = "Subscriber added successfully!";
                    } catch (Exception $e) {
                        $error = "Error adding subscriber: " . $e->getMessage();
                    }
                }
                break;
                
            case 'send_campaign':
                $subject = $_POST['subject'] ?? '';
                $content = $_POST['content'] ?? '';
                $property_ids = $_POST['property_ids'] ?? [];
                
                if ($subject && $content) {
                    try {
                        // Create campaign
                        $stmt = $db->prepare("INSERT INTO email_campaigns (subject, content, property_ids, total_count) VALUES (?, ?, ?, ?)");
                        $property_ids_json = json_encode($property_ids);
                        $total_count = $db->query("SELECT COUNT(*) FROM subscribers WHERE status = 'active'")->fetchColumn();
                        $stmt->execute([$subject, $content, $property_ids_json, $total_count]);
                        $campaign_id = $db->lastInsertId();
                        
                        $success = "Campaign created successfully! Campaign ID: " . $campaign_id;
                    } catch (Exception $e) {
                        $error = "Error creating campaign: " . $e->getMessage();
                    }
                }
                break;
                
            case 'update_smtp':
                $smtp_settings = [
                    'smtp_host' => $_POST['smtp_host'] ?? '',
                    'smtp_port' => $_POST['smtp_port'] ?? '587',
                    'smtp_username' => $_POST['smtp_username'] ?? '',
                    'smtp_password' => $_POST['smtp_password'] ?? '',
                    'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls',
                    'smtp_from_email' => $_POST['smtp_from_email'] ?? '',
                    'smtp_from_name' => $_POST['smtp_from_name'] ?? '',
                    'smtp_enabled' => isset($_POST['smtp_enabled']) ? '1' : '0'
                ];
                
                try {
                    foreach ($smtp_settings as $key => $value) {
                        $stmt = $db->prepare("INSERT INTO smtp_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
                        $stmt->execute([$key, $value]);
                    }
                    $success = "SMTP settings updated successfully!";
                } catch (Exception $e) {
                    $error = "Error updating SMTP settings: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get subscribers
$stmt = $db->query("SELECT * FROM subscribers ORDER BY created_at DESC");
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get properties for campaign selection
$stmt = $db->query("SELECT id, title, price, location FROM properties WHERE status = 'AVAILABLE' ORDER BY created_at DESC");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get SMTP settings
$stmt = $db->query("SELECT setting_key, setting_value FROM smtp_settings");
$smtp_settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $smtp_settings[$row['setting_key']] = $row['setting_value'];
}

// Get campaigns
$stmt = $db->query("SELECT * FROM email_campaigns ORDER BY created_at DESC LIMIT 10");
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="glass-card p-8 rounded-2xl mb-8">
            <h1 class="text-3xl font-bold text-white mb-4">Mailing List Management</h1>
            <p class="text-gray-300">Manage your subscribers and send email campaigns to promote your properties.</p>
        </div>

        <?php if (isset($success)): ?>
        <div class="glass-card p-4 rounded-xl mb-6 bg-green-500/20 border border-green-500/30">
            <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="glass-card p-4 rounded-xl mb-6 bg-red-500/20 border border-red-500/30">
            <p class="text-red-300"><?php echo htmlspecialchars($error); ?></p>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Subscribers Management -->
            <div class="glass-card p-6 rounded-xl">
                <h2 class="text-xl font-bold text-white mb-6">Subscribers (<?php echo count($subscribers); ?>)</h2>
                
                <!-- Add Subscriber Form -->
                <form method="POST" class="mb-6">
                    <input type="hidden" name="action" value="add_subscriber">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="subscriber@example.com">
                        </div>
                        <div>
                            <label class="block text-white font-semibold mb-2">Name (Optional)</label>
                            <input type="text" name="name" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="John Doe">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Subscriber
                    </button>
                </form>

                <!-- Subscribers List -->
                <div class="max-h-96 overflow-y-auto">
                    <div class="space-y-2">
                        <?php foreach ($subscribers as $subscriber): ?>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <div>
                                <p class="text-white font-medium"><?php echo htmlspecialchars($subscriber['email']); ?></p>
                                <?php if ($subscriber['name']): ?>
                                <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($subscriber['name']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $subscriber['status'] === 'active' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'; ?>">
                                    <?php echo ucfirst($subscriber['status']); ?>
                                </span>
                                <span class="text-gray-400 text-xs">
                                    <?php echo date('M j, Y', strtotime($subscriber['subscribed_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Email Campaign -->
            <div class="glass-card p-6 rounded-xl">
                <h2 class="text-xl font-bold text-white mb-6">Send Campaign</h2>
                
                <form method="POST">
                    <input type="hidden" name="action" value="send_campaign">
                    
                    <div class="mb-4">
                        <label class="block text-white font-semibold mb-2">Subject</label>
                        <input type="text" name="subject" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="New Properties Available!">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-white font-semibold mb-2">Content</label>
                        <textarea name="content" rows="6" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Write your email content here..."></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Include Properties</label>
                        <div class="max-h-32 overflow-y-auto space-y-2">
                            <?php foreach ($properties as $property): ?>
                            <label class="flex items-center space-x-2 text-white">
                                <input type="checkbox" name="property_ids[]" value="<?php echo $property['id']; ?>" class="rounded">
                                <span class="text-sm"><?php echo htmlspecialchars($property['title']); ?> - ₦<?php echo number_format($property['price']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        Create Campaign
                    </button>
                </form>
            </div>
        </div>

        <!-- SMTP Settings -->
        <div class="glass-card p-6 rounded-xl mt-8">
            <h2 class="text-xl font-bold text-white mb-6">SMTP Settings</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_smtp">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($smtp_settings['smtp_host'] ?? ''); ?>" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="smtp.gmail.com">
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">SMTP Port</label>
                        <input type="number" name="smtp_port" value="<?php echo htmlspecialchars($smtp_settings['smtp_port'] ?? '587'); ?>" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="587">
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Username</label>
                        <input type="text" name="smtp_username" value="<?php echo htmlspecialchars($smtp_settings['smtp_username'] ?? ''); ?>" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="your-email@gmail.com">
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Password</label>
                        <input type="password" name="smtp_password" value="<?php echo htmlspecialchars($smtp_settings['smtp_password'] ?? ''); ?>" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your email password">
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Encryption</label>
                        <select name="smtp_encryption" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="tls" <?php echo ($smtp_settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo ($smtp_settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="none" <?php echo ($smtp_settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">From Email</label>
                        <input type="email" name="smtp_from_email" value="<?php echo htmlspecialchars($smtp_settings['smtp_from_email'] ?? ''); ?>" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="noreply@yourdomain.com">
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">From Name</label>
                        <input type="text" name="smtp_from_name" value="<?php echo htmlspecialchars($smtp_settings['smtp_from_name'] ?? ''); ?>" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Premium Real Estate">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-2 text-white">
                            <input type="checkbox" name="smtp_enabled" <?php echo ($smtp_settings['smtp_enabled'] ?? '0') === '1' ? 'checked' : ''; ?> class="rounded">
                            <span>Enable SMTP</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary mt-6">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Save SMTP Settings
                </button>
            </form>
        </div>

        <!-- Recent Campaigns -->
        <?php if (!empty($campaigns)): ?>
        <div class="glass-card p-6 rounded-xl mt-8">
            <h2 class="text-xl font-bold text-white mb-6">Recent Campaigns</h2>
            
            <div class="space-y-4">
                <?php foreach ($campaigns as $campaign): ?>
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                    <div>
                        <h3 class="text-white font-medium"><?php echo htmlspecialchars($campaign['subject']); ?></h3>
                        <p class="text-gray-400 text-sm">Sent: <?php echo $campaign['sent_count']; ?>/<?php echo $campaign['total_count']; ?> subscribers</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $campaign['status'] === 'sent' ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-300'; ?>">
                            <?php echo ucfirst($campaign['status']); ?>
                        </span>
                        <span class="text-gray-400 text-xs">
                            <?php echo date('M j, Y H:i', strtotime($campaign['created_at'])); ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
