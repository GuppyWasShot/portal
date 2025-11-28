<?php
/**
 * Debug Tool - Detail Karya Snapshots
 * Untuk troubleshoot masalah foto snapshot besar & thumbnail
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/autoload.php';

// Get ID from URL
$id_project = isset($_GET['id']) ? intval($_GET['id']) : 0;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug Detail Karya Snapshots</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h1 { color: #2c3e50; margin-bottom: 10px; }
        h2 { 
            color: #34495e; 
            margin: 25px 0 15px 0; 
            border-bottom: 2px solid #3498db; 
            padding-bottom: 8px;
        }
        .section {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .success { background: #d4edda; border-left-color: #28a745; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            margin: 10px 0;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        table tr:hover { background: #f8f9fa; }
        code { 
            background: #e9ecef; 
            padding: 2px 6px; 
            border-radius: 3px; 
            color: #e83e8c;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .photo-card {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
        .photo-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .photo-card .status {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 5px;
            display: inline-block;
        }
        .status.ok { background: #d4edda; color: #155724; }
        .status.fail { background: #f8d7da; color: #721c24; }
        input[type="number"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100px;
            margin-right: 10px;
        }
        button {
            padding: 8px 16px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Debug Detail Karya - Snapshots</h1>
    <p style="color: #7f8c8d; margin-bottom: 20px;">Diagnostic tool untuk snapshot besar & thumbnail</p>

    <!-- ID Selector -->
    <div class="section">
        <form method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label>ID Karya:</label>
            <input type="number" name="id" value="<?php echo $id_project; ?>" required>
            <button type="submit">Load Karya</button>
            <?php if ($id_project > 0): ?>
            <a href="views/public/detail_karya.php?id=<?php echo $id_project; ?>" target="_blank" style="margin-left: 10px; color: #3498db;">
                → View Detail Page
            </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($id_project > 0): ?>
    <?php
    try {
        $karyaModel = new Karya();
        $karya = $karyaModel->getKaryaById($id_project);
        
        if (!$karya) {
            echo '<div class="section error">❌ Karya dengan ID ' . $id_project . ' tidak ditemukan!</div>';
        } else {
            // 1. INFO KARYA
            echo '<h2>📊 1. Info Karya</h2>';
            echo '<div class="section">';
            echo '<table>';
            echo '<tr><th>Property</th><th>Value</th></tr>';
            echo '<tr><td>ID</td><td>' . $karya['id_project'] . '</td></tr>';
            echo '<tr><td>Judul</td><td>' . htmlspecialchars($karya['judul']) . '</td></tr>';
            echo '<tr><td>Status</td><td><span class="badge badge-' . ($karya['status'] == 'Published' ? 'success' : 'warning') . '">' . $karya['status'] . '</span></td></tr>';
            echo '<tr><td>Snapshot URL (DB)</td><td><code>' . htmlspecialchars($karya['snapshot_url'] ?? 'NULL') . '</code></td></tr>';
            echo '</table>';
            echo '</div>';
            
            // 2. SNAPSHOT UTAMA CHECK
            echo '<h2>🖼️ 2. Main Snapshot Check</h2>';
            $main_snapshot = $karya['snapshot_url'];
            
            if (empty($main_snapshot)) {
                echo '<div class="section warning">⚠️ Tidak ada snapshot_url di database</div>';
            } else {
                echo '<div class="section">';
                echo '<table>';
                echo '<tr><th>Check</th><th>Result</th></tr>';
                
                // Path checks
                $paths_to_test = [
                    'Database value' => $main_snapshot,
                    'Relative path (old)' => '../../' . $main_snapshot,
                    'Absolute path (new)' => '/portal-tpl/' . $main_snapshot,
                    'Filesystem path' => __DIR__ . '/' . $main_snapshot,
                ];
                
                foreach ($paths_to_test as $label => $path) {
                    echo '<tr>';
                    echo '<td>' . $label . '</td>';
                    echo '<td><code>' . htmlspecialchars($path) . '</code></td>';
                    echo '</tr>';
                }
                
                // File exists check
                $file_path = __DIR__ . '/' . $main_snapshot;
                $exists = file_exists($file_path);
                echo '<tr>';
                echo '<td><strong>File exists?</strong></td>';
                echo '<td><span class="badge badge-' . ($exists ? 'success' : 'error') . '">' . ($exists ? 'YES' : 'NO') . '</span></td>';
                echo '</tr>';
                
                if ($exists) {
                    $size = filesize($file_path);
                    echo '<tr><td>File size</td><td>' . number_format($size / 1024, 2) . ' KB</td></tr>';
                }
                
                echo '</table>';
                echo '</div>';
                
                // Visual test
                echo '<h3>Visual Test:</h3>';
                echo '<div class="photo-grid">';
                echo '<div class="photo-card">';
                echo '<h4>Old Path (../../)</h4>';
                echo '<img src="../../' . htmlspecialchars($main_snapshot) . '" onerror="this.nextElementSibling.innerHTML=\'<span class=\\\'status fail\\\'>FAILED</span>\'">';
                echo '<div><span class="status ok">LOADED</span></div>';
                echo '</div>';
                
                echo '<div class="photo-card">';
                echo '<h4>New Path (/portal-tpl/)</h4>';
                echo '<img src="/portal-tpl/' . htmlspecialchars($main_snapshot) . '" onerror="this.nextElementSibling.innerHTML=\'<span class=\\\'status fail\\\'>FAILED</span>\'">';
                echo '<div><span class="status ok">LOADED</span></div>';
                echo '</div>';
                echo '</div>';
            }
            
            // 3. ALL FILES/SNAPSHOTS
            echo '<h2>📁 3. All Files & Snapshots</h2>';
            $files = $karyaModel->getFiles($id_project);
            
            if (empty($files)) {
                echo '<div class="section warning">⚠️ Tidak ada file tambahan untuk karya ini (tbl_project_files kosong)</div>';
                
                // Check database directly
                $stmt = $db->prepare("SELECT * FROM tbl_project_files WHERE id_project = ?");
                $stmt->bind_param("i", $id_project);
                $stmt->execute();
                $result = $stmt->get_result();
                $direct_files = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                
                echo '<p><strong>Direct DB Query Result:</strong> ' . count($direct_files) . ' files</p>';
                if (!empty($direct_files)) {
                    echo '<pre>' . print_r($direct_files, true) . '</pre>';
                }

            } else {
                $fileGroups = $karyaModel->separateFiles($files);
                
                // Snapshots
                if (!empty($fileGroups['snapshots'])) {
                    echo '<h3>Snapshots (' . count($fileGroups['snapshots']) . '):</h3>';
                    echo '<table>';
                    echo '<tr><th>#</th><th>Label</th><th>File Path</th><th>Exists?</th><th>Size</th></tr>';
                    
                    foreach ($fileGroups['snapshots'] as $idx => $snap) {
                        $file_path = __DIR__ . '/' . $snap['file_path'];
                        $exists = file_exists($file_path);
                        $size = $exists ? number_format(filesize($file_path) / 1024, 2) . ' KB' : '-';
                        
                        echo '<tr>';
                        echo '<td>' . ($idx + 1) . '</td>';
                        echo '<td>' . htmlspecialchars($snap['label']) . '</td>';
                        echo '<td><code>' . htmlspecialchars($snap['file_path']) . '</code></td>';
                        echo '<td><span class="badge badge-' . ($exists ? 'success' : 'error') . '">' . ($exists ? 'YES' : 'NO') . '</span></td>';
                        echo '<td>' . $size . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    
                    // Visual gallery
                    echo '<h3>Thumbnail Gallery Test:</h3>';
                    echo '<div class="photo-grid">';
                    foreach ($fileGroups['snapshots'] as $snap) {
                        echo '<div class="photo-card">';
                        echo '<img src="/portal-tpl/' . htmlspecialchars($snap['file_path']) . '" onerror="this.nextElementSibling.innerHTML=\'<span class=\\\'status fail\\\'>FAILED</span>\'">';
                        echo '<div><span class="status ok">OK</span></div>';
                        echo '<small><code>' . basename($snap['file_path']) . '</code></small>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>Tidak ada snapshot tambahan</p>';
                }
                
                // Documents
                if (!empty($fileGroups['documents'])) {
                    echo '<h3>Documents (' . count($fileGroups['documents']) . '):</h3>';
                    echo '<table>';
                    echo '<tr><th>#</th><th>Label</th><th>File Path</th><th>Exists?</th></tr>';
                    
                    foreach ($fileGroups['documents'] as $idx => $doc) {
                        $file_path = __DIR__ . '/' . $doc['file_path'];
                        $exists = file_exists($file_path);
                        
                        echo '<tr>';
                        echo '<td>' . ($idx + 1) . '</td>';
                        echo '<td>' . htmlspecialchars($doc['label']) . '</td>';
                        echo '<td><code>' . htmlspecialchars($doc['file_path']) . '</code></td>';
                        echo '<td><span class="badge badge-' . ($exists ? 'success' : 'error') . '">' . ($exists ? 'YES' : 'NO') . '</span></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            }
            
            // 4. RECOMMENDATIONS
            echo '<h2>💡 4. Recommendations</h2>';
            echo '<div class="section">';
            echo '<h3>Path yang Harus Digunakan:</h3>';
            echo '<pre>';
            echo "// Di views/public/detail_karya.php\n";
            echo "// ✅ CORRECT:\n";
            echo 'src="/portal-tpl/<?php echo $snapshot; ?>"\n\n';
            echo "// ❌ WRONG:\n";
            echo 'src="../../<?php echo $snapshot; ?>"';
            echo '</pre>';
            
            echo '<h3>Jika Foto Tidak Muncul:</h3>';
            echo '<ol>';
            echo '<li>Pastikan file benar-benar ada di folder <code>uploads/snapshots/</code></li>';
            echo '<li>Check permissions: folder 755, files 644</li>';
            echo '<li>Pastikan path di database tidak ada prefix <code>../../</code></li>';
            echo '<li>Use <code>/portal-tpl/{path}</code> bukan <code>../../{path}</code></li>';
            echo '<li>Clear browser cache (Ctrl+Shift+R)</li>';
            echo '</ol>';
            echo '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="section error">';
        echo '<h3>❌ Error!</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    <?php else: ?>
    <div class="section warning">
        <p>⚠️ Masukkan ID Karya untuk memulai debugging</p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
