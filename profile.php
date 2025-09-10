<?php
require_once 'config/config.php';
require_once 'classes/User.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error = '';
$success = '';

// Get current user info
$user->getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $newProfileImage = null;
    
    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($filetype, $allowed)) {
            $newFileName = uniqid() . '.' . $filetype;
            $uploadPath = UPLOAD_PATH . $newFileName;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
                // Delete old profile image if it's not the default
                if ($user->profile_image != 'default-avatar.png' && file_exists(UPLOAD_PATH . $user->profile_image)) {
                    unlink(UPLOAD_PATH . $user->profile_image);
                }
                $newProfileImage = $newFileName;
            } else {
                $error = 'Failed to upload image.';
            }
        } else {
            $error = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
        }
    }
    
    // Validation
    if (empty($error)) {
        if (empty($username) || empty($email)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $passwordChanged = false;
            
            // Check if password change is requested
            if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $error = 'Please fill in all password fields to change your password.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                } else {
                    // Verify current password
                    $tempUser = new User($db);
                    if ($tempUser->login($_SESSION['username'], $current_password)) {
                        $passwordChanged = true;
                    } else {
                        $error = 'Current password is incorrect.';
                    }
                }
            }
            
            if (empty($error)) {
                if ($passwordChanged) {
                    if ($user->updateProfileWithPassword($_SESSION['user_id'], $username, $email, $new_password, $newProfileImage)) {
                        $_SESSION['username'] = $username;
                        if ($newProfileImage) {
                            $_SESSION['profile_image'] = $newProfileImage;
                        }
                        $success = 'Profile and password updated successfully!';
                        $user->getUserById($_SESSION['user_id']);
                    } else {
                        $error = 'Failed to update profile. Username or email might already exist.';
                    }
                } else {
                    if ($user->updateProfile($_SESSION['user_id'], $username, $email, $newProfileImage)) {
                        $_SESSION['username'] = $username;
                        if ($newProfileImage) {
                            $_SESSION['profile_image'] = $newProfileImage;
                        }
                        $success = 'Profile updated successfully!';
                        $user->getUserById($_SESSION['user_id']);
                    } else {
                        $error = 'Failed to update profile. Username or email might already exist.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(inputId + '-eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="m1 12 4-8 11 8-11 8-4-8z"/>
                    <path d="m9.5 7.5-.5 12.5 5-12.5-4.5 0z"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2><?php echo APP_NAME; ?></h2>
            </div>
            <div class="nav-menu">
                <div class="user-menu">
                    <div class="user-avatar" onclick="toggleUserMenu()">
                        <img src="<?php echo UPLOAD_PATH . ($user->profile_image ?: 'default-avatar.png'); ?>" alt="Profile">
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="profile-header">
            <h1><i class="fas fa-user"></i> My Profile</h1>
            <p>Manage your account settings and preferences</p>
        </div>

        <div class="profile-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="profile-image-section">
                    <div class="profile-image-container">
                        <img src="<?php echo UPLOAD_PATH . ($user->profile_image ?: 'default-avatar.png'); ?>" 
                             alt="Profile" class="profile-image" id="profileImagePreview">
                        <button type="button" class="profile-image-upload" onclick="document.getElementById('profile_image').click()">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                    </div>
                    <h3><?php echo htmlspecialchars($user->username); ?></h3>
                </div>
                
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Account Information</h4>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($user->username); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($user->email); ?>">
                    </div>
                </div>
                
                <div class="form-section">
                    <h4><i class="fas fa-lock"></i> Change Password</h4>
                    <p class="form-section-note">Leave blank if you don't want to change your password</p>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <div class="password-input-container">
                            <input type="password" id="current_password" name="current_password" 
                                   placeholder="Enter your current password">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('current_password')">
                                <svg id="current_password-eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="password-input-container">
                            <input type="password" id="new_password" name="new_password" 
                                   placeholder="Enter new password (at least 6 characters)">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('new_password')">
                                <svg id="new_password-eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="password-input-container">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   placeholder="Confirm new password">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                <svg id="confirm_password-eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="assets/js/profile.js"></script>
</body>
</html>
