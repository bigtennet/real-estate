<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Site Settings - " . ($site_settings['site_name'] ?? 'Admin');
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Handle form submission
if ($_POST) {
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
    }
    $success_message = "Settings updated successfully!";
}

// Get all settings
$stmt = $db->query("SELECT * FROM site_settings ORDER BY setting_key");
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to associative array for easier access
$settings_array = [];
foreach ($settings as $setting) {
    $settings_array[$setting['setting_key']] = $setting;
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <h1 class="text-3xl font-bold text-white mb-8">Site Settings</h1>

            <?php if(isset($success_message)): ?>
            <div class="bg-green-500/20 text-green-300 p-4 rounded-lg mb-6">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <!-- General Settings -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">General Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Site Name</label>
                            <input
                                type="text"
                                name="site_name"
                                value="<?php echo htmlspecialchars($settings_array['site_name']['setting_value'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['site_name']['description'] ?? ''; ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Site Description</label>
                            <textarea
                                name="site_description"
                                rows="3"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ><?php echo htmlspecialchars($settings_array['site_description']['setting_value'] ?? ''); ?></textarea>
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['site_description']['description'] ?? ''; ?></p>
                        </div>
                    </div>
                    
                    <!-- Logo Upload -->
                    <div class="mt-6">
                        <label class="block text-white font-semibold mb-2">Site Logo</label>
                        <div class="flex items-center space-x-4">
                            <?php if (isset($settings_array['site_logo']['setting_value']) && !empty($settings_array['site_logo']['setting_value'])): ?>
                            <img src="<?php echo htmlspecialchars($settings_array['site_logo']['setting_value']); ?>" alt="Current Logo" class="w-16 h-16 object-contain bg-white/10 rounded-lg">
                            <?php endif; ?>
                            <div class="flex-1">
                                <input
                                    type="url"
                                    name="site_logo"
                                    value="<?php echo htmlspecialchars($settings_array['site_logo']['setting_value'] ?? ''); ?>"
                                    placeholder="Logo URL or upload file"
                                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <p class="text-gray-400 text-sm mt-1">Enter logo URL or upload a file</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Contact Phone</label>
                            <input
                                type="text"
                                name="contact_phone"
                                value="<?php echo htmlspecialchars($settings_array['contact_phone']['setting_value'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['contact_phone']['description'] ?? ''; ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Contact Email</label>
                            <input
                                type="email"
                                name="contact_email"
                                value="<?php echo htmlspecialchars($settings_array['contact_email']['setting_value'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['contact_email']['description'] ?? ''; ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Contact Address</label>
                            <textarea
                                name="contact_address"
                                rows="2"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ><?php echo htmlspecialchars($settings_array['contact_address']['setting_value'] ?? ''); ?></textarea>
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['contact_address']['description'] ?? ''; ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">WhatsApp Number</label>
                            <input
                                type="text"
                                name="whatsapp_number"
                                value="<?php echo htmlspecialchars($settings_array['whatsapp_number']['setting_value'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['whatsapp_number']['description'] ?? ''; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Display Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Properties Per Page</label>
                            <input
                                type="number"
                                name="properties_per_page"
                                value="<?php echo htmlspecialchars($settings_array['properties_per_page']['setting_value'] ?? '12'); ?>"
                                min="1"
                                max="50"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['properties_per_page']['description'] ?? ''; ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Max Images Per Property</label>
                            <input
                                type="number"
                                name="max_images_per_property"
                                value="<?php echo htmlspecialchars($settings_array['max_images_per_property']['setting_value'] ?? '8'); ?>"
                                min="1"
                                max="20"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['max_images_per_property']['description'] ?? ''; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Upload Settings -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Upload Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Enable Video Uploads</label>
                            <select
                                name="enable_video_uploads"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="true" <?php echo ($settings_array['enable_video_uploads']['setting_value'] ?? 'true') === 'true' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="false" <?php echo ($settings_array['enable_video_uploads']['setting_value'] ?? 'true') === 'false' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['enable_video_uploads']['description'] ?? ''; ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Max Video Size (MB)</label>
                            <input
                                type="number"
                                name="max_video_size_mb"
                                value="<?php echo htmlspecialchars($settings_array['max_video_size_mb']['setting_value'] ?? '100'); ?>"
                                min="1"
                                max="1000"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1"><?php echo $settings_array['max_video_size_mb']['description'] ?? ''; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Social Media Settings -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Social Media</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Facebook URL</label>
                            <input
                                type="url"
                                name="facebook_url"
                                value="<?php echo htmlspecialchars($settings_array['facebook_url']['setting_value'] ?? ''); ?>"
                                placeholder="https://facebook.com/yourpage"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1">Your Facebook page URL</p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Twitter URL</label>
                            <input
                                type="url"
                                name="twitter_url"
                                value="<?php echo htmlspecialchars($settings_array['twitter_url']['setting_value'] ?? ''); ?>"
                                placeholder="https://twitter.com/yourhandle"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1">Your Twitter profile URL</p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Instagram URL</label>
                            <input
                                type="url"
                                name="instagram_url"
                                value="<?php echo htmlspecialchars($settings_array['instagram_url']['setting_value'] ?? ''); ?>"
                                placeholder="https://instagram.com/yourhandle"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1">Your Instagram profile URL</p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">LinkedIn URL</label>
                            <input
                                type="url"
                                name="linkedin_url"
                                value="<?php echo htmlspecialchars($settings_array['linkedin_url']['setting_value'] ?? ''); ?>"
                                placeholder="https://linkedin.com/company/yourcompany"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1">Your LinkedIn company page URL</p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">YouTube URL</label>
                            <input
                                type="url"
                                name="youtube_url"
                                value="<?php echo htmlspecialchars($settings_array['youtube_url']['setting_value'] ?? ''); ?>"
                                placeholder="https://youtube.com/c/yourchannel"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1">Your YouTube channel URL</p>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">WhatsApp Business</label>
                            <input
                                type="text"
                                name="whatsapp_business"
                                value="<?php echo htmlspecialchars($settings_array['whatsapp_business']['setting_value'] ?? ''); ?>"
                                placeholder="+2341234567890"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p class="text-gray-400 text-sm mt-1">WhatsApp Business number</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="submit" class="btn-primary">
                        <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
