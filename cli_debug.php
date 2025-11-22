<?php
/**
 * CLI Debug Script - Ubah Password
 * Run: php cli_debug.php
 */

// Simulate web environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

session_start();

require_once __DIR__ . '/app/autoload.php';

echo "==============================================\n";
echo "  DEBUG UBAH PASSWORD - CLI VERSION\n";
echo "==============================================\n\n";

// 1. Check Session
echo "1. SESSION CHECK\n";
echo "-------------------------------------------\n";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "✓ Admin logged in\n";
    echo "  - ID Admin: " . ($_SESSION['id_admin'] ?? 'NOT SET') . "\n";
    echo "  - Username: " . ($_SESSION['username'] ?? 'NOT SET') . "\n";
} else {
    echo "✗ Admin NOT logged in\n";
    echo "  Session data:\n";
    print_r($_SESSION);
}
echo "\n";

// 2. Database Connection
echo "2. DATABASE CONNECTION\n";
echo "-------------------------------------------\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connected\n";
    echo "  - Host: localhost\n";
    echo "  - Database: db_portal_tpl\n";
} catch (Exception $e) {
    echo "✗ Database connection failed\n";
    echo "  Error: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// 3. Check tbl_admin
echo "3. TABLE tbl_admin\n";
echo "-------------------------------------------\n";
$result = $db->query("SELECT id_admin, username, email, LEFT(password, 30) as password_preview FROM tbl_admin");
if ($result) {
    echo "✓ Table found\n";
    echo sprintf("%-5s %-20s %-30s %-35s\n", "ID", "Username", "Email", "Password (preview)");
    echo str_repeat("-", 95) . "\n";
    while ($row = $result->fetch_assoc()) {
        printf("%-5s %-20s %-30s %-35s\n", 
            $row['id_admin'], 
            $row['username'], 
            $row['email'] ?: '(empty)', 
            $row['password_preview'] . '...'
        );
    }
} else {
    echo "✗ Failed to query table\n";
}
echo "\n";

// 4. Check Files
echo "4. FILE CHECK\n";
echo "-------------------------------------------\n";
$files = [
    'views/admin/ubah_password.php' => 'Ubah Password Page',
    'controllers/admin/proses_ubah_password.php' => 'Process Controller',
];

foreach ($files as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✓ $desc\n";
        echo "  Path: $file\n";
        echo "  Size: " . filesize($path) . " bytes\n";
    } else {
        echo "✗ $desc NOT FOUND\n";
        echo "  Expected: $file\n";
    }
}
echo "\n";

// 5. Password Hash Test
echo "5. PASSWORD HASH TEST\n";
echo "-------------------------------------------\n";
$test_password = "admin123";
$test_hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "Test password: $test_password\n";
echo "Generated hash: $test_hash\n";
$verify = password_verify($test_password, $test_hash);
echo "Verify result: " . ($verify ? "✓ PASS" : "✗ FAIL") . "\n";
echo "\n";

// 6. Test with actual admin data
if (isset($_SESSION['id_admin'])) {
    echo "6. CURRENT ADMIN PASSWORD TEST\n";
    echo "-------------------------------------------\n";
    $stmt = $db->prepare("SELECT password FROM tbl_admin WHERE id_admin = ?");
    $stmt->bind_param("i", $_SESSION['id_admin']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    if ($admin) {
        echo "Current admin password hash:\n";
        echo $admin['password'] . "\n";
        echo "\nTest verify with 'admin123': ";
        $verify = password_verify('admin123', $admin['password']);
        echo ($verify ? "✓ MATCH" : "✗ NO MATCH") . "\n";
    }
    echo "\n";
}

// 7. Recommendations
echo "7. RECOMMENDATIONS\n";
echo "-------------------------------------------\n";
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "⚠ You need to login first\n";
    echo "  1. Go to: http://localhost/portal_tpl/views/admin/login.php\n";
    echo "  2. Login with your credentials\n";
    echo "  3. Then access ubah_password.php\n";
} else {
    echo "✓ Session is valid\n";
    echo "✓ You can access ubah_password.php\n";
    
    // Check if email is empty
    $stmt = $db->prepare("SELECT email FROM tbl_admin WHERE id_admin = ?");
    $stmt->bind_param("i", $_SESSION['id_admin']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    if (empty($admin['email'])) {
        echo "\n⚠ Email is empty for current admin\n";
        echo "  Run this SQL to fix:\n";
        echo "  UPDATE tbl_admin SET email = 'admin@portaltpl.ac.id' WHERE id_admin = " . $_SESSION['id_admin'] . ";\n";
    }
}

echo "\n==============================================\n";
echo "  DEBUG COMPLETE\n";
echo "==============================================\n";
?>
