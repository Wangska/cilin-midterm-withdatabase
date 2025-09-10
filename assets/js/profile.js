// Profile page JavaScript functionality

// Password validation
document.addEventListener('DOMContentLoaded', function() {
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // Enable/disable password fields based on current password
    function togglePasswordFields() {
        const hasCurrentPassword = currentPassword.value.length > 0;
        
        if (hasCurrentPassword) {
            newPassword.required = true;
            confirmPassword.required = true;
        } else {
            newPassword.required = false;
            confirmPassword.required = false;
            newPassword.value = '';
            confirmPassword.value = '';
        }
    }
    
    // Real-time validation
    function validatePasswords() {
        const current = currentPassword.value;
        const newPass = newPassword.value;
        const confirm = confirmPassword.value;
        
        // Clear previous validation styles
        newPassword.classList.remove('error', 'success');
        confirmPassword.classList.remove('error', 'success');
        
        if (current.length > 0) {
            // New password length check
            if (newPass.length > 0) {
                if (newPass.length < 6) {
                    newPassword.classList.add('error');
                } else {
                    newPassword.classList.add('success');
                }
            }
            
            // Confirm password match check
            if (confirm.length > 0) {
                if (newPass === confirm && newPass.length >= 6) {
                    confirmPassword.classList.add('success');
                } else {
                    confirmPassword.classList.add('error');
                }
            }
        }
    }
    
    // Event listeners
    currentPassword.addEventListener('input', function() {
        togglePasswordFields();
        validatePasswords();
    });
    
    newPassword.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    // Form submission validation
    document.querySelector('.profile-form').addEventListener('submit', function(e) {
        const current = currentPassword.value;
        const newPass = newPassword.value;
        const confirm = confirmPassword.value;
        
        // If any password field is filled, all must be filled
        if ((current || newPass || confirm) && (!current || !newPass || !confirm)) {
            e.preventDefault();
            alert('Please fill in all password fields to change your password, or leave all blank to keep current password.');
            return false;
        }
        
        // Check password match
        if (newPass && confirm && newPass !== confirm) {
            e.preventDefault();
            alert('New passwords do not match.');
            return false;
        }
        
        // Check password length
        if (newPass && newPass.length < 6) {
            e.preventDefault();
            alert('New password must be at least 6 characters long.');
            return false;
        }
    });
});

// Image preview function (already exists in profile.php)
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImagePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// User menu toggle function (already exists in profile.php)
function toggleUserMenu() {
    document.getElementById('userDropdown').classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    if (!userMenu.contains(event.target)) {
        document.getElementById('userDropdown').classList.remove('show');
    }
});
