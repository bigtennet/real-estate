<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Manage Owners - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM owners WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: owners.php');
    exit;
}

// Handle form submission
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $action = $_POST['action'] ?? '';
    $owner_id = $_POST['owner_id'] ?? '';
    
    if ($action === 'add' && $name && $email && $phone && $whatsapp) {
        $stmt = $db->prepare("INSERT INTO owners (name, email, phone, whatsapp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $whatsapp]);
        header('Location: owners.php');
        exit;
    } elseif ($action === 'edit' && $owner_id && $name && $email && $phone && $whatsapp) {
        $stmt = $db->prepare("UPDATE owners SET name = ?, email = ?, phone = ?, whatsapp = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $whatsapp, $owner_id]);
        header('Location: owners.php');
        exit;
    }
}

// Get owners
$stmt = $db->query("SELECT * FROM owners ORDER BY created_at DESC");
$owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get owner for editing
$edit_owner = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM owners WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_owner = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white">Manage Owners</h1>
                <button onclick="toggleAddForm()" class="btn-primary">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Add Owner
                </button>
            </div>

            <!-- Add/Edit Form -->
            <div id="owner-form" class="glass-card p-6 rounded-xl mb-8" style="display: none;">
                <h2 class="text-xl font-bold text-white mb-6">
                    <?php echo $edit_owner ? 'Edit Owner' : 'Add New Owner'; ?>
                </h2>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="<?php echo $edit_owner ? 'edit' : 'add'; ?>">
                    <?php if($edit_owner): ?>
                    <input type="hidden" name="owner_id" value="<?php echo $edit_owner['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Name</label>
                            <input
                                type="text"
                                name="name"
                                value="<?php echo $edit_owner ? htmlspecialchars($edit_owner['name']) : ''; ?>"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Owner name"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="<?php echo $edit_owner ? htmlspecialchars($edit_owner['email']) : ''; ?>"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="owner@email.com"
                            />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Phone</label>
                            <input
                                type="tel"
                                name="phone"
                                value="<?php echo $edit_owner ? htmlspecialchars($edit_owner['phone']) : ''; ?>"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Phone number"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">WhatsApp</label>
                            <input
                                type="tel"
                                name="whatsapp"
                                value="<?php echo $edit_owner ? htmlspecialchars($edit_owner['whatsapp']) : ''; ?>"
                                required
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="WhatsApp number"
                            />
                        </div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="btn-primary">
                            <?php echo $edit_owner ? 'Update Owner' : 'Add Owner'; ?>
                        </button>
                        <button type="button" onclick="toggleAddForm()" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Owners Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-4 px-2">Name</th>
                            <th class="text-left py-4 px-2">Email</th>
                            <th class="text-left py-4 px-2">Phone</th>
                            <th class="text-left py-4 px-2">WhatsApp</th>
                            <th class="text-left py-4 px-2">Properties</th>
                            <th class="text-left py-4 px-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($owners as $owner): ?>
                        <?php
                        // Get property count for this owner
                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM properties WHERE owner_id = ?");
                        $stmt->execute([$owner['id']]);
                        $property_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                        ?>
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="py-4 px-2">
                                <div class="font-semibold"><?php echo htmlspecialchars($owner['name']); ?></div>
                                <div class="text-gray-400 text-sm">ID: <?php echo $owner['id']; ?></div>
                            </td>
                            <td class="py-4 px-2"><?php echo htmlspecialchars($owner['email']); ?></td>
                            <td class="py-4 px-2"><?php echo htmlspecialchars($owner['phone']); ?></td>
                            <td class="py-4 px-2"><?php echo htmlspecialchars($owner['whatsapp']); ?></td>
                            <td class="py-4 px-2">
                                <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded-full text-sm">
                                    <?php echo $property_count; ?> properties
                                </span>
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex space-x-2">
                                    <a href="?edit=<?php echo $owner['id']; ?>" class="p-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?delete=<?php echo $owner['id']; ?>" onclick="return confirm('Are you sure you want to delete this owner? This will also delete all their properties.')" class="p-2 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAddForm() {
    const form = document.getElementById('owner-form');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

// Show form if editing
<?php if($edit_owner): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('owner-form').style.display = 'block';
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
