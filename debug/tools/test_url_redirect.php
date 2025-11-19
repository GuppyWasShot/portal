<?php
/**
 * Test URL Redirect Generation
 * Akses: http://localhost/portal_tpl/test_url_redirect.php
 */

echo "<h1>Test URL Redirect Generation</h1>";

// Simulasi dari controllers/admin/logout.php
$_SERVER['PHP_SELF'] = '/portal_tpl/controllers/admin/logout.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = '';

echo "<h2>1. Test dari controllers/admin/logout.php</h2>";
$script_path = dirname($_SERVER['PHP_SELF']); // /portal_tpl/controllers/admin
$base_path = dirname(dirname($script_path)); // /portal_tpl
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$redirect_url = $protocol . '://' . $host . $base_path . '/views/admin/login.php?error=logout';

echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "script_path (dirname): " . $script_path . "<br>";
echo "base_path (dirname dirname): " . $base_path . "<br>";
echo "Protocol: " . $protocol . "<br>";
echo "Host: " . $host . "<br>";
echo "<strong>Generated URL: " . $redirect_url . "</strong><br>";
echo "<br>Expected: http://localhost/portal_tpl/views/admin/login.php?error=logout<br>";
echo "Match: " . ($redirect_url === 'http://localhost/portal_tpl/views/admin/login.php?error=logout' ? '✅ Yes' : '❌ No') . "<br>";

// Simulasi dari controllers/admin/change_status.php
echo "<h2>2. Test dari controllers/admin/change_status.php</h2>";
$_SERVER['PHP_SELF'] = '/portal_tpl/controllers/admin/change_status.php';
$script_path = dirname($_SERVER['PHP_SELF']); // /portal_tpl/controllers/admin
$base_path = dirname(dirname($script_path)); // /portal_tpl
$redirect_url = $protocol . '://' . $host . $base_path . '/views/admin/kelola_karya.php?success=status';

echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "script_path (dirname): " . $script_path . "<br>";
echo "base_path (dirname dirname): " . $base_path . "<br>";
echo "<strong>Generated URL: " . $redirect_url . "</strong><br>";
echo "<br>Expected: http://localhost/portal_tpl/views/admin/kelola_karya.php?success=status<br>";
echo "Match: " . ($redirect_url === 'http://localhost/portal_tpl/views/admin/kelola_karya.php?success=status' ? '✅ Yes' : '❌ No') . "<br>";

echo "<hr>";
echo "<h2>✅ Test Selesai!</h2>";
echo "<p>Jika URL yang di-generate sesuai dengan expected, maka redirect seharusnya berfungsi.</p>";
echo "<p>Silakan test logout dan change status di browser.</p>";

