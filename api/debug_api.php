<?php
// Debug version of the notes API
require_once '../config/config.php';
require_once '../classes/Note.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Log the request
$log_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'session_id' => session_id(),
    'user_id' => $_SESSION['user_id'] ?? 'not set',
    'logged_in' => isLoggedIn() ? 'yes' : 'no'
];

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized', 
        'debug' => $log_data
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$note = new Note($db);

// Get raw input
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

$log_data['raw_input'] = $raw_input;
$log_data['parsed_input'] = $input;

if (!$input || !isset($input['action'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request', 
        'debug' => $log_data
    ]);
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
            
            $log_data['sanitized_data'] = [
                'title' => $title,
                'content' => $content,
                'color' => $color
            ];
            
            if (empty($title) || empty($content)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Title and content are required', 
                    'debug' => $log_data
                ]);
                exit;
            }
            
            if ($note->create($userId, $title, $content, $color)) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Note created successfully', 
                    'id' => $note->id,
                    'debug' => $log_data
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to create note', 
                    'debug' => $log_data
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false, 
                'message' => 'Action not implemented in debug version', 
                'debug' => $log_data
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Server error occurred: ' . $e->getMessage(), 
        'debug' => $log_data
    ]);
}
?>
