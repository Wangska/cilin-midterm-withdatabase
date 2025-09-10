<?php
// Test script for the notes API
require_once 'config/config.php';
require_once 'classes/User.php';

// Force login for testing (you can remove this after testing)
if (!isLoggedIn()) {
    // Try to log in the demo user for testing
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    if ($user->login('demo', 'password')) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['profile_image'] = $user->profile_image;
        echo "✅ Logged in as demo user for testing<br><br>";
    } else {
        echo "❌ Could not log in demo user. Make sure to run setup.php first.<br>";
        echo "<a href='setup.php'>Run Setup</a><br><br>";
    }
}

if (isLoggedIn()) {
    echo "<h2>🧪 API Test</h2>";
    echo "Testing note creation API...<br><br>";
    
    // Simulate the API call
    $_POST['api_test'] = true;
    
    // Test data
    $test_data = [
        'action' => 'create',
        'title' => 'Test Note from API Test',
        'content' => 'This is a test note created from the API test script.',
        'color' => '#e6f3ff'
    ];
    
    // Simulate the JSON input that the API expects
    $json_input = json_encode($test_data);
    
    echo "<strong>Test Data:</strong><br>";
    echo "<pre>" . print_r($test_data, true) . "</pre>";
    
    // Include the API logic directly
    require_once 'classes/Note.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $note = new Note($db);
    
    $userId = $_SESSION['user_id'];
    
    echo "<strong>User ID:</strong> $userId<br><br>";
    
    try {
        $title = sanitize($test_data['title'] ?? '');
        $content = sanitize($test_data['content'] ?? '');
        $color = sanitize($test_data['color'] ?? '#ffffff');
        
        echo "<strong>Sanitized Data:</strong><br>";
        echo "Title: $title<br>";
        echo "Content: $content<br>";
        echo "Color: $color<br><br>";
        
        if (empty($title) || empty($content)) {
            echo "❌ Title and content are required<br>";
        } else {
            echo "✅ Data validation passed<br>";
            
            if ($note->create($userId, $title, $content, $color)) {
                echo "✅ Note created successfully! (ID: " . $note->id . ")<br>";
                
                // Test fetching the note
                $notes = $note->getUserNotes($userId);
                echo "✅ User has " . count($notes) . " notes total<br>";
                
                // Clean up test note
                $note->delete($note->id, $userId);
                echo "🧹 Test note deleted<br>";
                
            } else {
                echo "❌ Failed to create note<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Exception occurred: " . $e->getMessage() . "<br>";
    }
    
    echo "<hr>";
    echo "<h3>📋 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>If the test above passed, the issue is likely in the JavaScript or AJAX call</li>";
    echo "<li>Check the browser's developer console (F12) for JavaScript errors</li>";
    echo "<li>Check the Network tab to see the actual API request/response</li>";
    echo "<li>Try creating a note from the <a href='dashboard.php'>dashboard</a></li>";
    echo "</ol>";
    
} else {
    echo "❌ Not logged in. Please run the debug first.";
}

echo "<br><br>";
echo "<a href='debug.php'>← Back to Debug</a> | ";
echo "<a href='dashboard.php'>Go to Dashboard</a>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
h2, h3 { color: #333; }
pre { background: #fff; padding: 10px; border-left: 4px solid #667eea; }
</style>
