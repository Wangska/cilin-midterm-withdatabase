<?php
echo "<h2>🔧 System Check</h2>";

// 1. Check if database exists
echo "<h3>1. Database Check:</h3>";
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'notes_system'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database 'notes_system' exists<br>";
        
        // Connect to the database
        $pdo = new PDO("mysql:host=localhost;dbname=notes_system", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check tables
        $tables = ['users', 'notes', 'user_sessions'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' missing<br>";
            }
        }
        
        // Check if demo user exists
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE username = 'demo'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            echo "✅ Demo user exists<br>";
        } else {
            echo "❌ Demo user missing<br>";
        }
        
    } else {
        echo "❌ Database 'notes_system' does not exist<br>";
        echo "<strong>Solution:</strong> <a href='setup.php' style='background: #667eea; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Run Setup</a><br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
    echo "<strong>Make sure XAMPP MySQL is running!</strong><br>";
}

// 2. Check file permissions
echo "<h3>2. File Check:</h3>";
$files = [
    'config/config.php',
    'config/database.php', 
    'classes/Note.php',
    'classes/User.php',
    'api/notes.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists and readable<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// 3. Test session
echo "<h3>3. Session Check:</h3>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Sessions working<br>";
} else {
    echo "❌ Session problems<br>";
}

// 4. PHP Info
echo "<h3>4. PHP Info:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? "✅ Available" : "❌ Missing") . "<br>";

echo "<hr>";
echo "<h3>📋 Next Steps:</h3>";
echo "<ol>";
echo "<li>If database is missing: <a href='setup.php'><strong>Run Setup</strong></a></li>";
echo "<li>If XAMPP is not running: Start Apache + MySQL in XAMPP Control Panel</li>";
echo "<li>If all checks pass: <a href='test_direct.php'>Test Database Directly</a></li>";
echo "<li>Then try: <a href='login.php'>Login</a> → <a href='dashboard.php'>Dashboard</a></li>";
echo "</ol>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
h2, h3 { color: #333; }
a { color: #667eea; }
</style>
