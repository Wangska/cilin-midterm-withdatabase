<?php
// Ultra simple API test - no includes to avoid conflicts
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Start output buffering to catch any stray output
ob_start();

try {
    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Simple database connection test
    $pdo = new PDO("mysql:host=localhost;dbname=notes_system", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if we have user session
    if (!isset($_SESSION['user_id'])) {
        // Try to auto-login demo user for testing
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = 'demo' LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Demo user not found. Run setup.php']);
            exit;
        }
    }
    
    // Get the input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No action specified']);
        exit;
    }
    
    // Handle create note action
    if ($input['action'] === 'create') {
        $title = htmlspecialchars(trim($input['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars(trim($input['content'] ?? ''), ENT_QUOTES, 'UTF-8');
        $color = htmlspecialchars(trim($input['color'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8');
        
        if (empty($title) || empty($content)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Title and content required']);
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $title, $content, $color])) {
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Note created', 'id' => $pdo->lastInsertId()]);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to create note']);
        }
    }
    // Handle color update
    elseif ($input['action'] === 'updateColor') {
        $noteId = intval($input['id'] ?? 0);
        $color = htmlspecialchars(trim($input['color'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8');
        
        if ($noteId <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid note ID']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE notes SET color = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$color, $noteId, $_SESSION['user_id']])) {
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Color updated']);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to update color']);
        }
    }
    // Handle delete note
    elseif ($input['action'] === 'delete') {
        $noteId = intval($input['id'] ?? 0);
        
        if ($noteId <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid note ID']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$noteId, $_SESSION['user_id']])) {
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Note deleted']);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to delete note']);
        }
    }
    // Handle update note
    elseif ($input['action'] === 'update') {
        $noteId = intval($input['id'] ?? 0);
        $title = htmlspecialchars(trim($input['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars(trim($input['content'] ?? ''), ENT_QUOTES, 'UTF-8');
        $color = htmlspecialchars(trim($input['color'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8');
        
        if ($noteId <= 0 || empty($title) || empty($content)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ?, color = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$title, $content, $color, $noteId, $_SESSION['user_id']])) {
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Note updated']);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to update note']);
        }
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $input['action']]);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
