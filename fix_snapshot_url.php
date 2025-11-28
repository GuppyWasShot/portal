<?php
/**
 * Auto-Fix Snapshot URL
 * Automatically fixes snapshot_url yang menunjuk ke file tidak ada
 * Menggunakan foto pertama dari tbl_project_files sebagai thumbnail
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/autoload.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Auto-Fix Snapshot URL</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 13px; }
        th { background: #3498db; color: white; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .badge-error { background: #dc3545; color: white; }
        .badge-ok { background: #28a745; color: white; }
        button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h1>🔧 Auto-Fix Snapshot URL</h1>
    <p style="color: #666;">Automatic repair - refreshing fixes database automatically</p>

<?php
try {
    $db = Database::getInstance()->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed!");
    }
    
    // AUTO-SCAN & FIX
    echo "<div class='info'><h3>🔍 Scanning & Auto-Fixing...</h3></div>";
    
    // Get all projects with their snapshot info
    $query = "
        SELECT 
            p.id_project, 
            p.judul, 
            p.status,
            p.snapshot_url,
            (SELECT file_path FROM tbl_project_files 
             WHERE id_project = p.id_project 
             AND (mime_type LIKE 'image/%' OR nama_file LIKE '%.png' OR nama_file LIKE '%.jpg' OR nama_file LIKE '%.jpeg')
             ORDER BY id_file ASC LIMIT 1) as first_snapshot
        FROM tbl_project p
        ORDER BY p.id_project DESC
    ";
    
    $result = $db->query($query);
    
    if (!$result) {
        throw new Exception("Query failed: " . $db->error);
    }
    
    $issues_found = [];
    $fixed_count = 0;
    $failed = [];
    
    while ($row = $result->fetch_assoc()) {
        // Check if snapshot_url exists as file
        $snapshot_exists = false;
        if (!empty($row['snapshot_url'])) {
            $snapshot_path = __DIR__ . '/' . $row['snapshot_url'];
            $snapshot_exists = file_exists($snapshot_path);
        }
        
        // Check if first_snapshot exists
        $first_exists = false;
        if (!empty($row['first_snapshot'])) {
            $first_path = __DIR__ . '/' . $row['first_snapshot'];
            $first_exists = file_exists($first_path);
        }
        
        // AUTO-FIX if snapshot_url doesn't exist but first_snapshot does
        if (!$snapshot_exists && $first_exists) {
            $issues_found[] = $row;
            
            // Execute fix immediately
            $stmt = $db->prepare("UPDATE tbl_project SET snapshot_url = ? WHERE id_project = ?");
            $stmt->bind_param("si", $row['first_snapshot'], $row['id_project']);
            
            if ($stmt->execute()) {
                $fixed_count++;
                echo "<p style='color: #28a745;'>✓ Auto-fixed ID " . $row['id_project'] . ": " . htmlspecialchars(substr($row['judul'], 0, 40)) . "...</p>";
                echo "<p style='margin-left: 20px; font-size: 12px; color: #666;'>";
                echo "  Changed: <code>" . htmlspecialchars($row['snapshot_url'] ?? 'NULL') . "</code><br>";
                echo "  To: <code>" . htmlspecialchars($row['first_snapshot']) . "</code>";
                echo "</p>";
            } else {
                $failed[] = "ID " . $row['id_project'] . ": " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Display results
    if (empty($issues_found)) {
        echo "<div class='success'>";
        echo "<h3>✅ All Good!</h3>";
        echo "<p>Tidak ada snapshot_url yang perlu diperbaiki. Semua sudah sesuai.</p>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "<h3>✅ Auto-Fix Complete!</h3>";
        echo "<p><strong>Total issues found:</strong> " . count($issues_found) . "</p>";
        echo "<p><strong>Successfully fixed:</strong> $fixed_count</p>";
        
        if (!empty($failed)) {
            echo "<p><strong>Failed:</strong> " . count($failed) . "</p>";
            echo "<pre>" . implode("\n", $failed) . "</pre>";
        }
        echo "</div>";
        
        // Show summary table
        echo "<h3>📊 Fixed Items:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Judul</th><th>Old URL</th><th>New URL (First Image)</th></tr>";
        
        foreach ($issues_found as $issue) {
            echo "<tr>";
            echo "<td>" . $issue['id_project'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($issue['judul'], 0, 35)) . "...</td>";
            echo "<td><code style='color: #dc3545;'>" . htmlspecialchars($issue['snapshot_url'] ?? 'NULL') . "</code></td>";
            echo "<td><code style='color: #28a745;'>" . htmlspecialchars($issue['first_snapshot']) . "</code></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div class='info'>";
        echo "<h3>✅ Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Clear browser cache (Ctrl+Shift+R)</li>";
        echo "<li>Test detail pages - foto sekarang akan muncul!</li>";
        echo "<li>Refresh this page to verify no more issues</li>";
        echo "</ol>";
        
        // Quick links to test
        if (!empty($issues_found)) {
            echo "<h4>Quick Test Links:</h4>";
            foreach (array_slice($issues_found, 0, 3) as $issue) {
                echo "<p>→ <a href='views/public/detail_karya.php?id=" . $issue['id_project'] . "' target='_blank'>" . 
                     htmlspecialchars($issue['judul']) . "</a></p>";
            }
        }
        echo "</div>";
    }
    
    // Show current status of all projects
    echo "<div class='info'>";
    echo "<h3>📊 Current Database Status:</h3>";
    
    $status_query = "
        SELECT 
            p.id_project,
            p.judul,
            p.snapshot_url,
            (SELECT COUNT(*) FROM tbl_project_files 
             WHERE id_project = p.id_project 
             AND (mime_type LIKE 'image/%' OR nama_file LIKE '%.png' OR nama_file LIKE '%.jpg')) as image_count
        FROM tbl_project p
        WHERE p.status = 'Published'
        ORDER BY p.id_project DESC
        LIMIT 10
    ";
    
    $status_result = $db->query($status_query);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Judul</th><th>Snapshot URL</th><th>File OK?</th><th>Images</th></tr>";
    
    while ($row = $status_result->fetch_assoc()) {
        $exists = false;
        if (!empty($row['snapshot_url'])) {
            $exists = file_exists(__DIR__ . '/' . $row['snapshot_url']);
        }
        
        echo "<tr>";
        echo "<td>" . $row['id_project'] . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['judul'], 0, 30)) . "...</td>";
        echo "<td><code>" . htmlspecialchars($row['snapshot_url'] ?? 'NULL') . "</code></td>";
        
        if (empty($row['snapshot_url'])) {
            echo "<td>-</td>";
        } else {
            echo "<td><span class='badge badge-" . ($exists ? 'ok' : 'error') . "'>" . ($exists ? 'YES' : 'NO') . "</span></td>";
        }
        
        echo "<td>" . $row['image_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><small>Showing last 10 published projects</small></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Error!</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>

<div style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 5px;">
    <p><strong>✨ Auto-Fix Enabled!</strong></p>
    <p>Script ini otomatis memperbaiki database setiap kali di-refresh.</p>
    <p>Foto pertama dari <code>tbl_project_files</code> akan digunakan sebagai thumbnail utama.</p>
</div>

<div style="margin-top: 20px; text-align: center;">
    <button onclick="location.reload()">🔄 Refresh untuk Scan Ulang</button>
</div>

</body>
</html>
