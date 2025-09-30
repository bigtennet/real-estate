<?php
ob_start();

// Get site settings first
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'includes/functions.php';
$site_settings = getSiteSettings($db);

$page_title = "Properties - " . ($site_settings['site_name'] ?? 'Premium Real Estate');
$page_description = "Browse our extensive collection of premium properties for sale and rent.";
include 'includes/header.php';

// Get settings
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get search parameters
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';
$type = $_GET['type'] ?? '';
$bedrooms = $_GET['bedrooms'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';

// Build query - handle both old and new database structure
$query = "SELECT p.* FROM properties p WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (p.title LIKE :search OR p.location LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($location)) {
    $query .= " AND p.location LIKE :location";
    $params[':location'] = "%$location%";
}

if (!empty($type)) {
    $query .= " AND p.type = :type";
    $params[':type'] = $type;
}

if (!empty($bedrooms)) {
    $query .= " AND p.bedrooms >= :bedrooms";
    $params[':bedrooms'] = $bedrooms;
}

if (!empty($price_min)) {
    $query .= " AND p.price >= :price_min";
    $params[':price_min'] = $price_min;
}

if (!empty($price_max)) {
    $query .= " AND p.price <= :price_max";
    $params[':price_max'] = $price_max;
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Our <span class="gradient-text">Properties</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Discover our curated selection of premium properties
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="glass p-6 rounded-2xl mb-8">
            <form method="GET" class="space-y-4">
                <div class="flex flex-col lg:flex-row gap-4 mb-4">
                    <div class="flex-1 relative">
                        <i data-lucide="search" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by location, property type..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <button type="submit" class="btn-primary flex items-center justify-center">
                        <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
                        Search
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-white font-semibold mb-2">Location</label>
                        <input
                            type="text"
                            name="location"
                            placeholder="Enter location"
                            value="<?php echo htmlspecialchars($location); ?>"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Property Type</label>
                        <select
                            name="type"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Types</option>
                            <option value="HOUSE" <?php echo $type === 'HOUSE' ? 'selected' : ''; ?>>House</option>
                            <option value="APARTMENT" <?php echo $type === 'APARTMENT' ? 'selected' : ''; ?>>Apartment</option>
                            <option value="VILLA" <?php echo $type === 'VILLA' ? 'selected' : ''; ?>>Villa</option>
                            <option value="CONDO" <?php echo $type === 'CONDO' ? 'selected' : ''; ?>>Condo</option>
                            <option value="TOWNHOUSE" <?php echo $type === 'TOWNHOUSE' ? 'selected' : ''; ?>>Townhouse</option>
                            <option value="LAND" <?php echo $type === 'LAND' ? 'selected' : ''; ?>>Land</option>
                            <option value="COMMERCIAL" <?php echo $type === 'COMMERCIAL' ? 'selected' : ''; ?>>Commercial</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Bedrooms</label>
                        <select
                            name="bedrooms"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Any</option>
                            <option value="1" <?php echo $bedrooms === '1' ? 'selected' : ''; ?>>1+</option>
                            <option value="2" <?php echo $bedrooms === '2' ? 'selected' : ''; ?>>2+</option>
                            <option value="3" <?php echo $bedrooms === '3' ? 'selected' : ''; ?>>3+</option>
                            <option value="4" <?php echo $bedrooms === '4' ? 'selected' : ''; ?>>4+</option>
                            <option value="5" <?php echo $bedrooms === '5' ? 'selected' : ''; ?>>5+</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Price Range</label>
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                name="price_min"
                                placeholder="Min"
                                value="<?php echo htmlspecialchars($price_min); ?>"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <span class="text-gray-300">-</span>
                            <input
                                type="number"
                                name="price_max"
                                placeholder="Max"
                                value="<?php echo htmlspecialchars($price_max); ?>"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Properties Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(empty($properties)): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">No properties found</div>
                    <p class="text-gray-500">Try adjusting your search criteria</p>
                </div>
            <?php else: ?>
                <?php foreach($properties as $property): ?>
                <div class="glass-card rounded-2xl overflow-hidden hover:bg-white/10 transition-all duration-300 group">
                    <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="block">
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
                                <?php echo $property['status']; ?>
                            </div>
                            <div class="absolute top-4 left-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                                <?php echo $property['type']; ?>
                            </div>
                        </div>
                    </a>
                    
                    <div class="p-6">
                        <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="block">
                            <h3 class="text-xl font-bold text-white mb-2 hover:text-blue-300 transition-colors"><?php echo htmlspecialchars($property['title']); ?></h3>
                        </a>
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
                            <div class="flex space-x-2">
                                <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="btn-primary flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                                    View
                                </a>
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
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function openWhatsApp(whatsapp, title, location) {
    const message = `Hi! I'm interested in the property "${title}" at ${location}. Could you provide more details?`;
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${whatsapp.replace(/\D/g, '')}?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
