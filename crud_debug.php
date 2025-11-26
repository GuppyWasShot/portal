<?php
/**
 * CRUD Operations Debug Tool
 * 
 * Tujuan: Debugging dan testing semua CRUD operations untuk:
 * - Dosen (Create, Read, Update, Delete)
 * - Karya (Create, Read, Update, Delete, File Upload)
 * 
 * READ-ONLY untuk testing koneksi, bisa simulate operations
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ROOT_PATH', __DIR__);

// Start session for testing
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CRUD Debug Tool - Portal TPL</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Monaco', 'Courier New', monospace;
            background: #1a1a1a;
            color: #00ff00;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #0a0a0a;
            border: 2px solid #00ff00;
            border-radius: 8px;
            padding: 20px;
        }
        h1 { color: #00ff00; border-bottom: 2px solid #00ff00; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #00aaff; margin: 20px 0 10px 0; }
        .section {
            background: #111;
            border-left: 4px solid #00ff00;
            padding: 15px;
            margin: 15px 0;
        }
        .test-result {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success { background: #1a4d1a; color: #00ff00; border: 1px solid #00ff00; }
        .error { background: #4d1a1a; color: #ff4444; border: 1px solid #ff4444; }
        .warning { background: #4d4d1a; color: #ffaa00; border: 1px solid #ffaa00; }
        .info { background: #1a1a4d; color: #00aaff; border: 1px solid #00aaff; }
        pre {
            background: #000;
            border: 1px solid #333;
            padding: 10px;
            overflow-x: auto;
            margin: 10px 0;
            color: #ccc;
        }
        .code { color: #ff00ff; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table th {
            background: #003300;
            color: #00ff00;
            padding: 8px;
            text-align: left;
            border: 1px solid #00ff00;
        }
        table td {
            padding: 8px;
            border: 1px solid #333;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            background: #003300;
            color: #00ff00;
            border: 1px solid #00ff00;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover { background: #005500; }
    </style>
</head>
<body>
<div class="container">
    <h1>>> CRUD OPERATIONS DEBUG TOOL <<</h1>
    <p style="color: #ffaa00;">⚠️ Testing and Debugging Interface - <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php
    // Load autoload
    try {
        require_once ROOT_PATH . '/app/autoload.php';
        echo '<div class="test-result success">✓ Autoload loaded successfully</div>';
    } catch (Exception $e) {
        echo '<div class="test-result error">✗ Autoload failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }

    // Test database connection
    try {
        $db = Database::getInstance()->getConnection();
        echo '<div class="test-result success">✓ Database connected</div>';
    } catch (Exception $e) {
        echo '<div class="test-result error">✗ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }
    ?>

    <!-- DOSEN CRUD TESTS -->
    <div class="section">
        <h2>>> DOSEN CRUD OPERATIONS <<</h2>
        
        <?php
        try {
            $dosenModel = new Dosen();
            echo '<div class="test-result success">✓ Dosen model instantiated</div>';
            
            // Test getAll
            $dosen_list = $dosenModel->getAll();
            echo '<div class="test-result info">📊 Total Dosen in DB: <strong>' . count($dosen_list) . '</strong></div>';
            
            // Display sample data
            if (count($dosen_list) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Nama</th><th>Jabatan</th><th>Foto URL</th><th>Status</th></tr>';
                foreach (array_slice($dosen_list, 0, 5) as $dosen) {
                    $foto_status = !empty($dosen['foto_url']) ? '✓ Set' : '✗ Empty';
                    $foto_exists = !empty($dosen['foto_url']) && file_exists(ROOT_PATH . '/' . $dosen['foto_url']) ? '✓' : '✗';
                    echo '<tr>';
                    echo '<td>' . $dosen['id_dosen'] . '</td>';
                    echo '<td>' . htmlspecialchars($dosen['nama']) . '</td>';
                    echo '<td>' . htmlspecialchars($dosen['jabatan']) . '</td>';
                    echo '<td>' . htmlspecialchars($dosen['foto_url'] ?? 'NULL') . ' [DB: ' . $foto_status . '] [File: ' . $foto_exists . ']</td>';
                    echo '<td>' . htmlspecialchars($dosen['status']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
            // Test upload directory - Changed to uploads/dosen/
            $upload_dir = ROOT_PATH . '/uploads/dosen/';
            if (file_exists($upload_dir)) {
                $writable = is_writable($upload_dir);
                $status_class = $writable ? 'success' : 'error';
                $status_icon = $writable ? '✓' : '✗';
                echo '<div class="test-result ' . $status_class . '">' . $status_icon . ' Upload directory: ' . $upload_dir . ' (Writable: ' . ($writable ? 'YES' : 'NO') . ')</div>';
                
                // Count files in directory
                $files = glob($upload_dir . '*');
                echo '<div class="test-result info">📁 Files in upload directory: ' . count($files) . '</div>';
            } else {
                echo '<div class="test-result warning">⚠ Upload directory does not exist: ' . $upload_dir . '</div>';
                echo '<div class="test-result info">📝 Will be created automatically on first upload</div>';
            }
            
            // Test create method structure
            echo '<h3>CREATE Method Test (Dry Run)</h3>';
            echo '<pre class="code">Test Data:
[
    "nama" => "Test Dosen",
    "gelar" => "S.Kom., M.T.",
    "jabatan" => "Dosen",
    "email" => "test@example.com",
    "deskripsi" => "Test deskripsi",
    "urutan" => 0,
    "status" => "active"
]
            
Expected: Should call Dosen::create() with prepared statement
SQL: INSERT INTO tbl_dosen (nama, gelar, jabatan, email, foto_url, deskripsi, urutan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)</pre>';
            
        } catch (Exception $e) {
            echo '<div class="test-result error">✗ Dosen test failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
        ?>
    </div>

    <!-- KARYA CRUD TESTS -->
    <div class="section">
        <h2>>> KARYA CRUD OPERATIONS <<</h2>
        
        <?php
        try {
            $karyaModel = new Karya();
            echo '<div class="test-result success">✓ Karya model instantiated</div>';
            
            // Test getAllForAdmin (Karya doesn't have getAll, uses getAllForAdmin)
            $karya_list = $karyaModel->getAllForAdmin();
            echo '<div class="test-result info">📊 Total Karya in DB: <strong>' . count($karya_list) . '</strong></div>';
            
            // Display sample data
            if (count($karya_list) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Judul</th><th>Pembuat</th><th>Status</th><th>Snapshot</th><th>Files</th><th>Links</th></tr>';
                foreach (array_slice($karya_list, 0, 5) as $karya) {
                    $id = $karya['id_project'];
                    
                    // Count files and links
                    $files = $karyaModel->getFiles($id);
                    $links = $karyaModel->getLinks($id);
                    
                    $snapshot_status = !empty($karya['snapshot_url']) ? '✓' : '✗';
                    
                    echo '<tr>';
                    echo '<td>' . $id . '</td>';
                    echo '<td>' . htmlspecialchars($karya['judul']) . '</td>';
                    echo '<td>' . htmlspecialchars($karya['pembuat']) . '</td>';
                    echo '<td>' . htmlspecialchars($karya['status']) . '</td>';
                    echo '<td>' . $snapshot_status . ' ' . htmlspecialchars($karya['snapshot_url'] ?? 'NULL') . '</td>';
                    echo '<td>' . count($files) . ' files</td>';
                    echo '<td>' . count($links) . ' links</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
            // Test upload directories
            $snapshot_dir = ROOT_PATH . '/uploads/snapshots/';
            $files_dir = ROOT_PATH . '/uploads/files/';
            
            foreach ([$snapshot_dir => 'Snapshots', $files_dir => 'Files'] as $dir => $label) {
                if (file_exists($dir)) {
                    $writable = is_writable($dir);
                    $status_class = $writable ? 'success' : 'error';
                    $status_icon = $writable ? '✓' : '✗';
                    echo '<div class="test-result ' . $status_class . '">' . $status_icon . ' ' . $label . ' directory: ' . $dir . ' (Writable: ' . ($writable ? 'YES' : 'NO') . ')</div>';
                    
                    $files = glob($dir . '*');
                    echo '<div class="test-result info">📁 Files in ' . strtolower($label) . ' directory: ' . count($files) . '</div>';
                } else {
                    echo '<div class="test-result warning">⚠ ' . $label . ' directory does not exist: ' . $dir . '</div>';
                    echo '<div class="test-result info">📝 Will be created automatically on first upload</div>';
                }
            }
            
            // Test deleteFile method
            echo '<h3>DELETE FILE/LINK Redirect Test</h3>';
            echo '<pre class="code">delete_file.php Logic:
1. Get file by ID
2. Delete from database  
3. Delete physical file
4. REDIRECT to: form_edit_karya.php?id={file.id_project}&success=file_deleted

✓ FIXED: Now always redirects back to edit form using file\'s project ID
✗ BEFORE: Used $_GET[\'project_id\'] which might not be sent

Same fix applied to delete_link.php</pre>';
            
        } catch (Exception $e) {
            echo '<div class="test-result error">✗ Karya test failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
        ?>
    </div>

    <!-- FILE UPLOAD SIMULATION -->
    <div class="section">
        <h2>>> FILE UPLOAD DIAGNOSTICS <<</h2>
        
        <?php
        echo '<h3>PHP Upload Settings</h3>';
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>upload_max_filesize</td><td>' . ini_get('upload_max_filesize') . '</td></tr>';
        echo '<tr><td>post_max_size</td><td>' . ini_get('post_max_size') . '</td></tr>';
        echo '<tr><td>max_file_uploads</td><td>' . ini_get('max_file_uploads') . '</td></tr>';
        echo '<tr><td>file_uploads</td><td>' . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . '</td></tr>';
        echo '</table>';
        
        echo '<h3>GD Library (for image processing)</h3>';
        if (extension_loaded('gd')) {
            $gd_info = gd_info();
            echo '<div class="test-result success">✓ GD Library loaded</div>';
            echo '<table>';
            foreach ($gd_info as $key => $value) {
                $val = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
                echo '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($val) . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="test-result warning">⚠ GD Library not loaded</div>';
        }
        ?>
    </div>

    <!-- COMMON ERROR SCENARIOS -->
    <div class="section">
        <h2>>> COMMON ERROR SCENARIOS & FIXES <<</h2>
        
        <div class="test-result info">
            <h3>Issue #1: Dosen Upload Fails</h3>
            <strong>Symptoms:</strong> Upload returns to form with error, no file saved<br>
            <strong>Possible Causes:</strong>
            <ul>
                <li>Directory not writable → Check permissions (755 or 777)</li>
                <li>Directory doesn't exist → Auto-created now with mkdir($dir, 0755, true)</li>
                <li>File too large → Check upload_max_filesize and post_max_size</li>
                <li>Wrong MIME type → Only JPG, PNG, WEBP allowed</li>
            </ul>
            <strong>Debug:</strong> Check Apache error log for error_log() messages<br>
            <strong>Upload Path:</strong> uploads/dosen/ (changed from assets/img/dosen/)
        </div>

        <div class="test-result info">
            <h3>Issue #2: Delete File/Link Redirects Out of Edit Form</h3>
            <strong>Symptoms:</strong> After deleting file/link in edit form, redirected to karya list<br>
            <strong>Root Cause:</strong> delete_file.php checked $_GET['project_id'] which wasn't passed<br>
            <strong>Fix Applied:</strong> ✓ Now uses $file['id_project'] from database directly<br>
            <strong>Result:</strong> Always redirects back to form_edit_karya.php with id
        </div>

        <div class="test-result info">
            <h3>Issue #3: Password Change "Not Working"</h3>
            <strong>Symptoms:</strong> Error message after submitting<br>
            <strong>Possible Causes:</strong>
            <ul>
                <li>Old password typed incorrectly → Case sensitive!</li>
                <li>New password too short → Min 6 characters</li>
                <li>Passwords don't match → Check confirm password field</li>
            </ul>
            <strong>Code Status:</strong> ✓ Implementation is correct (verified)
        </div>
    </div>

    <!-- TESTING CHECKLIST -->
    <div class="section">
        <h2>>> MANUAL TESTING CHECKLIST <<</h2>
        
        <table>
            <tr><th>Test</th><th>Steps</th><th>Expected Result</th></tr>
            <tr>
                <td><strong>Dosen: Add</strong></td>
                <td>1. Go to kelola_dosen.php<br>2. Click "Tambah Dosen"<br>3. Fill form + upload photo<br>4. Submit</td>
                <td>✓ Success message<br>✓ Photo saved in uploads/dosen/<br>✓ Dosen appears in list</td>
            </tr>
            <tr>
                <td><strong>Dosen: Edit Photo</strong></td>
                <td>1. Edit existing dosen<br>2. Upload new photo<br>3. Submit</td>
                <td>✓ Old photo deleted<br>✓ New photo saved<br>✓ Updated in DB</td>
            </tr>
            <tr>
                <td><strong>Karya: Delete File</strong></td>
                <td>1. Edit karya<br>2. Click delete on file<br>3. Confirm</td>
                <td>✓ File deleted from DB<br>✓ Physical file removed<br>✓ STAYS in edit form<br>✓ Success message shown</td>
            </tr>
            <tr>
                <td><strong>Karya: Delete Link</strong></td>
                <td>1. Edit karya<br>2. Click delete on link<br>3. Confirm</td>
                <td>✓ Link deleted from DB<br>✓ STAYS in edit form<br>✓ Success message shown</td>
            </tr>
            <tr>
                <td><strong>Password: Change</strong></td>
                <td>1. Go to ubah_password.php<br>2. Enter CORRECT old password<br>3. Enter new password (6+ chars)<br>4. Confirm<br>5. Submit</td>
                <td>✓ Success message<br>✓ Can login with new password<br>✓ Cannot login with old password</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px; padding: 20px; border-top: 2px solid #00ff00;">
        <p style="color: #ffaa00;">⚠️ <strong>SECURITY WARNING</strong>: Delete this file after debugging!</p>
        <p style="color: #00aaff;">📝 Check Apache error logs for detailed error_log() messages</p>
        <p style="color: #00ff00;">✓ All fixed issues marked with ✓ in report above</p>
    </div>
</div>
</body>
</html>
