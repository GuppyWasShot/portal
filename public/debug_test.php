<?php
// File untuk testing koneksi dan debug
// Simpan di: public/debug_test.php
// Akses: http://localhost/portal_tpl/public/debug_test.php?id=1

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Portal TPL - Debug Test</h1>";

// 1. Test Database Connection
echo "<h2>1. Test Database Connection</h2>";
include '../config/db_connect.php';

if ($conn) {
    echo "✅ Database connection successful!<br>";
    echo "Server info: " . mysqli_get_server_info($conn) . "<br>";
} else {
    echo "❌ Database connection failed: " . mysqli_connect_error() . "<br>";
    exit;
}

// 2. Check Tables
echo "<h2>2. Check Tables</h2>";
$tables = ['tbl_project', 'tbl_category', 'tbl_rating', 'tbl_project_links', 'tbl_project_files', 'tbl_project_category'];

foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM $table"))['total'];
        echo "✅ Table '$table' exists with $count records<br>";
    } else {
        echo "❌ Table '$table' not found<br>";
    }
}

// 3. Check Rating Table Structure
echo "<h2>3. Check Rating Table Structure</h2>";
$result = mysqli_query($conn, "DESCRIBE tbl_rating");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if required columns exist
    $result = mysqli_query($conn, "DESCRIBE tbl_rating");
    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }
    
    $required = ['uuid_user', 'ip_address'];
    foreach ($required as $col) {
        if (in_array($col, $columns)) {
            echo "✅ Column '$col' exists<br>";
        } else {
            echo "❌ Column '$col' missing - run the SQL update script!<br>";
        }
    }
} else {
    echo "❌ Cannot describe tbl_rating<br>";
}

// 4. Test Query Single Project
echo "<h2>4. Test Query Single Project</h2>";
$test_id = isset($_GET['id']) ? intval($_GET['id']) : 14;

$query = "SELECT p.*, 
          GROUP_CONCAT(DISTINCT c.nama_kategori ORDER BY c.nama_kategori SEPARATOR ', ') as kategori,
          GROUP_CONCAT(DISTINCT c.warna_hex ORDER BY c.nama_kategori SEPARATOR ',') as warna,
          GROUP_CONCAT(DISTINCT c.icon_emoji ORDER BY c.nama_kategori SEPARATOR ',') as icons,
          AVG(r.skor) as avg_rating,
          COUNT(DISTINCT r.id_rating) as total_rating
          FROM tbl_project p
          LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
          LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
          LEFT JOIN tbl_rating r ON p.id_project = r.id_project
          WHERE p.id_project = $test_id
          GROUP BY p.id_project";

echo "<strong>Query:</strong><br><pre>$query</pre><br>";

$result = mysqli_query($conn, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Project found!<br>";
        $project = mysqli_fetch_assoc($result);
        echo "<pre>";
        print_r($project);
        echo "</pre>";
    } else {
        echo "❌ No project found with ID $test_id<br>";
        
        // Check if project exists but not published
        $check = mysqli_query($conn, "SELECT id_project, judul, status FROM tbl_project WHERE id_project = $test_id");
        if (mysqli_num_rows($check) > 0) {
            $p = mysqli_fetch_assoc($check);
            echo "ℹ️ Project exists but status is: " . $p['status'] . "<br>";
            echo "Title: " . $p['judul'] . "<br>";
        } else {
            echo "ℹ️ No project with ID $test_id in database<br>";
        }
    }
} else {
    echo "❌ Query error: " . mysqli_error($conn) . "<br>";
}

// 5. List All Projects
echo "<h2>5. All Projects in Database</h2>";
$result = mysqli_query($conn, "SELECT id_project, judul, status FROM tbl_project ORDER BY id_project DESC LIMIT 10");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Judul</th><th>Status</th><th>Test Link</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_project'] . "</td>";
        echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td><a href='detail_karya.php?id=" . $row['id_project'] . "' target='_blank'>View</a> | ";
        echo "<a href='debug_test.php?id=" . $row['id_project'] . "'>Debug</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No projects found in database<br>";
}

// 6. PHP Info
echo "<h2>6. PHP Configuration</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";
echo "Error Reporting: " . error_reporting() . "<br>";

mysqli_close($conn);
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #4F46E5; }
    h2 { color: #6366F1; margin-top: 30px; border-bottom: 2px solid #E0E7FF; padding-bottom: 10px; }
    table { border-collapse: collapse; margin: 10px 0; }
    pre { background: #F3F4F6; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>