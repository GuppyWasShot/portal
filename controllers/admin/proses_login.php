<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';

// Create Auth model
$auth = new Auth();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/login.php?error=invalid_request");
    exit();
}

// Get input
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$ip_address = $_SERVER['REMOTE_ADDR'];

// Attempt login using Auth model
$result = $auth->login($username, $password, $ip_address);

if ($result['success']) {
    // Login successful - redirect to dashboard
    header("Location: ../../views/admin/index.php");
    exit();
} else {
    // Login failed - redirect with error
    $error_message = $result['message'];
    
    // Check remaining attempts for better UX
    if ($error_message === 'gagal') {
        $remaining = $auth->getRemainingAttempts($ip_address);
        if ($remaining > 0) {
            header("Location: ../../views/admin/login.php?error=gagal&sisa=$remaining");
        } else {
            header("Location: ../../views/admin/login.php?error=terkunci");
        }
    } else {
        header("Location: ../../views/admin/login.php?error=$error_message");
    }
    exit();
}