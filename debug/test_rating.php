<?php
/**
 * Test Rating - Debugging Tool
 * 
 * Akses: /debug/test_rating.php?id=19
 * 
 * File ini untuk membantu debugging masalah rating
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../config/db_connect.php';

$id_project = isset($_GET['id']) ? intval($_GET['id']) : 19;

echo "<h1>Test Rating untuk Project ID: $id_project</h1>";

$db = Database::getInstance()->getConnection();

// 1. Cek apakah project ada
echo "<h2>1. Cek Project</h2>";
$stmt = $db->prepare("SELECT id_project, judul, status FROM tbl_project WHERE id_project = ?");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

if ($project) {
    echo "<p style='color: green;'>✓ Project ditemukan:</p>";
    echo "<ul>";
    echo "<li>ID: " . $project['id_project'] . "</li>";
    echo "<li>Judul: " . htmlspecialchars($project['judul']) . "</li>";
    echo "<li>Status: " . $project['status'] . "</li>";
    echo "</ul>";
    
    if ($project['status'] !== 'Published') {
        echo "<p style='color: red;'>✗ ERROR: Project status bukan 'Published'! Status saat ini: " . $project['status'] . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Status: Published (OK)</p>";
    }
} else {
    echo "<p style='color: red;'>✗ ERROR: Project dengan ID $id_project tidak ditemukan!</p>";
    exit();
}

// 2. Cek apakah tabel rating ada
echo "<h2>2. Cek Tabel Rating</h2>";
$table_check = $db->query("SHOW TABLES LIKE 'tbl_rating'");
if ($table_check->num_rows > 0) {
    echo "<p style='color: green;'>✓ Tabel tbl_rating ada</p>";
    
    // Cek struktur tabel
    $desc = $db->query("DESCRIBE tbl_rating");
    echo "<h3>Struktur Tabel:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $desc->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ ERROR: Tabel tbl_rating tidak ditemukan!</p>";
    exit();
}

// 3. Cek apakah kolom uuid_user ada
echo "<h2>3. Cek Kolom uuid_user</h2>";
$check_uuid = $db->query("SHOW COLUMNS FROM tbl_rating LIKE 'uuid_user'");
if ($check_uuid && $check_uuid->num_rows > 0) {
    echo "<p style='color: green;'>✓ Kolom uuid_user ada</p>";
    $has_uuid = true;
} else {
    echo "<p style='color: orange;'>⚠ Kolom uuid_user TIDAK ada - akan menggunakan ip_address saja</p>";
    echo "<p>Untuk menambahkan kolom, jalankan query berikut di phpMyAdmin:</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px;'>ALTER TABLE `tbl_rating` ADD COLUMN `uuid_user` varchar(255) DEFAULT NULL AFTER `ip_address`;</pre>";
    $has_uuid = false;
}

// 4. Test insert rating
echo "<h2>4. Test Insert Rating</h2>";
$test_uuid = 'test_' . time();
$test_ip = '127.0.0.1';
$test_score = 5;

if ($has_uuid) {
    $stmt = $db->prepare("INSERT INTO tbl_rating (id_project, uuid_user, ip_address, skor) VALUES (?, ?, ?, ?)");
    $bind_type = "issi";
    $bind_params = [$id_project, $test_uuid, $test_ip, $test_score];
} else {
    $stmt = $db->prepare("INSERT INTO tbl_rating (id_project, ip_address, skor) VALUES (?, ?, ?)");
    $bind_type = "isi";
    $bind_params = [$id_project, $test_ip, $test_score];
}
if (!$stmt) {
    echo "<p style='color: red;'>✗ ERROR: Gagal prepare statement - " . $db->error . "</p>";
} else {
    $stmt->bind_param($bind_type, ...$bind_params);
    if ($stmt->execute()) {
        $test_id = $stmt->insert_id;
        echo "<p style='color: green;'>✓ Test insert berhasil! ID rating: $test_id</p>";
        
        // Hapus test data
        $delete_stmt = $db->prepare("DELETE FROM tbl_rating WHERE id_rating = ?");
        $delete_stmt->bind_param("i", $test_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        echo "<p style='color: blue;'>ℹ Test data sudah dihapus</p>";
    } else {
        echo "<p style='color: red;'>✗ ERROR: Gagal execute insert - " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// 5. Cek rating yang ada
echo "<h2>5. Rating yang Ada untuk Project ini</h2>";
$rating_stmt = $db->prepare("SELECT * FROM tbl_rating WHERE id_project = ? ORDER BY tanggal_rating DESC LIMIT 10");
$rating_stmt->bind_param("i", $id_project);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();

if ($rating_result->num_rows > 0) {
    echo "<p>Total rating: " . $rating_result->num_rows . "</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>UUID</th><th>IP</th><th>Skor</th><th>Tanggal</th></tr>";
    while ($rating = $rating_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $rating['id_rating'] . "</td>";
        echo "<td>" . htmlspecialchars($rating['uuid_user']) . "</td>";
        echo "<td>" . htmlspecialchars($rating['ip_address']) . "</td>";
        echo "<td>" . $rating['skor'] . "</td>";
        echo "<td>" . $rating['tanggal_rating'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>ℹ Belum ada rating untuk project ini</p>";
}
$rating_stmt->close();

// 6. Test Rating Class
echo "<h2>6. Test Rating Class</h2>";
try {
    $ratingModel = new Rating();
    echo "<p style='color: green;'>✓ Rating class berhasil diinisialisasi</p>";
    
    // Test submit rating
    $test_result = $ratingModel->submitRating($id_project, 4, 'test_class_' . time(), '127.0.0.1');
    if ($test_result) {
        echo "<p style='color: green;'>✓ Test submitRating() berhasil</p>";
    } else {
        echo "<p style='color: red;'>✗ Test submitRating() gagal</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='../views/public/detail_karya.php?id=$id_project'>Kembali ke Detail Karya</a></p>";

