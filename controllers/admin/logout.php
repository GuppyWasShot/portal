<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';

// Create Auth model
$auth = new Auth();

// Logout using Auth model (handles activity logging and session destruction)
$auth->logout();

// Redirect to login page
header("Location: ../../views/admin/login.php?message=logout_success");
exit();