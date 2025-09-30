<?php
ob_start();

// Get site settings first
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'includes/functions.php';
$site_settings = getSiteSettings($db);

$page_title = "Property Details - " . ($site_settings['site_name'] ?? 'Premium Real Estate');
include 'includes/header.php';

$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    header('Location: properties.php');
    exit;
}

// Get property details
$query = "SELECT p.* FROM properties p WHERE p.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header('Location: properties.php');
    exit;
}

// Get settings for WhatsApp
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get property files
$stmt = $db->prepare("SELECT * FROM file_uploads WHERE property_id = ? ORDER BY file_type, created_at ASC");
$stmt->execute([$property_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate images and videos
$uploaded_images = array_filter($files, function($file) { return $file['file_type'] === 'image'; });
$videos = array_filter($files, function($file) { return $file['file_type'] === 'video'; });

// Get images from property JSON field as fallback
$json_images = json_decode($property['images'], true) ?? [];

// Combine uploaded images and JSON images
$images = [];
if (!empty($uploaded_images)) {
    $images = $uploaded_images;
} else if (!empty($json_images)) {
    // Convert JSON images to the same format as uploaded images
    foreach ($json_images as $index => $image_url) {
        $images[] = [
            'file_path' => $image_url,
            'file_name' => 'Image ' . ($index + 1),
            'is_primary' => $index === 0 ? 1 : 0
        ];
    }
}

// Get primary image
$primary_image = null;
foreach ($images as $image) {
    if (isset($image['is_primary']) && $image['is_primary']) {
        $primary_image = $image;
        break;
    }
}
if (!$primary_image && !empty($images)) {
    $primary_image = $images[0];
}

// Get features
$features = json_decode($property['features'], true) ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Property Header -->
        <div class="glass rounded-2xl p-8 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2"><?php echo htmlspecialchars($property['title']); ?></h1>
                    <div class="flex items-center text-gray-300 mb-4">
                        <i data-lucide="map-pin" class="w-5 h-5 mr-2"></i>
                        <?php echo htmlspecialchars($property['location']); ?>
                    </div>
                    <div class="text-3xl font-bold gradient-text">₦<?php echo number_format($property['price']); ?></div>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 mt-4 lg:mt-0">
                    <button 
                        onclick="openWhatsApp('<?php echo htmlspecialchars($settings['whatsapp_number'] ?? '+2341234567890'); ?>', '<?php echo htmlspecialchars($property['title']); ?>', '<?php echo htmlspecialchars($property['location']); ?>')"
                        class="btn-whatsapp flex items-center justify-center"
                    >
                        <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                        Contact Us
                    </button>
                    <a href="properties.php" class="glass-card px-6 py-3 rounded-xl text-white hover:bg-white/20 transition-all text-center">
                        <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                        Back to Properties
                    </a>
                </div>
            </div>
            
            <!-- Property Status -->
            <div class="flex flex-wrap gap-4 mb-6">
                <span class="px-4 py-2 rounded-full text-sm font-semibold <?php 
                    echo $property['status'] === 'AVAILABLE' ? 'bg-green-500/20 text-green-300' : 
                        ($property['status'] === 'SOLD' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300');
                ?>">
                    <?php echo $property['status']; ?>
                </span>
                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-500/20 text-blue-300">
                    <?php echo $property['type']; ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Image Gallery -->
                <?php if (!empty($images)): ?>
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-white mb-6">Property Images</h2>
                    
                    <!-- Main Image -->
                    <?php if ($primary_image): ?>
                    <div class="mb-6">
                        <img 
                            src="<?php echo htmlspecialchars($primary_image['file_path']); ?>" 
                            alt="<?php echo htmlspecialchars($property['title']); ?>"
                            class="w-full h-96 object-cover rounded-xl"
                            id="main-image"
                        />
                    </div>
                    <?php endif; ?>
                    
                    <!-- Thumbnail Grid -->
                    <?php if (count($images) > 1): ?>
                    <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
                        <?php foreach ($images as $index => $image): ?>
                        <img 
                            src="<?php echo htmlspecialchars($image['file_path']); ?>" 
                            alt="<?php echo htmlspecialchars($image['file_name'] ?? 'Property Image'); ?>"
                            class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                            onclick="changeMainImage('<?php echo htmlspecialchars($image['file_path']); ?>')"
                        />
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Property Description -->
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-white mb-4">Description</h2>
                    <div class="text-gray-300 leading-relaxed text-justify break-words whitespace-pre-wrap">
                        <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                    </div>
                </div>

                <!-- Features -->
                <?php if (!empty($features)): ?>
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-white mb-4">Features</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($features as $feature): ?>
                        <div class="flex items-center text-gray-300">
                            <i data-lucide="check" class="w-5 h-5 text-green-400 mr-3"></i>
                            <?php echo htmlspecialchars($feature); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Video -->
                <?php if (!empty($videos)): ?>
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-white mb-4">Property Video</h2>
                    <div class="space-y-4">
                        <?php foreach ($videos as $video): ?>
                        <video controls class="w-full rounded-xl">
                            <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- External Video URL -->
                <?php if (!empty($property['video_url'])): ?>
                <div class="glass-card p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-white mb-4">Property Video</h2>
                    <div class="aspect-video rounded-xl overflow-hidden">
                        <iframe 
                            src="<?php echo htmlspecialchars($property['video_url']); ?>" 
                            class="w-full h-full"
                            frameborder="0" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Property Details -->
                <div class="glass-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Property Details</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Bedrooms</span>
                            <span class="text-white font-semibold"><?php echo $property['bedrooms']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Bathrooms</span>
                            <span class="text-white font-semibold"><?php echo $property['bathrooms']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Area</span>
                            <span class="text-white font-semibold"><?php echo number_format($property['area']); ?> sq ft</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Type</span>
                            <span class="text-white font-semibold"><?php echo $property['type']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Status</span>
                            <span class="text-white font-semibold <?php 
                                echo $property['status'] === 'AVAILABLE' ? 'text-green-400' : 
                                    ($property['status'] === 'SOLD' ? 'text-red-400' : 'text-yellow-400');
                            ?>"><?php echo $property['status']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="glass-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        <div class="flex items-center text-gray-300">
                            <i data-lucide="phone" class="w-5 h-5 mr-3 text-blue-400"></i>
                            <?php echo htmlspecialchars($settings['contact_phone'] ?? '+234 (0) 123-456-7890'); ?>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i data-lucide="mail" class="w-5 h-5 mr-3 text-blue-400"></i>
                            <?php echo htmlspecialchars($settings['contact_email'] ?? 'info@premiumrealestate.ng'); ?>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i data-lucide="map-pin" class="w-5 h-5 mr-3 text-blue-400"></i>
                            <?php echo htmlspecialchars($settings['contact_address'] ?? '123 Victoria Island, Lagos, Nigeria'); ?>
                        </div>
                    </div>
                    
                    <button 
                        onclick="openWhatsApp('<?php echo htmlspecialchars($settings['whatsapp_number'] ?? '+2341234567890'); ?>', '<?php echo htmlspecialchars($property['title']); ?>', '<?php echo htmlspecialchars($property['location']); ?>')"
                        class="btn-whatsapp w-full mt-4 flex items-center justify-center"
                    >
                        <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                        Contact via WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(imageSrc) {
    const mainImage = document.getElementById('main-image');
    if (mainImage) {
        mainImage.src = imageSrc;
    }
}

function openWhatsApp(phone, property, location) {
    const message = `Hi! I'm interested in the property: ${property} in ${location}. Please provide more information.`;
    const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
