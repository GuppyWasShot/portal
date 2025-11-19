<?php
/**
 * Debug Login - Cek masalah login
 * Akses: http://localhost/portal_tpl/debug_login.php
 */

echo "<h1>Debug Login Admin</h1>";

// Test 1: Database Connection
echo "<h2>1. Test Database Connection</h2>";
try {
    require_once __DIR__ . '/../../app/autoload.php';
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection berhasil<br>";
    
    // Test query
    $result = $db->query("SELECT COUNT(*) as total FROM tbl_admin");
    $row = $result->fetch_assoc();
    echo "✅ Total admin di database: " . $row['total'] . "<br>";
    
    if ($row['total'] == 0) {
        echo "⚠️ <strong>PERINGATAN: Tidak ada admin di database!</strong><br>";
        echo "Silakan buat admin dengan menjalankan query SQL berikut:<br>";
        echo "<pre>";
        echo "INSERT INTO tbl_admin (username, password) VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "');";
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    die();
}

// Test 2: Cek Tabel Admin
echo "<h2>2. Cek Tabel Admin</h2>";
try {
    $result = $db->query("SELECT id_admin, username FROM tbl_admin LIMIT 5");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id_admin'] . "</td><td>" . htmlspecialchars($row['username']) . "</td></tr>";
    }
    echo "</table>";
    echo "✅ Tabel admin berhasil diakses<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Cek Password Hash
echo "<h2>3. Test Password Hash</h2>";
try {
    $result = $db->query("SELECT id_admin, username, password FROM tbl_admin LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $stored_hash = $row['password'];
        echo "✅ Password hash ditemukan untuk user: " . htmlspecialchars($row['username']) . "<br>";
        echo "Hash: " . substr($stored_hash, 0, 20) . "...<br>";
        
        // Test verify
        $test_password = 'admin123'; // Ganti dengan password yang benar
        if (password_verify($test_password, $stored_hash)) {
            echo "✅ Password 'admin123' cocok dengan hash!<br>";
        } else {
            echo "⚠️ Password 'admin123' TIDAK cocok. Coba password lain atau reset password.<br>";
        }
    } else {
        echo "⚠️ Tidak ada admin di database untuk di-test<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 4: Cek Functions
echo "<h2>4. Test Functions</h2>";
require_once __DIR__ . '/../../config/db_connect.php';
if (function_exists('logLoginAttempt')) {
    echo "✅ Function logLoginAttempt() tersedia<br>";
} else {
    echo "❌ Function logLoginAttempt() TIDAK ditemukan<br>";
}

if (function_exists('isIPLocked')) {
    echo "✅ Function isIPLocked() tersedia<br>";
} else {
    echo "❌ Function isIPLocked() TIDAK ditemukan<br>";
}

if (function_exists('resetFailedAttempts')) {
    echo "✅ Function resetFailedAttempts() tersedia<br>";
} else {
    echo "❌ Function resetFailedAttempts() TIDAK ditemukan<br>";
}

// Test 5: Cek IP Lock
echo "<h2>5. Test IP Lock</h2>";
$ip_address = $_SERVER['REMOTE_ADDR'];
echo "IP Address Anda: " . $ip_address . "<br>";
try {
    $is_locked = isIPLocked($db, $ip_address);
    if ($is_locked) {
        echo "⚠️ IP Anda sedang TERKUNCI (5x gagal dalam 10 menit terakhir)<br>";
        echo "Solusi: Tunggu 10 menit atau reset di database:<br>";
        echo "<pre>DELETE FROM tbl_admin_logs WHERE ip_address = '$ip_address' AND status = 'Failed';</pre>";
    } else {
        echo "✅ IP Anda tidak terkunci<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 6: Cek Session
echo "<h2>6. Test Session</h2>";
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    echo "✅ Session admin_logged_in: " . ($_SESSION['admin_logged_in'] ? 'true' : 'false') . "<br>";
    if (isset($_SESSION['admin_username'])) {
        echo "✅ Username: " . htmlspecialchars($_SESSION['admin_username']) . "<br>";
    }
} else {
    echo "ℹ️ Belum ada session login<br>";
}

echo "<hr>";
echo "<h2>📝 Langkah Troubleshooting</h2>";
echo "<ol>";
echo "<li>Pastikan ada admin di database <code>tbl_admin</code></li>";
echo "<li>Pastikan password sudah di-hash dengan <code>password_hash()</code></li>";
echo "<li>Cek apakah IP tidak terkunci (5x gagal = terkunci 10 menit)</li>";
echo "<li>Cek error log PHP untuk detail error</li>";
echo "<li>Pastikan semua function di <code>config/db_connect.php</code> tersedia</li>";
echo "</ol>";

echo "<h2>🔧 Buat Admin Baru</h2>";
echo "<p>Jika tidak ada admin, jalankan query SQL berikut:</p>";
echo "<pre>";
echo "INSERT INTO tbl_admin (username, password) VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "');";
echo "</pre>";
echo "<p>Atau reset password admin yang ada:</p>";
echo "<pre>";
echo "UPDATE tbl_admin SET password = '" . password_hash('admin123', PASSWORD_DEFAULT) . "' WHERE username = 'admin';";
echo "</pre>";

