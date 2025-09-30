<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Add Property - Admin";
include '../includes/header.php';

$config_path = file_exists('config/database.php') ? 'config/database.php' : '../config/database.php';
require_once $config_path;
$database = new Database();
$db = $database->getConnection();

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
            // Old database structure - insert with owner_id = 1 (admin)
            $stmt = $db->prepare("INSERT INTO properties (title, description, price, location, bedrooms, bathrooms, area, type, status, features, images, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title, $description, $price, $location, $bedrooms, $bathrooms, $area, $type, $status,
                json_encode($features), json_encode($images), 1
            ]);
        } else {
            // New database structure - no owner_id needed
            $stmt = $db->prepare("INSERT INTO properties (title, description, price, location, bedrooms, bathrooms, area, type, status, features, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title, $description, $price, $location, $bedrooms, $bathrooms, $area, $type, $status,
                json_encode($features), json_encode($images)
            ]);
        }
        
        header('Location: properties.php');
        exit;
    }
}
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="glass rounded-2xl p-8">
            <h1 class="text-3xl font-bold text-white mb-8">Add New Property</h1>
            
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">Property Title</label>
                        <input
                            type="text"
                            name="title"
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
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-white font-semibold mb-2">Location</label>
                        <input
                            type="text"
                            name="location"
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
                            <option value="HOUSE">House</option>
                            <option value="APARTMENT">Apartment</option>
                            <option value="VILLA">Villa</option>
                            <option value="CONDO">Condo</option>
                            <option value="TOWNHOUSE">Townhouse</option>
                            <option value="LAND">Land</option>
                            <option value="COMMERCIAL">Commercial</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Status</label>
                        <select
                            name="status"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="AVAILABLE">Available</option>
                            <option value="SOLD">Sold</option>
                            <option value="RENTED">Rented</option>
                            <option value="PENDING">Pending</option>
                        </select>
                    </div>
                </div>


                <!-- Features -->
                <div>
                    <label class="block text-white font-semibold mb-2">Features</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Pool" class="mr-2">
                            <span class="text-white">Pool</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Garden" class="mr-2">
                            <span class="text-white">Garden</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Garage" class="mr-2">
                            <span class="text-white">Garage</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Security" class="mr-2">
                            <span class="text-white">Security</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Balcony" class="mr-2">
                            <span class="text-white">Balcony</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Gym" class="mr-2">
                            <span class="text-white">Gym</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Parking" class="mr-2">
                            <span class="text-white">Parking</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="Smart Home" class="mr-2">
                            <span class="text-white">Smart Home</span>
                        </label>
                    </div>
                </div>

                <!-- Images -->
                <div>
                    <label class="block text-white font-semibold mb-2">Images</label>
                    <div id="image-inputs">
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
                    Add Property
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
