<?php
require_once 'config/config.php';

// Redirect based on login status
if (isLoggedIn()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
?>
