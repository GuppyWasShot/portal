<?php
/**
 * FIX Database Paths - Dosen Photos
 * One-time script to fix foto_url paths in database
 */

require_once __DIR__ . '/app/autoload.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Fix Dosen Photo Paths</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #3498db; color: white; }
    </style>
</head>
<body>
    <h1>🔧 Fix Dosen Photo Paths</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Show BEFORE
    echo "<div class='info'><h3>BEFORE - Current Paths:</h3>";
    $result = $db->query("SELECT id_dosen, nama, foto_url FROM tbl_dosen ORDER BY id_dosen");
    echo "<table><tr><th>ID</th><th>Nama</th><th>Old Path</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_dosen'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
        echo "<td><code>" . htmlspecialchars($row['foto_url']) . "</code></td>";
        echo "</tr>";
    }
    echo "</table></div>";
    
    // Execute UPDATE
    $sql = "UPDATE tbl_dosen 
            SET foto_url = REPLACE(foto_url, 'assets/img/dosen/', 'uploads/dosen/')
            WHERE foto_url LIKE 'assets/img/dosen/%'";
    
    $result = $db->query($sql);
    $affected = $db->affected_rows;
    
    if ($result) {
        echo "<div class='success'>";
        echo "<h3>✅ SUCCESS!</h3>";
        echo "<p><strong>Rows updated:</strong> $affected</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ ERROR!</h3>";
        echo "<p>" . htmlspecialchars($db->error) . "</p>";
        echo "</div>";
    }
    
    // Show AFTER
    echo "<div class='success'><h3>AFTER - New Paths:</h3>";
    $result = $db->query("SELECT id_dosen, nama, foto_url FROM tbl_dosen ORDER BY id_dosen");
    echo "<table><tr><th>ID</th><th>Nama</th><th>New Path</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_dosen'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
        echo "<td><code>" . htmlspecialchars($row['foto_url']) . "</code></td>";
        echo "</tr>";
    }
    echo "</table></div>";
    
    echo "<div class='info'>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Clear CloudFlare cache di InfinityFree Control Panel</li>";
    echo "<li>Visit <a href='views/public/dosen.php' target='_blank'>views/public/dosen.php</a> to verify photos load</li>";
    echo "<li>Run <a href='debug_foto_dosen.php' target='_blank'>debug tool</a> again to confirm all green</li>";
    echo "<li><strong>DELETE this file (fix_dosen_paths.php) after success!</strong></li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Database Error!</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</body></html>";
?>
