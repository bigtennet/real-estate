<?php
ob_start();

// Get site settings first
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'includes/functions.php';
$site_settings = getSiteSettings($db);

$page_title = ($site_settings['site_name'] ?? 'Premium Real Estate') . " - Find Your Dream Home";
$page_description = $site_settings['site_description'] ?? 'Discover luxury properties and find your perfect home with our premium real estate listings.';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Hero Background Image -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1920&h=1080&fit=crop" 
             alt="Luxury Real Estate" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 via-purple-900/60 to-slate-900/80"></div>
    </div>

    <!-- Floating elements -->
    <div class="absolute top-20 left-10 w-20 h-20 glass rounded-full animate-float"></div>
    <div class="absolute top-40 right-20 w-16 h-16 glass rounded-full animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-40 left-20 w-12 h-12 glass rounded-full animate-float" style="animation-delay: 4s;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="animate-fade-in">
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                <span class="gradient-text">Find Your</span>
                <br>
                <span class="text-white">Dream Home</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                Discover luxury properties and premium real estate listings in the most desirable locations
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-4xl mx-auto mb-8 animate-slide-up" style="animation-delay: 0.2s;">
            <div class="glass p-6 rounded-2xl">
                <form action="properties.php" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i data-lucide="search" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by location, property type..."
                            class="w-full pl-12 pr-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div class="flex-1 relative">
                        <i data-lucide="map-pin" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                        <select name="location" class="w-full pl-12 pr-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Location</option>
                            <option value="downtown">Downtown</option>
                            <option value="suburbs">Suburbs</option>
                            <option value="beach">Beach Area</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary flex items-center justify-center">
                        <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
                        Search
                    </button>
                </form>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up" style="animation-delay: 0.4s;">
            <a href="properties.php" class="btn-primary">Browse Properties</a>
            <a href="contact.php" class="glass-card px-8 py-4 rounded-xl text-white hover:bg-white/20 transition-all">Get in Touch</a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="home" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-bold gradient-text mb-2">500+</div>
                    <div class="text-gray-300 text-lg">Properties Sold</div>
                </div>
            </div>
            <div class="text-center">
                <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="users" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-bold gradient-text mb-2">1000+</div>
                    <div class="text-gray-300 text-lg">Happy Clients</div>
                </div>
            </div>
            <div class="text-center">
                <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="award" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-bold gradient-text mb-2">15+</div>
                    <div class="text-gray-300 text-lg">Years Experience</div>
                </div>
            </div>
            <div class="text-center">
                <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="star" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-bold gradient-text mb-2">4.9</div>
                    <div class="text-gray-300 text-lg">Average Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Property Types -->
<section class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Property <span class="gradient-text">Types</span>
            </h2>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Explore our diverse range of premium properties tailored to your lifestyle
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300 cursor-pointer group">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="home" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Houses</h3>
                <div class="text-3xl font-bold gradient-text mb-2">120+</div>
                <p class="text-gray-300">Single family homes</p>
            </div>
            <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300 cursor-pointer group">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="building" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Apartments</h3>
                <div class="text-3xl font-bold gradient-text mb-2">80+</div>
                <p class="text-gray-300">Modern apartments</p>
            </div>
            <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300 cursor-pointer group">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="tree-pine" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Villas</h3>
                <div class="text-3xl font-bold gradient-text mb-2">45+</div>
                <p class="text-gray-300">Luxury villas</p>
            </div>
            <div class="glass-card p-8 rounded-2xl hover:bg-white/10 transition-all duration-300 cursor-pointer group">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="store" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Commercial</h3>
                <div class="text-3xl font-bold gradient-text mb-2">60+</div>
                <p class="text-gray-300">Business properties</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Featured <span class="gradient-text">Properties</span>
            </h2>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Discover our handpicked selection of premium properties
            </p>
        </div>

        <?php
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT p.* FROM properties p 
                  WHERE p.status = 'AVAILABLE' 
                  ORDER BY p.created_at DESC 
                  LIMIT 3";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get settings for WhatsApp
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($properties as $property): ?>
            <div class="glass-card rounded-2xl overflow-hidden hover:bg-white/10 transition-all duration-300 group">
                <div class="relative h-64 overflow-hidden">
                    <?php 
                    $images = json_decode($property['images'], true) ?? [];
                    $main_image = $images[0] ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop';
                    ?>
                    <img
                        src="<?php echo htmlspecialchars($main_image); ?>"
                        alt="<?php echo htmlspecialchars($property['title']); ?>"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    />
                    <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        Available
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($property['title']); ?></h3>
                    <div class="flex items-center text-gray-300 mb-4">
                        <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                        <?php echo htmlspecialchars($property['location']); ?>
                    </div>
                    
                    <div class="flex items-center justify-between text-gray-300 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="bed" class="w-4 h-4 mr-1"></i>
                            <?php echo $property['bedrooms']; ?>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="bath" class="w-4 h-4 mr-1"></i>
                            <?php echo $property['bathrooms']; ?>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="square" class="w-4 h-4 mr-1"></i>
                            <?php echo number_format($property['area']); ?> sq ft
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php 
                        $features = json_decode($property['features'], true);
                        foreach(array_slice($features, 0, 3) as $feature): 
                        ?>
                        <span class="bg-white/10 text-white px-2 py-1 rounded-full text-sm">
                            <?php echo htmlspecialchars($feature); ?>
                        </span>
                        <?php endforeach; ?>
                        <?php if(count($features) > 3): ?>
                        <span class="bg-white/10 text-white px-2 py-1 rounded-full text-sm">
                            +<?php echo count($features) - 3; ?> more
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold gradient-text">
                            ₦<?php echo number_format($property['price']); ?>
                        </div>
                        <button 
                            onclick="openWhatsApp('<?php echo htmlspecialchars($settings['whatsapp_number'] ?? '+2341234567890'); ?>', '<?php echo htmlspecialchars($property['title']); ?>', '<?php echo htmlspecialchars($property['location']); ?>')"
                            class="btn-whatsapp flex items-center"
                        >
                            <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                            Contact
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="properties.php" class="btn-primary">View All Properties</a>
        </div>
    </div>
</section>

<script>
function openWhatsApp(whatsapp, title, location) {
    const message = `Hi! I'm interested in the property "${title}" at ${location}. Could you provide more details?`;
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${whatsapp.replace(/\D/g, '')}?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
