<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';

// Create Auth model
$auth = new Auth();
$auth->requireAuth();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/ubah_password.php?error=invalid_request");
    exit();
}

// Get input
$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validate required fields
if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
    header("Location: ../../views/admin/ubah_password.php?error=empty_field");
    exit();
}

// Check if new passwords match
if ($new_password !== $confirm_password) {
    header("Location: ../../views/admin/ubah_password.php?error=password_mismatch");
    exit();
}

// Get current admin
$currentAdmin = $auth->getCurrentAdmin();
if (!$currentAdmin || !isset($currentAdmin['id_admin'])) {
    header("Location: ../../views/admin/login.php?error=session_expired");
    exit();
}

// Attempt password change using Auth model
$result = $auth->changePassword(
    $currentAdmin['id_admin'],
    $old_password,
    $new_password
);

if ($result['success']) {
    header("Location: ../../views/admin/ubah_password.php?success=password_changed");
    exit();
} else {
    $error_message = $result['message'];
    header("Location: ../../views/admin/ubah_password.php?error=$error_message");
    exit();
}
