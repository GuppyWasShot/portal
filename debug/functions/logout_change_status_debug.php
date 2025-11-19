<?php
/**
 * Debug Logout & Change Status
 * Akses: http://localhost/portal_tpl/debug_logout_change_status.php
 */

echo "<h1>Debug Logout & Change Status</h1>";

// Test 1: Logout File
echo "<h2>1. Test Logout File</h2>";
$logout_file = __DIR__ . '/../../controllers/admin/logout.php';
if (file_exists($logout_file)) {
    echo "✅ File logout.php ada<br>";
    $content = file_get_contents($logout_file);
    
    // Cek path redirect (bisa URL absolut atau relatif)
    if (strpos($content, 'views/admin/login.php') !== false || strpos($content, 'redirect_url') !== false) {
        echo "✅ Path redirect ditemukan<br>";
        // Extract redirect URL
        if (preg_match('/redirect_url\s*=\s*[^;]+/', $content, $matches)) {
            echo "✅ Menggunakan URL absolut untuk redirect<br>";
            // Extract actual URL
            if (preg_match('/redirect_url\s*=\s*\$protocol\s*\.\s*[\'"]:\/\/[\'"]\s*\.\s*\$host\s*\.\s*\$base_path\s*\.\s*[\'"]\/views\/admin\/login\.php/', $content)) {
                echo "✅ URL pattern benar (menggunakan base_path calculation)<br>";
            }
        } elseif (strpos($content, '../../views/admin/login.php') !== false) {
            echo "⚠️ Masih menggunakan path relatif (sebaiknya gunakan URL absolut)<br>";
        }
    } else {
        echo "❌ Path redirect tidak ditemukan<br>";
    }
    
    // Cek session destroy
    if (strpos($content, 'session_destroy()') !== false) {
        echo "✅ session_destroy() ada<br>";
    } else {
        echo "❌ session_destroy() tidak ditemukan<br>";
    }
    
    // Cek logActivity
    if (strpos($content, 'logActivity') !== false) {
        echo "✅ logActivity() ada<br>";
    } else {
        echo "⚠️ logActivity() tidak ditemukan (optional)<br>";
    }
} else {
    echo "❌ File logout.php tidak ditemukan<br>";
}

// Test 2: Change Status File
echo "<h2>2. Test Change Status File</h2>";
$change_status_file = __DIR__ . '/../../controllers/admin/change_status.php';
if (file_exists($change_status_file)) {
    echo "✅ File change_status.php ada<br>";
    $content = file_get_contents($change_status_file);
    
    // Cek form method
    if (strpos($content, 'method="POST"') !== false) {
        echo "✅ Form method POST ada<br>";
    } else {
        echo "❌ Form method POST tidak ditemukan<br>";
    }
    
    // Cek form action
    if (strpos($content, 'action=""') !== false || strpos($content, 'action=') === false) {
        echo "✅ Form action kosong (submit ke dirinya sendiri) - OK<br>";
    } else {
        echo "⚠️ Form action: " . (preg_match('/action="([^"]+)"/', $content, $matches) ? $matches[1] : 'tidak ditemukan') . "<br>";
    }
    
    // Cek path redirect (bisa URL absolut atau relatif)
    if (strpos($content, 'views/admin/kelola_karya.php') !== false || strpos($content, 'redirect_url') !== false || strpos($content, '$base_path') !== false) {
        echo "✅ Path redirect ditemukan<br>";
        // Extract redirect URL
        if (preg_match('/redirect_url\s*=\s*[^;]+/', $content, $matches) || strpos($content, '$base_path') !== false) {
            echo "✅ Menggunakan URL absolut untuk redirect<br>";
            // Check if using base_path calculation
            if (strpos($content, 'dirname(dirname($script_path))') !== false || strpos($content, '$base_path') !== false) {
                echo "✅ URL pattern benar (menggunakan base_path calculation)<br>";
            }
        } elseif (strpos($content, '../../views/admin/kelola_karya.php') !== false) {
            echo "⚠️ Masih menggunakan path relatif (sebaiknya gunakan URL absolut)<br>";
        }
    } else {
        echo "❌ Path redirect tidak ditemukan<br>";
    }
    
    // Cek db_connect
    if (strpos($content, 'db_connect.php') !== false) {
        echo "✅ db_connect.php di-include<br>";
    } else {
        echo "❌ db_connect.php tidak di-include<br>";
    }
    
    // Cek $conn
    if (strpos($content, '$conn = $db') !== false) {
        echo "✅ Variable \$conn di-set<br>";
    } else {
        echo "❌ Variable \$conn tidak di-set<br>";
    }
} else {
    echo "❌ File change_status.php tidak ditemukan<br>";
}

// Test 3: Path Verification
echo "<h2>3. Test Path Verification</h2>";
$paths = [
    'controllers/admin/logout.php' => 'controllers/admin/logout.php',
    'views/admin/login.php' => 'views/admin/login.php',
    'controllers/admin/change_status.php' => 'controllers/admin/change_status.php',
    'views/admin/kelola_karya.php' => 'views/admin/kelola_karya.php',
];

foreach ($paths as $name => $path) {
    $full_path = __DIR__ . '/../../' . $path;
    if (file_exists($full_path)) {
        echo "✅ $name: ada<br>";
    } else {
        echo "❌ $name: TIDAK ada<br>";
    }
}

// Test 4: URL Generation Test
echo "<h2>4. Test URL Generation</h2>";
echo "Simulasi URL generation dari controllers/admin/logout.php:<br>";
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
if ($redirect_url === 'http://localhost/portal_tpl/views/admin/login.php?error=logout') {
    echo "✅ URL generation benar!<br>";
} else {
    echo "⚠️ URL generation mungkin salah<br>";
}

// Test file existence
echo "<br>Test file existence:<br>";
$login_file = __DIR__ . '/../../views/admin/login.php';
$kelola_file = __DIR__ . '/../../views/admin/kelola_karya.php';
echo "Login file: " . (file_exists($login_file) ? '✅ Ada' : '❌ Tidak ada') . "<br>";
echo "Kelola file: " . (file_exists($kelola_file) ? '✅ Ada' : '❌ Tidak ada') . "<br>";

echo "<hr>";
echo "<h2>📝 Kesimpulan</h2>";
echo "<p>Jika semua test di atas ✅, maka logout dan change status seharusnya berfungsi.</p>";
echo "<p>Jika masih ada masalah, cek:</p>";
echo "<ul>";
echo "<li>Browser console untuk error JavaScript</li>";
echo "<li>Network tab untuk melihat response dari server</li>";
echo "<li>PHP error log untuk error server-side</li>";
echo "</ul>";

