<?php
/**
 * DEBUG FILE - Ubah Password
 * File ini untuk debugging masalah ubah password
 * Akses: http://localhost/portal_tpl/debug_ubah_password.php
 */

session_start();
require_once __DIR__ . '/app/autoload.php';

echo "<h1>Debug Ubah Password</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .section { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    table td, table th { padding: 8px; border: 1px solid #ddd; text-align: left; }
    table th { background: #4CAF50; color: white; }
</style>";

// 1. Cek Session
echo "<div class='section'>";
echo "<h2>1. Session Check</h2>";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "<p class='success'>✓ Admin sudah login</p>";
    echo "<table>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    echo "<tr><td>admin_logged_in</td><td>" . ($_SESSION['admin_logged_in'] ? 'true' : 'false') . "</td></tr>";
    echo "<tr><td>id_admin</td><td>" . ($_SESSION['id_admin'] ?? 'NOT SET') . "</td></tr>";
    echo "<tr><td>username</td><td>" . ($_SESSION['username'] ?? 'NOT SET') . "</td></tr>";
    echo "</table>";
} else {
    echo "<p class='error'>✗ Admin belum login</p>";
    echo "<p>Silakan login terlebih dahulu di: <a href='views/admin/login.php'>Login Admin</a></p>";
}
echo "</div>";

// 2. Cek Database Connection
echo "<div class='section'>";
echo "<h2>2. Database Connection</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✓ Database connection berhasil</p>";
    echo "<p class='info'>Host: localhost | Database: db_portal_tpl</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection gagal</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
echo "</div>";

// 3. Cek Tabel Admin
echo "<div class='section'>";
echo "<h2>3. Tabel tbl_admin</h2>";
if (isset($db)) {
    $result = $db->query("SELECT id_admin, username, email, LEFT(password, 20) as password_preview FROM tbl_admin");
    if ($result) {
        echo "<p class='success'>✓ Tabel tbl_admin ditemukan</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password (preview)</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_admin']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['password_preview']) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ Gagal query tabel tbl_admin</p>";
    }
}
echo "</div>";

// 4. Cek File Proses Ubah Password
echo "<div class='section'>";
echo "<h2>4. File Check</h2>";
$files_to_check = [
    'views/admin/ubah_password.php' => 'Halaman Ubah Password',
    'controllers/admin/proses_ubah_password.php' => 'Controller Proses Ubah Password',
];

foreach ($files_to_check as $file => $desc) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "<p class='success'>✓ $desc: <code>$file</code></p>";
        echo "<p class='info'>Size: " . filesize($full_path) . " bytes | Readable: " . (is_readable($full_path) ? 'Yes' : 'No') . "</p>";
    } else {
        echo "<p class='error'>✗ $desc tidak ditemukan: <code>$file</code></p>";
    }
}
echo "</div>";

// 5. Test Password Hash & Verify
echo "<div class='section'>";
echo "<h2>5. Password Hash Test</h2>";
$test_password = "admin123";
$test_hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "<p><strong>Test Password:</strong> $test_password</p>";
echo "<p><strong>Generated Hash:</strong></p>";
echo "<pre>" . htmlspecialchars($test_hash) . "</pre>";

$verify_result = password_verify($test_password, $test_hash);
echo "<p><strong>Verify Test:</strong> " . ($verify_result ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>") . "</p>";

// Test dengan hash yang ada di database
if (isset($db) && isset($_SESSION['id_admin'])) {
    $stmt = $db->prepare("SELECT password FROM tbl_admin WHERE id_admin = ?");
    $stmt->bind_param("i", $_SESSION['id_admin']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    if ($admin) {
        echo "<p><strong>Password Hash dari Database (current user):</strong></p>";
        echo "<pre>" . htmlspecialchars($admin['password']) . "</pre>";
    }
}
echo "</div>";

// 6. Simulate Form Submission
echo "<div class='section'>";
echo "<h2>6. Test Form Ubah Password</h2>";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "<form method='POST' action='controllers/admin/proses_ubah_password.php' style='max-width: 500px;'>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label>Password Lama:</label><br>";
    echo "<input type='password' name='password_lama' required style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label>Password Baru:</label><br>";
    echo "<input type='password' name='password_baru' required minlength='6' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label>Konfirmasi Password:</label><br>";
    echo "<input type='password' name='konfirmasi_password' required minlength='6' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<button type='submit' style='background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Test Ubah Password</button>";
    echo "</form>";
} else {
    echo "<p class='error'>Login terlebih dahulu untuk test form</p>";
}
echo "</div>";

// 7. Check PHP Version & Extensions
echo "<div class='section'>";
echo "<h2>7. PHP Environment</h2>";
echo "<table>";
echo "<tr><th>Item</th><th>Value</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>password_hash available</td><td>" . (function_exists('password_hash') ? '✓ Yes' : '✗ No') . "</td></tr>";
echo "<tr><td>password_verify available</td><td>" . (function_exists('password_verify') ? '✓ Yes' : '✗ No') . "</td></tr>";
echo "<tr><td>mysqli extension</td><td>" . (extension_loaded('mysqli') ? '✓ Loaded' : '✗ Not loaded') . "</td></tr>";
echo "<tr><td>session.save_path</td><td>" . session_save_path() . "</td></tr>";
echo "</table>";
echo "</div>";

// 8. Recent Error Logs (if accessible)
echo "<div class='section'>";
echo "<h2>8. Error Logs</h2>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    echo "<p class='info'>Error log location: $error_log</p>";
    $last_errors = shell_exec("tail -20 $error_log 2>&1");
    if ($last_errors) {
        echo "<pre>" . htmlspecialchars($last_errors) . "</pre>";
    }
} else {
    echo "<p class='info'>Error log tidak ditemukan atau tidak dapat diakses</p>";
    echo "<p>Check PHP error_log setting di php.ini</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>9. Troubleshooting Tips</h2>";
echo "<ul>";
echo "<li>Pastikan sudah login sebagai admin</li>";
echo "<li>Cek apakah file <code>proses_ubah_password.php</code> ada dan readable</li>";
echo "<li>Pastikan password lama yang diinput benar</li>";
echo "<li>Cek browser console untuk error JavaScript</li>";
echo "<li>Cek network tab di browser DevTools untuk melihat response dari server</li>";
echo "<li>Pastikan session tidak expired</li>";
echo "</ul>";
echo "</div>";
?>
