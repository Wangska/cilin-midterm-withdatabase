<?php
// Clean output buffer to prevent any HTML from interfering
ob_clean();

require_once '../config/config.php';
require_once '../classes/Note.php';

// Disable HTML error output
ini_set('display_errors', 0);
ini_set('html_errors', 0);

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$note = new Note($db);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$action = $input['action'];
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'create':
            $title = sanitize($input['title'] ?? '');
            $content = sanitize($input['content'] ?? '');
            $color = sanitize($input['color'] ?? '#ffffff');
            
            if (empty($title) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Title and content are required']);
                exit;
            }
            
            if ($note->create($userId, $title, $content, $color)) {
                echo json_encode(['success' => true, 'message' => 'Note created successfully', 'id' => $note->id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create note']);
            }
            break;
            
        case 'update':
            $noteId = intval($input['id'] ?? 0);
            $title = sanitize($input['title'] ?? '');
            $content = sanitize($input['content'] ?? '');
            $color = sanitize($input['color'] ?? '#ffffff');
            
            if ($noteId <= 0 || empty($title) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
                exit;
            }
            
            if ($note->update($noteId, $userId, $title, $content, $color)) {
                echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update note or note not found']);
            }
            break;
            
        case 'delete':
            $noteId = intval($input['id'] ?? 0);
            
            if ($noteId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid note ID']);
                exit;
            }
            
            if ($note->delete($noteId, $userId)) {
                echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete note or note not found']);
            }
            break;
            
        case 'updateColor':
            $noteId = intval($input['id'] ?? 0);
            $color = sanitize($input['color'] ?? '#ffffff');
            
            if ($noteId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid note ID']);
                exit;
            }
            
            // Validate color format (hex)
            if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
                echo json_encode(['success' => false, 'message' => 'Invalid color format']);
                exit;
            }
            
            if ($note->updateColor($noteId, $userId, $color)) {
                echo json_encode(['success' => true, 'message' => 'Note color updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update note color or note not found']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error occurred', 'error' => $e->getMessage()]);
}
?>
