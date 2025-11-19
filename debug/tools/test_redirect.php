<?php
/**
 * Test Redirect Path
 * Akses: http://localhost/portal_tpl/test_redirect.php
 */

echo "<h1>Test Redirect Path</h1>";

// Simulasi dari controllers/admin/logout.php
$current_dir = __DIR__ . '/controllers/admin';
$target_file = $current_dir . '/../../views/admin/login.php';

echo "<h2>1. Path Test</h2>";
echo "Current dir: " . $current_dir . "<br>";
echo "Target file: " . $target_file . "<br>";
echo "File exists: " . (file_exists($target_file) ? '✅ Yes' : '❌ No') . "<br>";

// Test relative path
$relative = '../../views/admin/login.php';
echo "<br>Relative path: " . $relative . "<br>";

// Test dengan realpath
$realpath = realpath($current_dir . '/' . $relative);
echo "Realpath result: " . ($realpath ? $realpath : '❌ Not found') . "<br>";

// Test URL path
echo "<h2>2. URL Path Test</h2>";
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$url_path = $base_url . '/views/admin/login.php';
echo "Base URL: " . $base_url . "<br>";
echo "URL path: " . $url_path . "<br>";

// Test dari controllers/admin/
echo "<h2>3. Test dari controllers/admin/</h2>";
$from_controllers = __DIR__ . '/controllers/admin';
$to_views = __DIR__ . '/views/admin/login.php';

// Calculate relative path
$from_parts = explode('/', trim($from_controllers, '/'));
$to_parts = explode('/', trim($to_views, '/'));

// Find common path
$common = 0;
for ($i = 0; $i < min(count($from_parts), count($to_parts)); $i++) {
    if ($from_parts[$i] === $to_parts[$i]) {
        $common++;
    } else {
        break;
    }
}

// Build relative path
$relative_parts = [];
$up_levels = count($from_parts) - $common;
for ($i = 0; $i < $up_levels; $i++) {
    $relative_parts[] = '..';
}
for ($i = $common; $i < count($to_parts); $i++) {
    $relative_parts[] = $to_parts[$i];
}

$calculated_relative = implode('/', $relative_parts);
echo "Calculated relative: " . $calculated_relative . "<br>";
echo "Expected: ../../views/admin/login.php<br>";
echo "Match: " . ($calculated_relative === '../../views/admin/login.php' ? '✅ Yes' : '❌ No') . "<br>";

// Test actual file
echo "<h2>4. Actual File Test</h2>";
$test_path = __DIR__ . '/controllers/admin/../../views/admin/login.php';
echo "Test path: " . $test_path . "<br>";
echo "File exists: " . (file_exists($test_path) ? '✅ Yes' : '❌ No') . "<br>";

// Normalize path
$normalized = realpath(__DIR__ . '/controllers/admin');
$normalized_target = realpath(__DIR__ . '/views/admin/login.php');
echo "<br>Normalized from: " . $normalized . "<br>";
echo "Normalized to: " . $normalized_target . "<br>";

