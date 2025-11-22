<?php
/**
 * Quick Test - Ubah Password
 * Untuk test cepat apakah halaman bisa diakses
 */

session_start();

// Simulasi login admin untuk testing
if (!isset($_SESSION['admin_logged_in'])) {
    echo "<h2>Simulasi Login Admin</h2>";
    echo "<p>Untuk test halaman ubah password, kita perlu login dulu.</p>";
    echo "<form method='POST'>";
    echo "<button type='submit' name='simulate_login'>Simulasi Login sebagai Admin</button>";
    echo "</form>";
    
    if (isset($_POST['simulate_login'])) {
        // Set session admin (HANYA UNTUK TESTING!)
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['id_admin'] = 1; // Sesuaikan dengan ID admin yang ada
        $_SESSION['username'] = 'administrator';
        echo "<p style='color: green;'>✓ Session admin di-set. <a href='views/admin/ubah_password.php'>Klik di sini untuk ke halaman Ubah Password</a></p>";
    }
    exit;
}

echo "<h2>Session Status</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p><a href='views/admin/ubah_password.php'>Ke Halaman Ubah Password</a></p>";
echo "<p><a href='debug_ubah_password.php'>Ke Debug Tool</a></p>";
?>
