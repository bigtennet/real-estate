<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Edit Property - Admin";
include '../includes/header.php';

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    header('Location: properties.php');
    exit;
}

// Get property details
$stmt = $db->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header('Location: properties.php');
    exit;
}

// Handle form submission
if ($_POST) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $location = $_POST['location'] ?? '';
    $bedrooms = $_POST['bedrooms'] ?? '';
    $bathrooms = $_POST['bathrooms'] ?? '';
    $area = $_POST['area'] ?? '';
    $type = $_POST['type'] ?? '';
    $status = $_POST['status'] ?? 'AVAILABLE';
    
    // Handle features
    $features = [];
    if (isset($_POST['features'])) {
        $features = array_filter($_POST['features']);
    }
    
    // Handle images
    $images = [];
    if (isset($_POST['images'])) {
        $images = array_filter($_POST['images']);
    }
    
    if ($title && $description && $price && $location && $bedrooms && $bathrooms && $area && $type) {
        // Check if owner_id column exists
        $stmt = $db->query("SHOW COLUMNS FROM properties LIKE 'owner_id'");
        $has_owner_id = $stmt->rowCount() > 0;
        
        if ($has_owner_id) {
            // Old database structure - update with owner_id
            $stmt = $db->prepare("UPDATE properties SET title = ?, description = ?, price = ?, location = ?, bedrooms = ?, bathrooms = ?, area = ?, type = ?, status = ?, features = ?, images = ? WHERE id = ?");
            $stmt->execute([
                $title, $description, $price, $location, $bedrooms, $bathrooms, $area, $type, $status,
                json_encode($features), json_encode($images), $property_id
            ]);
        } else {
            // New database structure - no owner_id needed
            $stmt = $db->prepare("UPDATE properties SET title = ?, description = ?, price = ?, location = ?, bedrooms = ?, bathrooms = ?, area = ?, type = ?, status = ?, features = ?, images = ? WHERE id = ?");
            $stmt->execute([
                $title, $description, $price, $location, $bedrooms, $bathrooms, $area, $type, $status,
                json_encode($features), json_encode($images), $property_id
            ]);
        }
        
        header('Location: properties.php');
        exit;
    }
}

// Get current features and images
$current_features = json_decode($property['features'], true) ?? [];
$current_images = json_decode($property['images'], true) ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white">Edit Property</h1>
                <a href="properties.php" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                    Back to Properties
                </a>
            </div>
            
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">Property Title</label>
                        <input
                            type="text"
                            name="title"
                            value="<?php echo htmlspecialchars($property['title']); ?>"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter property title"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Price (₦)</label>
                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            value="<?php echo $property['price']; ?>"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter price in Naira"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-white font-semibold mb-2">Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter property description"
                    ><?php echo htmlspecialchars($property['description']); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">Location</label>
                        <input
                            type="text"
                            name="location"
                            value="<?php echo htmlspecialchars($property['location']); ?>"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter location"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Bedrooms</label>
                        <input
                            type="number"
                            name="bedrooms"
                            value="<?php echo $property['bedrooms']; ?>"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Number of bedrooms"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Bathrooms</label>
                        <input
                            type="number"
                            name="bathrooms"
                            value="<?php echo $property['bathrooms']; ?>"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Number of bathrooms"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">Area (sq ft)</label>
                        <input
                            type="number"
                            name="area"
                            step="0.01"
                            value="<?php echo $property['area']; ?>"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Property area"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Property Type</label>
                        <select
                            name="type"
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Type</option>
                            <option value="HOUSE" <?php echo $property['type'] === 'HOUSE' ? 'selected' : ''; ?>>House</option>
                            <option value="APARTMENT" <?php echo $property['type'] === 'APARTMENT' ? 'selected' : ''; ?>>Apartment</option>
                            <option value="VILLA" <?php echo $property['type'] === 'VILLA' ? 'selected' : ''; ?>>Villa</option>
                            <option value="CONDO" <?php echo $property['type'] === 'CONDO' ? 'selected' : ''; ?>>Condo</option>
                            <option value="TOWNHOUSE" <?php echo $property['type'] === 'TOWNHOUSE' ? 'selected' : ''; ?>>Townhouse</option>
                            <option value="LAND" <?php echo $property['type'] === 'LAND' ? 'selected' : ''; ?>>Land</option>
                            <option value="COMMERCIAL" <?php echo $property['type'] === 'COMMERCIAL' ? 'selected' : ''; ?>>Commercial</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Status</label>
                        <select
                            name="status"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="AVAILABLE" <?php echo $property['status'] === 'AVAILABLE' ? 'selected' : ''; ?>>Available</option>
                            <option value="SOLD" <?php echo $property['status'] === 'SOLD' ? 'selected' : ''; ?>>Sold</option>
                            <option value="RENTED" <?php echo $property['status'] === 'RENTED' ? 'selected' : ''; ?>>Rented</option>
                            <option value="PENDING" <?php echo $property['status'] === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                </div>

                <!-- Features -->
                <div>
                    <label class="block text-white font-semibold mb-2">Features</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php
                        $all_features = ['Pool', 'Garden', 'Garage', 'Security', 'Balcony', 'Gym', 'Parking', 'Smart Home', 'City View', 'Ocean View', 'Beach Access', 'Concierge', 'Fitness Center'];
                        foreach ($all_features as $feature):
                        ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="<?php echo $feature; ?>" <?php echo in_array($feature, $current_features) ? 'checked' : ''; ?> class="mr-2">
                            <span class="text-white"><?php echo $feature; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Images -->
                <div>
                    <label class="block text-white font-semibold mb-2">Images</label>
                    <div id="image-inputs">
                        <?php foreach ($current_images as $index => $image): ?>
                        <div class="flex gap-2 mb-2">
                            <input
                                type="url"
                                name="images[]"
                                value="<?php echo htmlspecialchars($image); ?>"
                                placeholder="Image URL"
                                class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <button type="button" onclick="removeImageInput(this)" class="bg-red-500 text-white px-4 py-3 rounded-xl">Remove</button>
                        </div>
                        <?php endforeach; ?>
                        <div class="flex gap-2 mb-2">
                            <input
                                type="url"
                                name="images[]"
                                placeholder="Image URL"
                                class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <button type="button" onclick="addImageInput()" class="btn-primary">Add</button>
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full btn-primary"
                >
                    Update Property
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function addImageInput() {
    const container = document.getElementById('image-inputs');
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = `
        <input
            type="url"
            name="images[]"
            placeholder="Image URL"
            class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button type="button" onclick="removeImageInput(this)" class="bg-red-500 text-white px-4 py-3 rounded-xl">Remove</button>
    `;
    container.appendChild(div);
}

function removeImageInput(button) {
    button.parentElement.remove();
}
</script>

<?php include '../includes/footer.php'; ?>
