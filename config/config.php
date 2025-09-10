<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (but don't display HTML errors for API calls)
error_reporting(E_ALL);
if (strpos($_SERVER['REQUEST_URI'], 'api/') !== false) {
    ini_set('display_errors', 0);
    ini_set('html_errors', 0);
} else {
    ini_set('display_errors', 1);
}

// Application constants
define('APP_NAME', 'Notes System');
define('BASE_URL', 'http://localhost/cilin-midterm-withdatabase/');
define('UPLOAD_PATH', 'uploads/profiles/');

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Include database connection
require_once 'config/database.php';

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
