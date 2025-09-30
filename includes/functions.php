<?php
// Helper function to get site settings
function getSiteSettings($db) {
    static $settings = null;
    
    if ($settings === null) {
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

// Helper function to get a specific setting
function getSiteSetting($db, $key, $default = '') {
    $settings = getSiteSettings($db);
    return $settings[$key] ?? $default;
}
?>
