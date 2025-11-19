<?php
/**
 * Test Login Process - Simulasi proses login
 * Akses: http://localhost/portal_tpl/test_login_process.php
 */

session_start();
require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/config/db_connect.php';

$db = Database::getInstance()->getConnection();
$conn = $db;

echo "<h1>Test Login Process</h1>";

// Simulasi data POST
$_POST['username'] = 'admin';
$_POST['password'] = 'admin123';
$username = trim($_POST['username']);
$password = $_POST['password'];

echo "<h2>1. Test Query</h2>";
$stmt = $conn->prepare("SELECT id_admin, username, password FROM tbl_admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    echo "✅ Username ditemukan: " . htmlspecialchars($admin['username']) . "<br>";
    echo "✅ ID Admin: " . $admin['id_admin'] . "<br>";
    
    echo "<h2>2. Test Password Verify</h2>";
    if (password_verify($password, $admin['password'])) {
        echo "✅ Password cocok!<br>";
        
        echo "<h2>3. Test Session</h2>";
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_username'] = $admin['username'];
        
        echo "✅ Session ter-set:<br>";
        echo "- admin_logged_in: " . ($_SESSION['admin_logged_in'] ? 'true' : 'false') . "<br>";
        echo "- admin_id: " . $_SESSION['admin_id'] . "<br>";
        echo "- admin_username: " . htmlspecialchars($_SESSION['admin_username']) . "<br>";
        
        echo "<h2>4. Test Redirect Path</h2>";
        $redirect_path = '../../views/admin/index.php';
        echo "Redirect path: " . $redirect_path . "<br>";
        $full_path = __DIR__ . '/' . $redirect_path;
        if (file_exists($full_path)) {
            echo "✅ File target ada: " . $full_path . "<br>";
        } else {
            echo "❌ File target TIDAK ada: " . $full_path . "<br>";
            // Test dengan path absolut
            $abs_path = __DIR__ . '/views/admin/index.php';
            if (file_exists($abs_path)) {
                echo "✅ File target ada dengan path absolut: " . $abs_path . "<br>";
            }
        }
        
        echo "<h2>5. Test Functions</h2>";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        echo "IP Address: " . $ip_address . "<br>";
        
        try {
            logLoginAttempt($conn, $username, $ip_address, 'Success');
            echo "✅ logLoginAttempt() berhasil<br>";
            
            resetFailedAttempts($conn, $ip_address);
            echo "✅ resetFailedAttempts() berhasil<br>";
            
            logActivity($conn, $admin['id_admin'], $admin['username'], 'Login ke sistem');
            echo "✅ logActivity() berhasil<br>";
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "<br>";
        }
        
        echo "<hr>";
        echo "<h2>✅ Login Process BERHASIL!</h2>";
        echo "<p>Session sudah ter-set. Silakan coba akses:</p>";
        echo "<a href='views/admin/index.php' target='_blank'>Dashboard Admin</a><br>";
        echo "<p>Atau cek session di browser developer tools.</p>";
        
    } else {
        echo "❌ Password TIDAK cocok!<br>";
    }
} else {
    echo "❌ Username tidak ditemukan!<br>";
}

$stmt->close();

echo "<hr>";
echo "<h2>📝 Next Steps</h2>";
echo "<ol>";
echo "<li>Jika semua test di atas ✅, berarti proses login seharusnya berfungsi</li>";
echo "<li>Coba login di: <a href='views/admin/login.php'>Login Page</a></li>";
echo "<li>Jika masih error, cek browser console untuk error JavaScript</li>";
echo "<li>Cek Network tab di browser untuk melihat response dari proses_login.php</li>";
echo "</ol>";

