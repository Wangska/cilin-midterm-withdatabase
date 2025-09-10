<?php
// Debug script to help identify note creation issues
require_once 'config/config.php';
require_once 'classes/Note.php';
require_once 'classes/User.php';

echo "<h2>🔍 Debug Information</h2>";

// Check if we're logged in
echo "<h3>1. Session Status:</h3>";
if (session_status() === PHP_SESSION_NONE) {
    echo "❌ Session not started<br>";
} else {
    echo "✅ Session active<br>";
}

if (isLoggedIn()) {
    echo "✅ User is logged in (User ID: " . $_SESSION['user_id'] . ")<br>";
} else {
    echo "❌ User not logged in<br>";
}

// Check database connection
echo "<h3>2. Database Connection:</h3>";
try {
    $database = new Database();
    $db = $database->getConnection();
    if ($db) {
        echo "✅ Database connection successful<br>";
        
        // Check if tables exist
        $tables = ['users', 'notes', 'user_sessions'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' does not exist<br>";
            }
        }
        
        // Check if there are users
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Total users: " . $result['count'] . "<br>";
        
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test note creation if logged in
if (isLoggedIn()) {
    echo "<h3>3. Test Note Creation:</h3>";
    try {
        $note = new Note($db);
        $testResult = $note->create($_SESSION['user_id'], "Test Note", "This is a test note", "#ffffff");
        
        if ($testResult) {
            echo "✅ Note creation test successful (Note ID: " . $note->id . ")<br>";
            
            // Clean up test note
            $note->delete($note->id, $_SESSION['user_id']);
            echo "🧹 Test note cleaned up<br>";
        } else {
            echo "❌ Note creation test failed<br>";
        }
    } catch (Exception $e) {
        echo "❌ Note creation error: " . $e->getMessage() . "<br>";
    }
}

// Check file permissions
echo "<h3>4. File Permissions:</h3>";
$files_to_check = ['api/notes.php', 'classes/Note.php', 'config/config.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "✅ $file is readable<br>";
        } else {
            echo "❌ $file is not readable<br>";
        }
    } else {
        echo "❌ $file does not exist<br>";
    }
}

// Check uploads directory
echo "<h3>5. Uploads Directory:</h3>";
if (is_dir('uploads/profiles')) {
    if (is_writable('uploads/profiles')) {
        echo "✅ uploads/profiles directory is writable<br>";
    } else {
        echo "⚠️ uploads/profiles directory is not writable<br>";
    }
} else {
    echo "❌ uploads/profiles directory does not exist<br>";
}

echo "<h3>6. PHP Configuration:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Error Reporting: " . (error_reporting() ? "Enabled" : "Disabled") . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? "On" : "Off") . "<br>";

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>If database tables don't exist, run <a href='setup.php'>setup.php</a></li>";
echo "<li>If you're not logged in, go to <a href='login.php'>login.php</a></li>";
echo "<li>If all checks pass, the issue might be in the JavaScript. Check browser console for errors.</li>";
echo "</ol>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
h3 { color: #667eea; margin-top: 20px; }
</style>
