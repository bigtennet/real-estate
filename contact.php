<?php
ob_start();

// Get site settings first
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'includes/functions.php';
$site_settings = getSiteSettings($db);

$page_title = "Contact Us - " . ($site_settings['site_name'] ?? 'Premium Real Estate');
$page_description = "Get in touch with our real estate experts. We're here to help you find your dream property.";
include 'includes/header.php';

// Handle form submission
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Here you would typically save to database or send email
    $success_message = "Thank you for your message! We will get back to you soon.";
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Get In <span class="gradient-text">Touch</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Ready to find your dream property? Contact us today and let our experts help you every step of the way.
            </p>
        </div>

        <?php if(isset($success_message)): ?>
        <div class="glass-card p-4 rounded-xl mb-8 text-center">
            <div class="text-green-400 font-semibold"><?php echo $success_message; ?></div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="glass-card p-8 rounded-2xl">
                <h2 class="text-2xl font-bold text-white mb-6">Send us a Message</h2>
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Full Name</label>
                            <input
                                type="text"
                                name="name"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Your full name"
                            />
                        </div>
                        <div>
                            <label class="block text-white font-semibold mb-2">Email</label>
                            <input
                                type="email"
                                name="email"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="your@email.com"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Phone</label>
                            <input
                                type="tel"
                                name="phone"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Your phone number"
                            />
                        </div>
                        <div>
                            <label class="block text-white font-semibold mb-2">Subject</label>
                            <select
                                name="subject"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Select a subject</option>
                                <option value="buying">I want to buy a property</option>
                                <option value="selling">I want to sell my property</option>
                                <option value="renting">I want to rent a property</option>
                                <option value="investment">Real estate investment advice</option>
                                <option value="general">General inquiry</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-white font-semibold mb-2">Message</label>
                        <textarea
                            name="message"
                            rows="5"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Tell us about your real estate needs..."
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full btn-primary flex items-center justify-center"
                    >
                        <i data-lucide="send" class="w-5 h-5 mr-2"></i>
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <div class="glass-card p-8 rounded-2xl">
                    <h2 class="text-2xl font-bold text-white mb-6">Contact Information</h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="phone" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Phone</h3>
                                <p class="text-blue-400 font-medium mb-1"><?php echo htmlspecialchars($site_settings['contact_phone'] ?? '+234 (0) 123-456-7890'); ?></p>
                                <p class="text-gray-300 text-sm">Call us for immediate assistance</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Email</h3>
                                <p class="text-blue-400 font-medium mb-1"><?php echo htmlspecialchars($site_settings['contact_email'] ?? 'info@premiumrealestate.ng'); ?></p>
                                <p class="text-gray-300 text-sm">Send us an email anytime</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Office</h3>
                                <p class="text-blue-400 font-medium mb-1"><?php echo htmlspecialchars($site_settings['contact_address'] ?? '123 Victoria Island, Lagos, Nigeria'); ?></p>
                                <p class="text-gray-300 text-sm">Visit our office location</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Hours</h3>
                                <p class="text-blue-400 font-medium mb-1">Mon - Fri: 9AM - 6PM</p>
                                <p class="text-gray-300 text-sm">Saturday: 10AM - 4PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Contact -->
                <div class="glass-card p-6 rounded-2xl text-center">
                    <h3 class="text-xl font-bold text-white mb-4">Quick Contact</h3>
                    <p class="text-gray-300 mb-6">
                        For immediate assistance, contact us via WhatsApp
                    </p>
                    <button
                        onclick="openWhatsApp('+1234567890', 'General Inquiry', 'Premium Real Estate')"
                        class="btn-whatsapp flex items-center justify-center mx-auto"
                    >
                        <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                        Chat on WhatsApp
                    </button>
                </div>

                <!-- Map Placeholder -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-xl font-bold text-white mb-4">Visit Our Office</h3>
                    <div class="w-full h-48 bg-gradient-to-br from-blue-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <i data-lucide="map-pin" class="w-12 h-12 text-blue-400 mx-auto mb-2"></i>
                            <p class="text-gray-300">Interactive map would be here</p>
                            <p class="text-sm text-gray-400">123 Main Street, City, State 12345</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openWhatsApp(whatsapp, title, location) {
    const message = `Hi! I'm interested in your real estate services. Could you provide more information?`;
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${whatsapp.replace(/\D/g, '')}?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
