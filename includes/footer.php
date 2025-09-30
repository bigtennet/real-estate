    <!-- Footer -->
    <footer class="glass mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <a href="index.php" class="flex items-center space-x-2 mb-4">
                        <?php if (!empty($site_settings['site_logo'])): ?>
                        <img src="<?php echo htmlspecialchars($site_settings['site_logo']); ?>" alt="Logo" class="w-10 h-10 rounded-lg object-contain">
                        <?php else: ?>
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="home" class="w-6 h-6 text-white"></i>
                        </div>
                        <?php endif; ?>
                        <span class="text-xl font-bold gradient-text"><?php echo htmlspecialchars($site_settings['site_name'] ?? 'Premium Real Estate'); ?></span>
                    </a>
                    <p class="text-gray-300 mb-4">
                        <?php echo htmlspecialchars($site_settings['site_description'] ?? 'Your trusted partner in finding the perfect property. We specialize in luxury real estate and exceptional service.'); ?>
                    </p>
                    <div class="flex space-x-4">
                        <?php if (!empty($site_settings['facebook_url'])): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['facebook_url']); ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <i data-lucide="facebook" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($site_settings['twitter_url'])): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['twitter_url']); ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <i data-lucide="twitter" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($site_settings['instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['instagram_url']); ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <i data-lucide="instagram" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($site_settings['linkedin_url'])): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['linkedin_url']); ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <i data-lucide="linkedin" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($site_settings['youtube_url'])): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['youtube_url']); ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <i data-lucide="youtube" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                        <li><a href="properties.php" class="text-gray-300 hover:text-white transition-colors">Properties</a></li>
                        <li><a href="about.php" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><span class="text-gray-300">Property Sales</span></li>
                        <li><span class="text-gray-300">Property Rentals</span></li>
                        <li><span class="text-gray-300">Property Management</span></li>
                        <li><span class="text-gray-300">Investment Consulting</span></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Contact Info</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i data-lucide="phone" class="w-4 h-4 text-blue-400 mr-3"></i>
                            <span class="text-gray-300"><?php echo htmlspecialchars($site_settings['contact_phone'] ?? '+234 (0) 123-456-7890'); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 text-blue-400 mr-3"></i>
                            <span class="text-gray-300"><?php echo htmlspecialchars($site_settings['contact_email'] ?? 'info@premiumrealestate.ng'); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="map-pin" class="w-4 h-4 text-blue-400 mr-3"></i>
                            <span class="text-gray-300"><?php echo htmlspecialchars($site_settings['contact_address'] ?? '123 Victoria Island, Lagos, Nigeria'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Signup -->
            <div class="glass-card p-8 rounded-2xl mt-12">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-white mb-4">Stay Updated</h3>
                    <p class="text-gray-300 mb-6">Get the latest property listings and real estate news delivered to your inbox.</p>
                        <form method="POST" action="newsletter-subscribe.php" class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                            <input
                                type="email"
                                name="email"
                                placeholder="Enter your email"
                                required
                                class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <button type="submit" class="btn-primary whitespace-nowrap">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                Subscribe
                            </button>
                        </form>
                </div>
            </div>
            
            <div class="border-t border-white/10 mt-8 pt-8 text-center">
                <p class="text-gray-400">© 2024 Premium Real Estate. All rights reserved.</p>
                <p class="text-gray-500 text-sm mt-2">Designed with ❤️ for Nigerian real estate</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
