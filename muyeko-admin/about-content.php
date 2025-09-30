<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "About Content - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Handle form submission
if ($_POST) {
    $section_key = $_POST['section_key'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;
    
    if ($section_key && $title && $content) {
        // Check if section exists
        $stmt = $db->prepare("SELECT id FROM about_content WHERE section_key = ?");
        $stmt->execute([$section_key]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update existing
            $stmt = $db->prepare("UPDATE about_content SET title = ?, content = ?, image_url = ?, display_order = ? WHERE section_key = ?");
            $stmt->execute([$title, $content, $image_url, $display_order, $section_key]);
        } else {
            // Insert new
            $stmt = $db->prepare("INSERT INTO about_content (section_key, title, content, image_url, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$section_key, $title, $content, $image_url, $display_order]);
        }
        
        header('Location: about-content.php');
        exit;
    }
}

// Get all about content
$stmt = $db->query("SELECT * FROM about_content ORDER BY display_order ASC");
$about_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white">About Page Content</h1>
                <a href="index.php" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
            
            <!-- About Sections -->
            <div class="space-y-6">
                <?php foreach ($about_sections as $section): ?>
                <div class="glass-card p-6 rounded-xl">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($section['title']); ?></h3>
                            <p class="text-gray-400">Section: <?php echo htmlspecialchars($section['section_key']); ?></p>
                        </div>
                        <button onclick="editSection('<?php echo $section['section_key']; ?>')" class="btn-primary">
                            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                            Edit
                        </button>
                    </div>
                    
                    <?php if ($section['image_url']): ?>
                    <div class="mb-4">
                        <img src="<?php echo htmlspecialchars($section['image_url']); ?>" alt="<?php echo htmlspecialchars($section['title']); ?>" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                    <?php endif; ?>
                    
                    <div class="text-gray-300">
                        <?php echo nl2br(htmlspecialchars($section['content'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="glass rounded-2xl p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Edit Section</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form method="POST" id="editForm">
                <input type="hidden" name="section_key" id="edit_section_key">
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">Title</label>
                        <input
                            type="text"
                            name="title"
                            id="edit_title"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Content</label>
                        <textarea
                            name="content"
                            id="edit_content"
                            rows="6"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Image URL</label>
                        <input
                            type="url"
                            name="image_url"
                            id="edit_image_url"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://example.com/image.jpg"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Display Order</label>
                        <input
                            type="number"
                            name="display_order"
                            id="edit_display_order"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-8">
                    <button type="button" onclick="closeModal()" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSection(sectionKey) {
    // Get section data (you would normally fetch this via AJAX)
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    
    // Set form values (in a real implementation, you'd fetch the data)
    document.getElementById('edit_section_key').value = sectionKey;
}

function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include '../includes/footer.php'; ?>
