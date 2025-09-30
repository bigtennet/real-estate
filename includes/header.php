<?php
// Get site settings if not already loaded
if (!isset($site_settings)) {
    // Determine the correct path to config/database.php
    $config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
    require_once $config_path;
    $database = new Database();
    $db = $database->getConnection();
    
    // Determine the correct path to includes/functions.php
    $functions_path = file_exists('includes/functions.php') ? 'includes/functions.php' : '../includes/functions.php';
    require_once $functions_path;
    $site_settings = getSiteSettings($db);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : ($site_settings['site_name'] ?? 'Premium Real Estate - Find Your Dream Home'); ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : ($site_settings['site_description'] ?? 'Discover luxury properties and find your perfect home with our premium real estate listings.'); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        glass: {
                            white: 'rgba(255, 255, 255, 0.1)',
                            black: 'rgba(0, 0, 0, 0.1)',
                        }
                    },
                    backdropBlur: {
                        xs: '2px',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                    },
                },
            },
        }
        
        // Theme management
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.classList.remove(currentTheme);
            html.classList.add(newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        // Initialize theme
        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (prefersDark ? 'dark' : 'light');
            
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(theme);
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', initTheme);
    </script>
    
    <!-- Custom CSS -->
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .glass-dark {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        /* Light mode styles */
        .light {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .light .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }
        
        .light .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.08);
        }
        
        .light .glass-card:hover {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.12);
        }
        
        .light .text-white {
            color: #1e293b !important;
        }
        
        .light .text-gray-300 {
            color: #64748b !important;
        }
        
        .light .text-gray-400 {
            color: #94a3b8 !important;
        }
        
        .light .text-gray-500 {
            color: #64748b !important;
        }
        
        .light .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .light .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
        }
        
        .light .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }
        
        .light .btn-whatsapp {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .light .btn-whatsapp:hover {
            background: linear-gradient(135deg, #059669, #047857);
        }
        
        .light .text-blue-400 {
            color: #3b82f6 !important;
        }
        
        .light .text-green-400 {
            color: #10b981 !important;
        }
        
        .light .text-yellow-400 {
            color: #f59e0b !important;
        }
        
        .light .text-purple-400 {
            color: #8b5cf6 !important;
        }
        
        .light .text-pink-400 {
            color: #ec4899 !important;
        }
        
        .light .text-cyan-400 {
            color: #06b6d4 !important;
        }
        
        .light .text-orange-400 {
            color: #f97316 !important;
        }
        
        .light .text-emerald-400 {
            color: #10b981 !important;
        }
        
        .light .text-indigo-400 {
            color: #6366f1 !important;
        }
        
        .light .text-red-400 {
            color: #ef4444 !important;
        }
        
        .light .bg-blue-500\/20 {
            background-color: rgba(59, 130, 246, 0.2) !important;
        }
        
        .light .text-blue-300 {
            color: #60a5fa !important;
        }
        
        .light .border-white\/20 {
            border-color: rgba(0, 0, 0, 0.1) !important;
        }
        
        .light .border-white\/10 {
            border-color: rgba(0, 0, 0, 0.05) !important;
        }
        
        .light .hover\:bg-white\/10:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }
        
        .light .hover\:bg-white\/20:hover {
            background-color: rgba(0, 0, 0, 0.1) !important;
        }
        
        .light .hover\:text-blue-300:hover {
            color: #60a5fa !important;
        }
        
        .light .hover\:text-green-300:hover {
            color: #34d399 !important;
        }
        
        .light .placeholder-gray-400::placeholder {
            color: #94a3b8 !important;
        }
        
        .light .focus\:ring-blue-500:focus {
            --tw-ring-color: #3b82f6 !important;
        }
        
        .light .focus\:outline-none:focus {
            outline: none !important;
        }
        
        .light .bg-white\/10 {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }
        
        .light .bg-white\/20 {
            background-color: rgba(0, 0, 0, 0.1) !important;
        }
        
        .light .bg-gradient-to-r {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6) !important;
        }
        
        .light .bg-gradient-to-br {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
        }
        
        .light .from-slate-900 {
            --tw-gradient-from: #f8fafc !important;
        }
        
        .light .via-purple-900 {
            --tw-gradient-via: #e2e8f0 !important;
        }
        
        .light .to-slate-900 {
            --tw-gradient-to: #f8fafc !important;
        }
        
        /* Additional light mode improvements */
        .light .bg-slate-900 {
            background-color: #f8fafc !important;
        }
        
        .light .bg-purple-900 {
            background-color: #e2e8f0 !important;
        }
        
        .light .text-slate-900 {
            color: #1e293b !important;
        }
        
        .light .text-slate-800 {
            color: #1e293b !important;
        }
        
        .light .text-slate-700 {
            color: #334155 !important;
        }
        
        .light .text-slate-600 {
            color: #475569 !important;
        }
        
        .light .text-slate-500 {
            color: #64748b !important;
        }
        
        .light .text-slate-400 {
            color: #94a3b8 !important;
        }
        
        .light .text-slate-300 {
            color: #cbd5e1 !important;
        }
        
        .light .text-slate-200 {
            color: #e2e8f0 !important;
        }
        
        .light .text-slate-100 {
            color: #f1f5f9 !important;
        }
        
        .light .border-slate-200 {
            border-color: #e2e8f0 !important;
        }
        
        .light .border-slate-300 {
            border-color: #cbd5e1 !important;
        }
        
        .light .bg-slate-50 {
            background-color: #f8fafc !important;
        }
        
        .light .bg-slate-100 {
            background-color: #f1f5f9 !important;
        }
        
        .light .bg-slate-200 {
            background-color: #e2e8f0 !important;
        }
        
        .light .hover\:bg-slate-100:hover {
            background-color: #f1f5f9 !important;
        }
        
        .light .hover\:bg-slate-200:hover {
            background-color: #e2e8f0 !important;
        }
        
        .light .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .light .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        
        .light .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        
        /* Light mode navigation */
        .light nav {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
        }
        
        .light nav a {
            color: #1e293b !important;
        }
        
        .light nav a:hover {
            color: #3b82f6 !important;
        }
        
        /* Light mode footer */
        .light footer {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
        }
        
        .light footer h3 {
            color: #1e293b !important;
        }
        
        .light footer p {
            color: #64748b !important;
        }
        
        .light footer a {
            color: #64748b !important;
        }
        
        .light footer a:hover {
            color: #3b82f6 !important;
        }
        
        /* Light mode form improvements */
        .light input[type="text"],
        .light input[type="email"],
        .light input[type="tel"],
        .light input[type="url"],
        .light input[type="number"],
        .light textarea,
        .light select {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
            color: #1e293b !important;
        }
        
        .light input[type="text"]:focus,
        .light input[type="email"]:focus,
        .light input[type="tel"]:focus,
        .light input[type="url"]:focus,
        .light input[type="number"]:focus,
        .light textarea:focus,
        .light select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        .light input::placeholder,
        .light textarea::placeholder {
            color: #94a3b8 !important;
        }
        
        /* Light mode status badges */
        .light .bg-green-500 {
            background-color: #10b981 !important;
        }
        
        .light .bg-red-500 {
            background-color: #ef4444 !important;
        }
        
        .light .bg-yellow-500 {
            background-color: #f59e0b !important;
        }
        
        .light .bg-blue-500 {
            background-color: #3b82f6 !important;
        }
        
        .light .bg-purple-500 {
            background-color: #8b5cf6 !important;
        }
        
        /* Light mode property cards */
        .light .property-card {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        
        .light .property-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        /* Improved dropdown styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-width: 200px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 8px;
        }
        
        .dark .dropdown-content {
            background-color: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .dropdown-item {
            display: block;
            padding: 12px 16px;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.1);
            color: #1d4ed8;
        }
        
        .dark .dropdown-item {
            color: #e5e7eb;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dark .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }
        
        /* Light mode dropdown improvements */
        .light .dropdown-content {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .light .dropdown-item {
            color: #374151;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .light .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.1);
            color: #1d4ed8;
        }
        
        .light .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.37);
        }
        
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .gradient-text {
            background: linear-gradient(to right, #60a5fa, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #3b82f6, #8b5cf6);
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 16px 0 rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            transform: scale(1);
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #2563eb, #7c3aed);
            box-shadow: 0 8px 32px 0 rgba(59, 130, 246, 0.4);
            transform: scale(1.05);
        }
        
        .btn-whatsapp {
            background: linear-gradient(to right, #10b981, #059669);
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 16px 0 rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
            transform: scale(1);
        }
        
        .btn-whatsapp:hover {
            background: linear-gradient(to right, #059669, #047857);
            box-shadow: 0 8px 32px 0 rgba(16, 185, 129, 0.4);
            transform: scale(1.05);
        }
        
        body {
            background: linear-gradient(135deg, #0f172a 0%, #7c3aed 50%, #0f172a 100%);
            background-attachment: fixed;
        }
    </style>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="min-h-screen dark">
    <!-- Navigation -->
    <nav class="glass fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="index.php" class="flex items-center space-x-2">
                    <?php if (!empty($site_settings['site_logo'])): ?>
                    <img src="<?php echo htmlspecialchars($site_settings['site_logo']); ?>" alt="Logo" class="w-10 h-10 rounded-lg object-contain">
                    <?php else: ?>
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="home" class="w-6 h-6 text-white"></i>
                    </div>
                    <?php endif; ?>
                    <span class="text-xl font-bold gradient-text"><?php echo htmlspecialchars($site_settings['site_name'] ?? 'Premium Real Estate'); ?></span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-white hover:text-blue-300 transition-colors">Home</a>
                    <a href="properties.php" class="text-white hover:text-blue-300 transition-colors">Properties</a>
                    <a href="about.php" class="text-white hover:text-blue-300 transition-colors">About</a>
                    <a href="contact.php" class="text-white hover:text-blue-300 transition-colors">Contact</a>
                    <a href="admin/" class="glass-card px-4 py-2 rounded-lg text-white hover:bg-white/20 transition-all">
                        <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                        Admin
                    </a>
                    
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                        <i data-lucide="sun" class="w-5 h-5 text-white dark:hidden"></i>
                        <i data-lucide="moon" class="w-5 h-5 text-white hidden dark:block"></i>
                    </button>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-white hover:text-blue-300 transition-colors">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 glass-card rounded-lg mt-2">
                    <a href="index.php" class="block px-3 py-2 text-white hover:text-blue-300 transition-colors">Home</a>
                    <a href="properties.php" class="block px-3 py-2 text-white hover:text-blue-300 transition-colors">Properties</a>
                    <a href="about.php" class="block px-3 py-2 text-white hover:text-blue-300 transition-colors">About</a>
                    <a href="contact.php" class="block px-3 py-2 text-white hover:text-blue-300 transition-colors">Contact</a>
                    <a href="admin/" class="block px-3 py-2 text-white hover:text-blue-300 transition-colors">
                        <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                        Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
