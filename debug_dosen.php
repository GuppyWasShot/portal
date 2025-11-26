<?php
/**
 * Debug Tool for Kelola Dosen Module
 * 
 * Tests all aspects of Dosen CRUD operations
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/app/autoload.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dosen Module Debug</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h1 { color: #2c3e50; margin-bottom: 10px; }
        h2 { color: #34495e; margin: 25px 0 15px 0; border-bottom: 2px solid #3498db; padding-bottom: 8px; }
        h3 { color: #7f8c8d; margin: 15px 0 10px 0; }
        .section {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .success { background: #d4edda; border-left-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; color: #0c5460; }
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
            font-weight: 600;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        table tr:hover { background: #f8f9fa; }
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
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; color: #e83e8c; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Kelola Dosen - Debug Tool</h1>
    <p style="color: #7f8c8d; margin-bottom: 20px;">Comprehensive testing for Dosen CRUD module</p>
    <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php
    // 1. CHECK SESSION
    echo '<div class="section">';
    echo '<h2>1. Session Status</h2>';
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        echo '<div class="section success">✓ Admin logged in</div>';
        echo '<table>';
        echo '<tr><th>Session Variable</th><th>Value</th></tr>';
        $session_vars = ['admin_logged_in', 'id_admin', 'admin_id', 'username', 'admin_username'];
        foreach ($session_vars as $var) {
            $value = isset($_SESSION[$var]) ? $_SESSION[$var] : 'NOT SET';
            echo '<tr><td><code>' . $var . '</code></td><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<div class="section error">✗ NOT logged in - Login required to test admin functions</div>';
    }
    echo '</div>';

    // 2. CHECK MODEL
    echo '<div class="section">';
    echo '<h2>2. Dosen Model Check</h2>';
    try {
        $dosenModel = new Dosen();
        echo '<div class="section success">✓ Dosen model instantiated successfully</div>';
        
        // Check methods
        $methods = ['getAll', 'getById', 'create', 'update', 'delete', 'getActiveCount'];
        echo '<h3>Available Methods:</h3>';
        echo '<table><tr><th>Method</th><th>Status</th></tr>';
        foreach ($methods as $method) {
            $exists = method_exists($dosenModel, $method);
            $badge = $exists ? '<span class="badge badge-success">EXISTS</span>' : '<span class="badge badge-error">MISSING</span>';
            echo '<tr><td><code>' . $method . '()</code></td><td>' . $badge . '</td></tr>';
        }
        echo '</table>';
    } catch (Exception $e) {
        echo '<div class="section error">✗ Model Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';

    // 3. TEST DATABASE CONNECTION
    echo '<div class="section">';
    echo '<h2>3. Database Connection</h2>';
    try {
        $db = Database::getInstance()->getConnection();
        echo '<div class="section success">✓ Database connected</div>';
        
        // Check tbl_dosen structure
        $result = $db->query("DESCRIBE tbl_dosen");
        echo '<h3>Table Structure: tbl_dosen</h3>';
        echo '<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td><code>' . $row['Field'] . '</code></td>';
            echo '<td>' . $row['Type'] . '</td>';
            echo '<td>' . $row['Null'] . '</td>';
            echo '<td>' . $row['Key'] . '</td>';
            echo '<td>' . ($row['Default'] ?? 'NULL') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } catch (Exception $e) {
        echo '<div class="section error">✗ Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';

    // 4. TEST MODEL METHODS
    echo '<div class="section">';
    echo '<h2>4. Model Method Tests</h2>';
    
    if (isset($dosenModel)) {
        // Test getAll()
        echo '<h3>Test: getAll()</h3>';
        try {
            $dosen_list = $dosenModel->getAll();
            echo '<div class="section success">✓ getAll() returned ' . count($dosen_list) . ' records</div>';
            
            if (!empty($dosen_list)) {
                echo '<h4>Sample Data (first 3 records):</h4>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Nama</th><th>Gelar</th><th>Jabatan</th><th>Status</th><th>Urutan</th><th>Foto</th></tr>';
                foreach (array_slice($dosen_list, 0, 3) as $dosen) {
                    echo '<tr>';
                    echo '<td>' . $dosen['id_dosen'] . '</td>';
                    echo '<td>' . htmlspecialchars($dosen['nama']) . '</td>';
                    echo '<td>' . htmlspecialchars($dosen['gelar'] ?? '-') . '</td>';
                    echo '<td>' . htmlspecialchars($dosen['jabatan'] ?? '-') . '</td>';
                    echo '<td><span class="badge badge-' . ($dosen['status'] == 'active' ? 'success' : 'warning') . '">' . $dosen['status'] . '</span></td>';
                    echo '<td>' . $dosen['urutan'] . '</td>';
                    echo '<td>' . (!empty($dosen['foto_url']) ? '✓' : '✗') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="section warning">⚠ No dosen records in database</div>';
            }
        } catch (Exception $e) {
            echo '<div class="section error">✗ getAll() failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        // Test getById()
        if (!empty($dosen_list)) {
            echo '<h3>Test: getById()</h3>';
            $test_id = $dosen_list[0]['id_dosen'];
            try {
                $single_dosen = $dosenModel->getById($test_id);
                if ($single_dosen) {
                    echo '<div class="section success">✓ getById(' . $test_id . ') successful</div>';
                    echo '<pre>' . print_r($single_dosen, true) . '</pre>';
                } else {
                    echo '<div class="section error">✗ getById(' . $test_id . ') returned NULL</div>';
                }
            } catch (Exception $e) {
                echo '<div class="section error">✗ getById() failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        
        // Test getActiveCount()
        echo '<h3>Test: getActiveCount()</h3>';
        try {
            $active_count = $dosenModel->getActiveCount();
            echo '<div class="section success">✓ Active dosen count: ' . $active_count . '</div>';
        } catch (Exception $e) {
            echo '<div class="section error">✗ getActiveCount() failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    echo '</div>';

    // 5. CHECK UPLOAD DIRECTORY
    echo '<div class="section">';
    echo '<h2>5. Upload Directory Check</h2>';
    
    $upload_dir = __DIR__ . '/uploads/dosen/';
    echo '<p><strong>Path:</strong> <code>' . $upload_dir . '</code></p>';
    
    if (file_exists($upload_dir)) {
        echo '<div class="section success">✓ Directory exists</div>';
        
        if (is_writable($upload_dir)) {
            echo '<div class="section success">✓ Directory is writable</div>';
        } else {
            echo '<div class="section error">✗ Directory is NOT writable</div>';
            echo '<p>Fix: <code>chmod 755 uploads/dosen/</code></p>';
        }
        
        // Count files
        $files = glob($upload_dir . '*');
        echo '<p><strong>Files in directory:</strong> ' . count($files) . '</p>';
        
        if (!empty($files)) {
            echo '<h4>Sample Files:</h4>';
            echo '<ul>';
            foreach (array_slice($files, 0, 5) as $file) {
                $filename = basename($file);
                $size = filesize($file);
                echo '<li><code>' . $filename . '</code> (' . number_format($size / 1024, 2) . ' KB)</li>';
            }
            echo '</ul>';
        }
    } else {
        echo '<div class="section error">✗ Directory does NOT exist</div>';
        echo '<p>Fix: <code>mkdir -p uploads/dosen && chmod 755 uploads/dosen</code></p>';
    }
    echo '</div>';

    // 6. CHECK CONTROLLERS
    echo '<div class="section">';
    echo '<h2>6. Controller Files Check</h2>';
    
    $controllers = [
        'proses_tambah_dosen.php' => 'Add Dosen',
        'proses_edit_dosen.php' => 'Edit Dosen',
        'hapus_dosen.php' => 'Delete Dosen'
    ];
    
    echo '<table><tr><th>Controller</th><th>Purpose</th><th>Status</th><th>Size</th></tr>';
    foreach ($controllers as $file => $purpose) {
        $path = __DIR__ . '/controllers/admin/' . $file;
        $exists = file_exists($path);
        $badge = $exists ? '<span class="badge badge-success">EXISTS</span>' : '<span class="badge badge-error">MISSING</span>';
        $size = $exists ? number_format(filesize($path)) . ' bytes' : '-';
        
        echo '<tr>';
        echo '<td><code>' . $file . '</code></td>';
        echo '<td>' . $purpose . '</td>';
        echo '<td>' . $badge . '</td>';
        echo '<td>' . $size . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // 7. CHECK VIEWS
    echo '<div class="section">';
    echo '<h2>7. View Files Check</h2>';
    
    $views = [
        'kelola_dosen.php' => 'Main list page',
        'form_tambah_dosen.php' => 'Add form',
        'form_edit_dosen.php' => 'Edit form'
    ];
    
    echo '<table><tr><th>View File</th><th>Purpose</th><th>Status</th></tr>';
    foreach ($views as $file => $purpose) {
        $path = __DIR__ . '/views/admin/' . $file;
        $exists = file_exists($path);
        $badge = $exists ? '<span class="badge badge-success">EXISTS</span>' : '<span class="badge badge-error">MISSING</span>';
        
        echo '<tr>';
        echo '<td><code>' . $file . '</code></td>';
        echo '<td>' . $purpose . '</td>';
        echo '<td>' . $badge . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // 8. COMMON ISSUES
    echo '<div class="section">';
    echo '<h2>8. Common Issues & Solutions</h2>';
    
    echo '<div class="section info">';
    echo '<h3>Issue #1: Photo upload fails</h3>';
    echo '<p><strong>Symptoms:</strong> File not uploaded, error redirect</p>';
    echo '<p><strong>Causes:</strong></p>';
    echo '<ul>';
    echo '<li>Directory <code>uploads/dosen/</code> not writable</li>';
    echo '<li>File size exceeds limit (max 2MB)</li>';
    echo '<li>Invalid file type (only JPG, PNG, WEBP allowed)</li>';
    echo '</ul>';
    echo '<p><strong>Fix:</strong> <code>chmod 755 uploads/dosen/</code></p>';
    echo '</div>';
    
    echo '<div class="section info">';
    echo '<h3>Issue #2: Dosen list not showing</h3>';
    echo '<p><strong>Causes:</strong></p>';
    echo '<ul>';
    echo '<li>Model not instantiated in view</li>';
    echo '<li>Database connection failed</li>';
    echo '<li>Query returns empty (no dosen in DB)</li>';
    echo '</ul>';
    echo '<p><strong>Check:</strong> View file uses <code>$dosenModel->getAll()</code></p>';
    echo '</div>';
    
    echo '<div class="section info">';
    echo '<h3>Issue #3: Edit form not loading</h3>';
    echo '<p><strong>Causes:</strong></p>';
    echo '<ul>';
    echo '<li>Invalid ID in URL</li>';
    echo '<li>Dosen not found in database</li>';
    echo '<li>Session expired</li>';
    echo '</ul>';
    echo '<p><strong>Check:</strong> URL has <code>?id=X</code> parameter</p>';
    echo '</div>';
    
    echo '</div>';

    // 9. MANUAL TESTING CHECKLIST
    echo '<div class="section">';
    echo '<h2>9. Manual Testing Checklist</h2>';
    
    echo '<table>';
    echo '<tr><th>Test Case</th><th>URL/Action</th><th>Expected Result</th></tr>';
    echo '<tr>';
    echo '<td>View List</td>';
    echo '<td><a href="views/admin/kelola_dosen.php" target="_blank">kelola_dosen.php</a></td>';
    echo '<td>✓ List of dosen displayed</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Add Dosen</td>';
    echo '<td>Click "Tambah Dosen" button</td>';
    echo '<td>✓ Form shown, fill & submit</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Upload Photo</td>';
    echo '<td>Add dosen with photo</td>';
    echo '<td>✓ Photo saved in <code>uploads/dosen/</code></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Edit Dosen</td>';
    echo '<td>Click edit icon</td>';
    echo '<td>✓ Form pre-filled with data</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Delete Dosen</td>';
    echo '<td>Click delete icon</td>';
    echo '<td>✓ Confirm dialog, then deleted</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Public View</td>';
    echo '<td><a href="views/public/dosen.php" target="_blank">dosen.php (public)</a></td>';
    echo '<td>✓ Active dosen displayed with photos</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
    ?>

    <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
        <p style="margin-bottom: 10px;"><strong>⚠️ Debug File Notice:</strong></p>
        <p style="color: #856404;">Delete this file (<code>debug_dosen.php</code>) after debugging is complete for security reasons!</p>
        <p style="color: #856404; margin-top: 8px;">📝 Check error logs: <code>/opt/lampp/logs/php_error_log</code></p>
    </div>
</div>
</body>
</html>
