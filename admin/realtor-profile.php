<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Realtor Profile - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Handle form submission
if ($_POST) {
    $full_name = $_POST['full_name'] ?? '';
    $title = $_POST['title'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $profile_image = $_POST['profile_image'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $license_number = $_POST['license_number'] ?? '';
    $experience_years = $_POST['experience_years'] ?? 0;
    
    // Handle specialties
    $specialties = [];
    if (isset($_POST['specialties'])) {
        $specialties = array_filter($_POST['specialties']);
    }
    
    // Handle achievements
    $achievements = [];
    if (isset($_POST['achievements'])) {
        $achievements = array_filter($_POST['achievements']);
    }
    
    if ($full_name && $title && $bio) {
        // Check if profile exists
        $stmt = $db->prepare("SELECT id FROM realtor_profile LIMIT 1");
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update existing
            $stmt = $db->prepare("UPDATE realtor_profile SET full_name = ?, title = ?, bio = ?, profile_image = ?, phone = ?, email = ?, whatsapp = ?, license_number = ?, experience_years = ?, specialties = ?, achievements = ? WHERE id = ?");
            $stmt->execute([$full_name, $title, $bio, $profile_image, $phone, $email, $whatsapp, $license_number, $experience_years, json_encode($specialties), json_encode($achievements), $existing['id']]);
        } else {
            // Insert new
            $stmt = $db->prepare("INSERT INTO realtor_profile (full_name, title, bio, profile_image, phone, email, whatsapp, license_number, experience_years, specialties, achievements) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $title, $bio, $profile_image, $phone, $email, $whatsapp, $license_number, $experience_years, json_encode($specialties), json_encode($achievements)]);
        }
        
        header('Location: realtor-profile.php');
        exit;
    }
}

// Get realtor profile
$stmt = $db->query("SELECT * FROM realtor_profile LIMIT 1");
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$specialties = [];
$achievements = [];
if ($profile) {
    $specialties = json_decode($profile['specialties'], true) ?? [];
    $achievements = json_decode($profile['achievements'], true) ?? [];
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white">Realtor Profile</h1>
                <a href="index.php" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
            
            <form method="POST" class="space-y-8">
                <!-- Basic Information -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Full Name</label>
                            <input
                                type="text"
                                name="full_name"
                                value="<?php echo htmlspecialchars($profile['full_name'] ?? 'Prince Ademuyiwa Edward Ojo'); ?>"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Title</label>
                            <input
                                type="text"
                                name="title"
                                value="<?php echo htmlspecialchars($profile['title'] ?? 'Senior Real Estate Consultant'); ?>"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-white font-semibold mb-2">Bio</label>
                        <textarea
                            name="bio"
                            rows="6"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-white font-semibold mb-2">Profile Image URL</label>
                        <input
                            type="url"
                            name="profile_image"
                            value="<?php echo htmlspecialchars($profile['profile_image'] ?? ''); ?>"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://example.com/profile.jpg"
                        />
                        <?php if (!empty($profile['profile_image'])): ?>
                        <div class="mt-4">
                            <img src="<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="Profile" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Phone</label>
                            <input
                                type="tel"
                                name="phone"
                                value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">WhatsApp</label>
                            <input
                                type="tel"
                                name="whatsapp"
                                value="<?php echo htmlspecialchars($profile['whatsapp'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">License Number</label>
                            <input
                                type="text"
                                name="license_number"
                                value="<?php echo htmlspecialchars($profile['license_number'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-xl font-bold text-white mb-6">Professional Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Experience (Years)</label>
                            <input
                                type="number"
                                name="experience_years"
                                value="<?php echo $profile['experience_years'] ?? 0; ?>"
                                min="0"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                    
                    <!-- Specialties -->
                    <div class="mt-6">
                        <label class="block text-white font-semibold mb-2">Specialties</label>
                        <div id="specialties-container">
                            <?php foreach ($specialties as $index => $specialty): ?>
                            <div class="flex gap-2 mb-2">
                                <input
                                    type="text"
                                    name="specialties[]"
                                    value="<?php echo htmlspecialchars($specialty); ?>"
                                    class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <button type="button" onclick="removeSpecialty(this)" class="bg-red-500 text-white px-4 py-3 rounded-xl">Remove</button>
                            </div>
                            <?php endforeach; ?>
                            <div class="flex gap-2 mb-2">
                                <input
                                    type="text"
                                    name="specialties[]"
                                    placeholder="Add specialty"
                                    class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <button type="button" onclick="addSpecialty()" class="btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Achievements -->
                    <div class="mt-6">
                        <label class="block text-white font-semibold mb-2">Achievements</label>
                        <div id="achievements-container">
                            <?php foreach ($achievements as $index => $achievement): ?>
                            <div class="flex gap-2 mb-2">
                                <input
                                    type="text"
                                    name="achievements[]"
                                    value="<?php echo htmlspecialchars($achievement); ?>"
                                    class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <button type="button" onclick="removeAchievement(this)" class="bg-red-500 text-white px-4 py-3 rounded-xl">Remove</button>
                            </div>
                            <?php endforeach; ?>
                            <div class="flex gap-2 mb-2">
                                <input
                                    type="text"
                                    name="achievements[]"
                                    placeholder="Add achievement"
                                    class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <button type="button" onclick="addAchievement()" class="btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full btn-primary">
                    <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                    Save Profile
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function addSpecialty() {
    const container = document.getElementById('specialties-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = `
        <input
            type="text"
            name="specialties[]"
            placeholder="Add specialty"
            class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button type="button" onclick="removeSpecialty(this)" class="bg-red-500 text-white px-4 py-3 rounded-xl">Remove</button>
    `;
    container.appendChild(div);
}

function removeSpecialty(button) {
    button.parentElement.remove();
}

function addAchievement() {
    const container = document.getElementById('achievements-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = `
        <input
            type="text"
            name="achievements[]"
            placeholder="Add achievement"
            class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button type="button" onclick="removeAchievement(this)" class="bg-red-500 text-white px-4 py-3 rounded-xl">Remove</button>
    `;
    container.appendChild(div);
}

function removeAchievement(button) {
    button.parentElement.remove();
}
</script>

<?php include '../includes/footer.php'; ?>
