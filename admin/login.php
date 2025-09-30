<?php
ob_start();
session_start();

// Simple authentication
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple hardcoded credentials (in production, use proper authentication)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

$page_title = "Admin Login - Premium Real Estate";
include '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center">
    <div class="glass-card p-8 rounded-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="shield" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Admin Login</h1>
            <p class="text-gray-300">Access the admin dashboard</p>
        </div>

        <?php if(isset($error)): ?>
        <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-6 text-center">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-white font-semibold mb-2">Username</label>
                <input
                    type="text"
                    name="username"
                    required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter username"
                />
            </div>
            
            <div>
                <label class="block text-white font-semibold mb-2">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter password"
                />
            </div>
            
            <button
                type="submit"
                name="login"
                class="w-full btn-primary"
            >
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">
                Default credentials: admin / admin123
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
