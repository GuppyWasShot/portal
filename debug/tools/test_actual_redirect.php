<?php
/**
 * Test Actual Redirect - Simulasi redirect dari logout dan change_status
 * Akses: http://localhost/portal_tpl/test_actual_redirect.php
 */

echo "<h1>Test Actual Redirect</h1>";

// Simulasi dari controllers/admin/logout.php
echo "<h2>1. Test Logout Redirect</h2>";
$_SERVER['PHP_SELF'] = '/portal_tpl/controllers/admin/logout.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = '';

$script_path = dirname($_SERVER['PHP_SELF']); // /portal_tpl/controllers/admin
$base_path = dirname(dirname($script_path)); // /portal_tpl
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$redirect_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_path . '/views/admin/login.php?error=logout';

echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "script_path: " . $script_path . "<br>";
echo "base_path: " . $base_path . "<br>";
echo "<strong>Generated URL: " . $redirect_url . "</strong><br>";
echo "Expected: http://localhost/portal_tpl/views/admin/login.php?error=logout<br>";
echo "Match: " . ($redirect_url === 'http://localhost/portal_tpl/views/admin/login.php?error=logout' ? '✅ Yes' : '❌ No') . "<br>";

// Simulasi dari controllers/admin/change_status.php
echo "<h2>2. Test Change Status Redirect</h2>";
$_SERVER['PHP_SELF'] = '/portal_tpl/controllers/admin/change_status.php';

$script_path = dirname($_SERVER['PHP_SELF']); // /portal_tpl/controllers/admin
$base_path = dirname(dirname($script_path)); // /portal_tpl
$redirect_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_path . '/views/admin/kelola_karya.php?success=status';

echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "script_path: " . $script_path . "<br>";
echo "base_path: " . $base_path . "<br>";
echo "<strong>Generated URL: " . $redirect_url . "</strong><br>";
echo "Expected: http://localhost/portal_tpl/views/admin/kelola_karya.php?success=status<br>";
echo "Match: " . ($redirect_url === 'http://localhost/portal_tpl/views/admin/kelola_karya.php?success=status' ? '✅ Yes' : '❌ No') . "<br>";

// Test file existence
echo "<h2>3. Test File Existence</h2>";
$base_dir = __DIR__;
$login_file = $base_dir . '/views/admin/login.php';
$kelola_file = $base_dir . '/views/admin/kelola_karya.php';

echo "Login file: " . ($login_file) . "<br>";
echo "Exists: " . (file_exists($login_file) ? '✅ Yes' : '❌ No') . "<br>";
echo "Kelola file: " . ($kelola_file) . "<br>";
echo "Exists: " . (file_exists($kelola_file) ? '✅ Yes' : '❌ No') . "<br>";

echo "<hr>";
echo "<h2>✅ Test Selesai!</h2>";
echo "<p>Jika URL yang di-generate sesuai dengan expected, maka redirect seharusnya berfungsi.</p>";
echo "<p>Jika masih error, kemungkinan masalahnya:</p>";
echo "<ul>";
echo "<li>Base path calculation salah</li>";
echo "<li>URL encoding issue</li>";
echo "<li>Browser cache</li>";
echo "<li>Session issue</li>";
echo "</ul>";

