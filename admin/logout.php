<?php
session_start();
include '../config/db_connect.php';

// Log aktivitas logout sebelum menghapus session
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_username'])) {
    logActivity(
        $conn, 
        $_SESSION['admin_id'], 
        $_SESSION['admin_username'], 
        'Logout dari sistem'
    );
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke login dengan pesan sukses
header("Location: login.php?error=logout");
exit();
?>