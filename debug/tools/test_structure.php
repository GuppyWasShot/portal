<?php
/**
 * Test File - Cek apakah struktur dan Class berfungsi
 * Akses: http://localhost/portal_tpl/test_structure.php
 */

echo "<h1>Test Struktur Portal TPL</h1>";

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

// Test 3: Karya Class
echo "<h2>3. Test Karya Class</h2>";
try {
    $karya = new Karya();
    echo "✅ Karya Class berhasil di-instantiate<br>";
    
    // Test method
    $test_karya = $karya->getKaryaById(14);
    if ($test_karya) {
        echo "✅ Method getKaryaById() berfungsi<br>";
        echo "   - Judul: " . htmlspecialchars($test_karya['judul']) . "<br>";
    } else {
        echo "⚠️ Method getKaryaById() berfungsi, tapi tidak ada data dengan ID=1<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 4: Rating Class
echo "<h2>4. Test Rating Class</h2>";
try {
    $rating = new Rating();
    echo "✅ Rating Class berhasil di-instantiate<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 5: File Structure
echo "<h2>5. Test File Structure</h2>";
$files_to_check = [
    'app/models/Database.php' => 'Database Class',
    'app/models/Karya.php' => 'Karya Class',
    'app/models/Rating.php' => 'Rating Class',
    'app/autoload.php' => 'Autoloader',
    'views/public/index.php' => 'View: Index',
    'views/public/galeri.php' => 'View: Galeri',
    'views/public/detail_karya.php' => 'View: Detail Karya',
    'views/public/proses_rating.php' => 'View: Proses Rating',
];

foreach ($files_to_check as $file => $desc) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $desc: $file<br>";
    } else {
        echo "❌ $desc: $file TIDAK DITEMUKAN<br>";
    }
}

echo "<hr>";
echo "<h2>✅ Test Selesai!</h2>";
echo "<p>Jika semua test berhasil, struktur sudah benar dan siap digunakan.</p>";
echo "<p><a href='views/public/index.php'>→ Buka Beranda</a></p>";
echo "<p><a href='views/public/galeri.php'>→ Buka Galeri</a></p>";

