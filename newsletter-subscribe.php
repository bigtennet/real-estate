<?php
ob_start();

// Get site settings first
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'includes/functions.php';
$site_settings = getSiteSettings($db);

$page_title = "Newsletter Subscription - " . ($site_settings['site_name'] ?? 'Premium Real Estate');
$page_description = "Subscribe to our newsletter for the latest property listings.";
include 'includes/header.php';

$message = '';
$message_type = '';

if ($_POST && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM subscribers WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $message = "You're already subscribed to our newsletter!";
                $message_type = 'info';
            } else {
                // Add new subscriber
                $stmt = $db->prepare("INSERT INTO subscribers (email, status) VALUES (?, 'active')");
                $stmt->execute([$email]);
                
                $message = "Thank you for subscribing to our newsletter! You'll receive updates about our latest properties.";
                $message_type = 'success';
            }
        } catch (Exception $e) {
            $message = "Sorry, there was an error processing your subscription. Please try again later.";
            $message_type = 'error';
        }
    } else {
        $message = "Please enter a valid email address.";
        $message_type = 'error';
    }
} else {
    // Redirect if no POST data
    header('Location: index.php');
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="glass-card p-8 rounded-2xl text-center">
            <div class="mb-8">
                <?php if ($message_type === 'success'): ?>
                <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check" class="w-8 h-8 text-green-400"></i>
                </div>
                <?php elseif ($message_type === 'error'): ?>
                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="x" class="w-8 h-8 text-red-400"></i>
                </div>
                <?php else: ?>
                <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="info" class="w-8 h-8 text-blue-400"></i>
                </div>
                <?php endif; ?>
                
                <h1 class="text-3xl font-bold text-white mb-4">
                    <?php echo $message_type === 'success' ? 'Subscription Successful!' : ($message_type === 'error' ? 'Subscription Error' : 'Already Subscribed'); ?>
                </h1>
                
                <p class="text-gray-300 text-lg mb-8">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            </div>
            
            <div class="space-y-4">
                <a href="index.php" class="btn-primary inline-flex items-center">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                    Back to Home
                </a>
                
                <a href="properties.php" class="btn-whatsapp inline-flex items-center ml-4">
                    <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                    Browse Properties
                </a>
            </div>
            
            <?php if ($message_type === 'success'): ?>
            <div class="mt-8 p-6 bg-blue-500/10 rounded-xl border border-blue-500/20">
                <h3 class="text-white font-semibold mb-2">What to expect:</h3>
                <ul class="text-gray-300 text-sm space-y-1">
                    <li>• Weekly property updates</li>
                    <li>• Exclusive listings before they go public</li>
                    <li>• Market insights and trends</li>
                    <li>• Special offers and promotions</li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
