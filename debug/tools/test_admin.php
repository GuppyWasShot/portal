<?php
/**
 * Test File - Cek apakah halaman admin berfungsi
 * Akses: http://localhost/portal_tpl/test_admin.php
 */

echo "<h1>Test Halaman Admin Portal TPL</h1>";

// Test 1: Autoloader
echo "<h2>1. Test Autoloader</h2>";
try {
    require_once __DIR__ . '/app/autoload.php';
    echo "✅ Autoloader berhasil di-load<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    die();
}

// Test 2: Database Class
echo "<h2>2. Test Database Class</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Database Class berhasil di-instantiate<br>";
    echo "✅ Koneksi database berhasil<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 3: File Structure Admin
echo "<h2>3. Test File Structure Admin</h2>";
$admin_files = [
    'views/admin/login.php' => 'Login Page',
    'views/admin/index.php' => 'Dashboard',
    'views/admin/kelola_karya.php' => 'Kelola Karya',
    'views/admin/form_tambah_karya.php' => 'Form Tambah Karya',
    'views/admin/form_edit_karya.php' => 'Form Edit Karya',
    'controllers/admin/proses_login.php' => 'Proses Login',
    'controllers/admin/logout.php' => 'Logout',
    'controllers/admin/proses_tambah_karya.php' => 'Proses Tambah Karya',
    'controllers/admin/proses_edit_karya.php' => 'Proses Edit Karya',
    'controllers/admin/hapus_karya.php' => 'Hapus Karya',
    'controllers/admin/change_status.php' => 'Ubah Status',
    'views/layouts/header_admin.php' => 'Header Admin Layout',
    'views/layouts/footer_admin.php' => 'Footer Admin Layout',
];

foreach ($admin_files as $file => $desc) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $desc: $file<br>";
    } else {
        echo "❌ $desc: $file TIDAK DITEMUKAN<br>";
    }
}

// Test 4: Path Check
echo "<h2>4. Test Path</h2>";
$paths_to_check = [
    'app/models/Database.php' => 'Database Class',
    'app/models/Karya.php' => 'Karya Class',
    'config/db_connect.php' => 'DB Config',
    'assets/styles.css' => 'CSS File',
    'assets/img/logo.svg' => 'Logo Image',
];

foreach ($paths_to_check as $file => $desc) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $desc: $file<br>";
    } else {
        echo "❌ $desc: $file TIDAK DITEMUKAN<br>";
    }
}

echo "<hr>";
echo "<h2>✅ Test Selesai!</h2>";
echo "<h3>📋 Cara Akses Admin:</h3>";
echo "<ol>";
echo "<li><strong>Entry Point:</strong> <a href='admin.php' target='_blank'>http://localhost/portal_tpl/admin.php</a></li>";
echo "<li><strong>Login Page:</strong> <a href='views/admin/login.php' target='_blank'>http://localhost/portal_tpl/views/admin/login.php</a></li>";
echo "<li><strong>Dashboard:</strong> <a href='views/admin/index.php' target='_blank'>http://localhost/portal_tpl/views/admin/index.php</a> (perlu login)</li>";
echo "</ol>";

echo "<h3>🔐 Kredensial Login:</h3>";
echo "<p>Gunakan username dan password admin yang sudah terdaftar di database <code>tbl_admin</code></p>";

echo "<h3>📝 Catatan:</h3>";
echo "<ul>";
echo "<li>Semua halaman admin memerlukan login</li>";
echo "<li>Jika belum login, akan otomatis redirect ke login page</li>";
echo "<li>IP lockout: 5 kali gagal = terkunci 10 menit</li>";
echo "<li>Lihat dokumentasi lengkap di <code>ADMIN_ACCESS.md</code></li>";
echo "</ul>";

