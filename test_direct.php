<?php
// Direct test without any includes that might cause HTML output
session_start();

// Set up database connection directly
try {
    $pdo = new PDO("mysql:host=localhost;dbname=notes_system", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if logged in
    if (!isset($_SESSION['user_id'])) {
        // Try to login demo user
        $stmt = $pdo->prepare("SELECT id, username, profile_image FROM users WHERE username = ? AND password_hash = ?");
        $demo_password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $stmt->execute(['demo', $demo_password_hash]);
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profile_image'] = $user['profile_image'];
        }
    }
    
    if (isset($_SESSION['user_id'])) {
        // Test note creation
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $_SESSION['user_id'],
            'Test Note Direct',
            'This is a direct test note',
            '#e6f3ff'
        ]);
        
        if ($result) {
            $note_id = $pdo->lastInsertId();
            echo "✅ Note created successfully! ID: $note_id<br>";
            
            // Clean up
            $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
            $stmt->execute([$note_id]);
            echo "🧹 Test note cleaned up<br>";
        } else {
            echo "❌ Failed to create note<br>";
        }
    } else {
        echo "❌ Not logged in and couldn't login demo user<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='dashboard.php'>Go to Dashboard</a>";
?>
