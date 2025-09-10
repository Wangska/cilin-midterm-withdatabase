<?php
// Database setup script
$host = "localhost";
$username = "root";
$password = "";
$db_name = "notes_system";

try {
    // Create connection without database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute schema
    $schema = file_get_contents('database/schema.sql');
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Database setup completed successfully!<br>";
    echo "📊 Database '$db_name' has been created with all necessary tables.<br>";
    echo "👤 Demo user created with credentials: username=demo, password=password<br>";
    echo "<br>🚀 You can now <a href='index.php'>start using the application</a>";
    
} catch(PDOException $e) {
    echo "❌ Error setting up database: " . $e->getMessage();
}
?>
