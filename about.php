<?php
ob_start();

// Get site settings first
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'includes/functions.php';
$site_settings = getSiteSettings($db);

$page_title = "About Us - " . ($site_settings['site_name'] ?? 'Premium Real Estate');
$page_description = $site_settings['site_description'] ?? 'Learn about our company, team, and commitment to excellence in real estate.';
include 'includes/header.php';

$stmt = $db->query("SELECT * FROM about_content ORDER BY display_order ASC");
$about_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get realtor profile
$stmt = $db->query("SELECT * FROM realtor_profile LIMIT 1");
$realtor = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <?php 
            $hero_section = array_filter($about_sections, function($section) { return $section['section_key'] === 'hero'; });
            $hero = reset($hero_section);
            ?>
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                <?php echo htmlspecialchars($hero['title'] ?? 'About Premium Real Estate'); ?>
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                <?php echo htmlspecialchars($hero['content'] ?? 'We are passionate about helping you find the perfect property and making your real estate dreams come true.'); ?>
            </p>
        </div>

        <!-- Company Story -->
        <div class="glass-card p-8 rounded-2xl mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-6">Our Story</h2>
                    <div class="space-y-4 text-gray-300">
                        <p>
                            Founded in 2010, Premium Real Estate has been at the forefront of luxury property sales and rentals. 
                            What started as a small team with big dreams has grown into one of the most trusted names in real estate.
                        </p>
                        <p>
                            Our journey began with a simple mission: to make real estate transactions seamless, transparent, and 
                            rewarding for all parties involved. Over the years, we've helped thousands of families find their 
                            dream homes and investors build their portfolios.
                        </p>
                        <p>
                            Today, we continue to innovate and adapt to the changing real estate landscape, always putting our 
                            clients' needs first and maintaining the highest standards of service excellence.
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&h=400&fit=crop"
                        alt="Our office"
                        class="rounded-2xl w-full h-80 object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent rounded-2xl"></div>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-white text-center mb-12">Our Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="glass-card p-6 rounded-xl text-center hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="heart" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Client-First Approach</h3>
                    <p class="text-gray-300">We prioritize our clients' needs and dreams above all else, ensuring every interaction is meaningful and results-driven.</p>
                </div>
                <div class="glass-card p-6 rounded-xl text-center hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="shield" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Trust & Integrity</h3>
                    <p class="text-gray-300">Transparency and honesty form the foundation of our business relationships, building lasting partnerships with our clients.</p>
                </div>
                <div class="glass-card p-6 rounded-xl text-center hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="award" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Excellence</h3>
                    <p class="text-gray-300">We strive for excellence in every transaction, delivering exceptional service that exceeds expectations.</p>
                </div>
                <div class="glass-card p-6 rounded-xl text-center hover:bg-white/10 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i data-lucide="building" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Market Expertise</h3>
                    <p class="text-gray-300">Our deep understanding of local markets and trends helps clients make informed decisions about their investments.</p>
                </div>
            </div>
        </div>

        <!-- Meet Our Realtor -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-white text-center mb-12">Meet Our Realtor</h2>
            <?php if ($realtor): ?>
            <div class="glass-card p-8 rounded-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <?php if (!empty($realtor['profile_image'])): ?>
                        <div class="w-48 h-48 mx-auto lg:mx-0 mb-8">
                            <img 
                                src="<?php echo htmlspecialchars($realtor['profile_image']); ?>" 
                                alt="<?php echo htmlspecialchars($realtor['full_name']); ?>" 
                                class="w-full h-full object-cover rounded-2xl"
                            />
                        </div>
                        <?php else: ?>
                        <div class="w-48 h-48 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl mx-auto lg:mx-0 mb-8 flex items-center justify-center">
                            <i data-lucide="user" class="w-24 h-24 text-white"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h3 class="text-3xl font-bold text-white mb-4"><?php echo htmlspecialchars($realtor['full_name']); ?></h3>
                        <p class="text-blue-400 text-xl mb-6"><?php echo htmlspecialchars($realtor['title']); ?></p>
                        
                        <div class="text-gray-300 leading-relaxed mb-6">
                            <?php echo nl2br(htmlspecialchars($realtor['bio'])); ?>
                        </div>
                        
                        <!-- Experience -->
                        <div class="flex items-center mb-4">
                            <i data-lucide="award" class="w-5 h-5 text-yellow-400 mr-3"></i>
                            <span class="text-white font-semibold"><?php echo $realtor['experience_years']; ?>+ Years Experience</span>
                        </div>
                        
                        <!-- License -->
                        <?php if (!empty($realtor['license_number'])): ?>
                        <div class="flex items-center mb-4">
                            <i data-lucide="shield-check" class="w-5 h-5 text-green-400 mr-3"></i>
                            <span class="text-white">License: <?php echo htmlspecialchars($realtor['license_number']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Specialties -->
                        <?php 
                        $specialties = json_decode($realtor['specialties'], true) ?? [];
                        if (!empty($specialties)): 
                        ?>
                        <div class="mb-6">
                            <h4 class="text-white font-semibold mb-3">Specialties</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($specialties as $specialty): ?>
                                <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars($specialty); ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Achievements -->
                        <?php 
                        $achievements = json_decode($realtor['achievements'], true) ?? [];
                        if (!empty($achievements)): 
                        ?>
                        <div class="mb-6">
                            <h4 class="text-white font-semibold mb-3">Achievements</h4>
                            <div class="space-y-2">
                                <?php foreach ($achievements as $achievement): ?>
                                <div class="flex items-center">
                                    <i data-lucide="star" class="w-4 h-4 text-yellow-400 mr-2"></i>
                                    <span class="text-gray-300"><?php echo htmlspecialchars($achievement); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Contact Info -->
                        <div class="flex flex-wrap gap-4">
                            <?php if (!empty($realtor['phone'])): ?>
                            <a href="tel:<?php echo htmlspecialchars($realtor['phone']); ?>" class="flex items-center text-blue-400 hover:text-blue-300 transition-colors">
                                <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                <?php echo htmlspecialchars($realtor['phone']); ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($realtor['email'])): ?>
                            <a href="mailto:<?php echo htmlspecialchars($realtor['email']); ?>" class="flex items-center text-blue-400 hover:text-blue-300 transition-colors">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                <?php echo htmlspecialchars($realtor['email']); ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($realtor['whatsapp'])): ?>
                            <a href="https://wa.me/<?php echo htmlspecialchars($realtor['whatsapp']); ?>" target="_blank" class="flex items-center text-green-400 hover:text-green-300 transition-colors">
                                <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                                WhatsApp
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Stats -->
        <div class="glass-card p-8 rounded-2xl">
            <h2 class="text-3xl font-bold text-white text-center mb-12">Our Achievements</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">500+</div>
                    <div class="text-gray-300">Properties Sold</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">1000+</div>
                    <div class="text-gray-300">Happy Clients</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">15+</div>
                    <div class="text-gray-300">Years Experience</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">4.9</div>
                    <div class="text-gray-300">Average Rating</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
