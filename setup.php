<?php
// Database setup script
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Read and execute SQL schema
    $sql = file_get_contents('database/schema.sql');
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }
    
    echo "✅ Database setup completed successfully!\n";
    echo "✅ Sample data inserted!\n";
    echo "✅ You can now access the website!\n";
    
} catch (Exception $e) {
    echo "❌ Error setting up database: " . $e->getMessage() . "\n";
    echo "Please check your database configuration in config/database.php\n";
}
?>
