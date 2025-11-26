<?php
/**
 * Debug System - OOP Refactoring Health Check
 * 
 * Tujuan: Memvalidasi semua komponen OOP sudah terhubung dengan benar
 * READ-ONLY: Script ini tidak mengubah data apapun
 * 
 * Letakkan di: /opt/lampp/htdocs/portal_tpl_oop/debug_system.php
 */

// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Define root path
define('ROOT_PATH', __DIR__);

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Check - Portal TPL OOP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { font-size: 16px; opacity: 0.9; }
        .content { padding: 30px; }
        .section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .section-header {
            background: #f5f5f5;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 18px;
            border-bottom: 2px solid #667eea;
        }
        .section-body { padding: 20px; }
        .check-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .check-item:last-child { border-bottom: none; }
        .check-label { font-weight: 500; color: #333; }
        .check-value { font-family: 'Courier New', monospace; color: #666; }
        .status {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status-ok {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .error-detail {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 13px;
            border-left: 4px solid #dc3545;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .count-badge {
            background: #667eea;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔍 Portal TPL - System Health Check</h1>
        <p>OOP Refactoring Validation & Debugging Tool</p>
        <p style="font-size: 13px; margin-top: 10px;">⚠️ READ-ONLY MODE - No data will be modified</p>
    </div>
    
    <div class="content">
        
        <!-- 1. PHP Environment Check -->
        <div class="section">
            <div class="section-header">📋 1. PHP Environment</div>
            <div class="section-body">
                <?php
                $phpVersion = phpversion();
                $phpOk = version_compare($phpVersion, '7.4.0', '>=');
                ?>
                <div class="check-item">
                    <span class="check-label">PHP Version</span>
                    <span>
                        <span class="check-value"><?php echo $phpVersion; ?></span>
                        <span class="status <?php echo $phpOk ? 'status-ok' : 'status-error'; ?>">
                            <?php echo $phpOk ? '✓ OK' : '✗ OUTDATED'; ?>
                        </span>
                    </span>
                </div>
                
                <?php
                $requiredExtensions = ['mysqli', 'session', 'json', 'gd'];
                foreach ($requiredExtensions as $ext):
                    $loaded = extension_loaded($ext);
                ?>
                <div class="check-item">
                    <span class="check-label">Extension: <?php echo $ext; ?></span>
                    <span class="status <?php echo $loaded ? 'status-ok' : 'status-error'; ?>">
                        <?php echo $loaded ? '✓ LOADED' : '✗ MISSING'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2. Autoload & Database Connection -->
        <div class="section">
            <div class="section-header">🔌 2. Autoload & Database Connection</div>
            <div class="section-body">
                <?php
                $autoloadPath = ROOT_PATH . '/app/autoload.php';
                $autoloadExists = file_exists($autoloadPath);
                $autoloadError = null;
                $dbConnection = null;
                $dbError = null;
                
                if ($autoloadExists) {
                    try {
                        require_once $autoloadPath;
                        echo '<div class="check-item">';
                        echo '<span class="check-label">Autoload File</span>';
                        echo '<span class="status status-ok">✓ LOADED</span>';
                        echo '</div>';
                        
                        // Test database connection
                        try {
                            $dbConnection = Database::getInstance()->getConnection();
                            if ($dbConnection && $dbConnection->ping()) {
                                echo '<div class="check-item">';
                                echo '<span class="check-label">Database Connection</span>';
                                echo '<span class="status status-ok">✓ CONNECTED</span>';
                                echo '</div>';
                                
                                // Show database info
                                echo '<div class="check-item">';
                                echo '<span class="check-label">Database Host</span>';
                                echo '<span class="check-value">' . $dbConnection->host_info . '</span>';
                                echo '</div>';
                                
                                echo '<div class="check-item">';
                                echo '<span class="check-label">Character Set</span>';
                                echo '<span class="check-value">' . $dbConnection->character_set_name() . '</span>';
                                echo '</div>';
                            } else {
                                throw new Exception("Connection failed");
                            }
                        } catch (Exception $e) {
                            $dbError = $e->getMessage();
                            echo '<div class="check-item">';
                            echo '<span class="check-label">Database Connection</span>';
                            echo '<span class="status status-error">✗ FAILED</span>';
                            echo '</div>';
                            echo '<div class="error-detail">Error: ' . htmlspecialchars($dbError) . '</div>';
                        }
                    } catch (Exception $e) {
                        $autoloadError = $e->getMessage();
                        echo '<div class="check-item">';
                        echo '<span class="check-label">Autoload File</span>';
                        echo '<span class="status status-error">✗ ERROR</span>';
                        echo '</div>';
                        echo '<div class="error-detail">' . htmlspecialchars($autoloadError) . '</div>';
                    }
                } else {
                    echo '<div class="check-item">';
                    echo '<span class="check-label">Autoload File</span>';
                    echo '<span class="status status-error">✗ NOT FOUND</span>';
                    echo '</div>';
                    echo '<div class="error-detail">Path: ' . htmlspecialchars($autoloadPath) . '</div>';
                }
                ?>
            </div>
        </div>

        <!-- 3. Model Health Check -->
        <?php if ($dbConnection): ?>
        <div class="section">
            <div class="section-header">🎯 3. OOP Models Health Check</div>
            <div class="section-body">
                <table>
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Status</th>
                            <th>Data Count</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $models = [
                            'Dosen' => 'getAll',
                            'Matkul' => 'getAll',
                            'Faq' => 'getAll',
                            'Category' => 'getAll',
                            'About' => 'getAll',
                            'Karya' => 'getAll',
                            'Auth' => null, // No getAll method
                            'Rating' => 'getAll'
                        ];
                        
                        foreach ($models as $modelName => $method):
                            $status = 'error';
                            $count = '-';
                            $detail = '';
                            
                            try {
                                if (!class_exists($modelName)) {
                                    throw new Exception("Class not found");
                                }
                                
                                $model = new $modelName();
                                $status = 'ok';
                                $detail = 'Class instantiated';
                                
                                if ($method && method_exists($model, $method)) {
                                    $data = $model->$method();
                                    $count = is_array($data) ? count($data) : '-';
                                    $detail = "Method $method() executed successfully";
                                } elseif ($modelName === 'Auth') {
                                    $detail = 'Auth model (no getAll method)';
                                    $count = '✓';
                                }
                                
                            } catch (Exception $e) {
                                $status = 'error';
                                $detail = $e->getMessage();
                            }
                        ?>
                        <tr>
                            <td><strong><?php echo $modelName; ?></strong></td>
                            <td>
                                <span class="status <?php echo $status === 'ok' ? 'status-ok' : 'status-error'; ?>">
                                    <?php echo $status === 'ok' ? '✓ OK' : '✗ FAILED'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($count !== '-' && $count !== '✓'): ?>
                                    <span class="count-badge"><?php echo $count; ?> records</span>
                                <?php else: ?>
                                    <?php echo $count; ?>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 13px; color: <?php echo $status === 'ok' ? '#28a745' : '#dc3545'; ?>">
                                <?php echo htmlspecialchars($detail); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- 4. Critical Files Check -->
        <div class="section">
            <div class="section-header">📁 4. Critical Files Existence</div>
            <div class="section-body">
                <?php
                $criticalFiles = [
                    'Controllers' => [
                        'controllers/admin/proses_tambah_dosen.php',
                        'controllers/admin/proses_tambah_matkul.php',
                        'controllers/admin/proses_tambah_faq.php',
                        'controllers/admin/proses_login.php',
                        'controllers/admin/logout.php',
                        'controllers/public/proses_rating.php'
                    ],
                    'Views' => [
                        'views/admin/index.php',
                        'views/admin/kelola_dosen.php',
                        'views/admin/kelola_karya.php',
                        'views/public/index.php'
                    ],
                    'Models' => [
                        'app/models/Database.php',
                        'app/models/Dosen.php',
                        'app/models/Auth.php',
                        'app/models/Karya.php'
                    ],
                    'Assets' => [
                        'assets/css/styles.css',
                        'assets/js/script.js'
                    ]
                ];
                
                foreach ($criticalFiles as $category => $files):
                ?>
                    <div class="info-box">
                        <strong><?php echo $category; ?></strong>
                    </div>
                    <?php
                    foreach ($files as $file):
                        $fullPath = ROOT_PATH . '/' . $file;
                        $exists = file_exists($fullPath);
                    ?>
                    <div class="check-item">
                        <span class="check-label" style="font-size: 13px;"><?php echo $file; ?></span>
                        <span class="status <?php echo $exists ? 'status-ok' : 'status-error'; ?>">
                            <?php echo $exists ? '✓ FOUND' : '✗ MISSING'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 5. Session Test -->
        <div class="section">
            <div class="section-header">🔐 5. Session Management</div>
            <div class="section-body">
                <?php
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $sessionStatus = session_status();
                $sessionStatusText = [
                    PHP_SESSION_DISABLED => 'DISABLED',
                    PHP_SESSION_NONE => 'NONE',
                    PHP_SESSION_ACTIVE => 'ACTIVE'
                ];
                ?>
                <div class="check-item">
                    <span class="check-label">Session Status</span>
                    <span class="status <?php echo $sessionStatus === PHP_SESSION_ACTIVE ? 'status-ok' : 'status-warning'; ?>">
                        <?php echo $sessionStatusText[$sessionStatus]; ?>
                    </span>
                </div>
                
                <div class="check-item">
                    <span class="check-label">Session ID</span>
                    <span class="check-value"><?php echo session_id() ?: 'Not set'; ?></span>
                </div>
                
                <div class="check-item">
                    <span class="check-label">Session Save Path</span>
                    <span class="check-value"><?php echo session_save_path(); ?></span>
                </div>
            </div>
        </div>

        <!-- 6. Summary -->
        <div class="section">
            <div class="section-header">📊 6. Summary</div>
            <div class="section-body">
                <?php
                $timestamp = date('Y-m-d H:i:s');
                ?>
                <div class="info-box" style="background: #d4edda; border-color: #28a745;">
                    <strong style="color: #155724;">✓ System Check Completed</strong>
                    <p style="margin-top: 10px; color: #155724;">
                        Timestamp: <?php echo $timestamp; ?><br>
                        PHP Version: <?php echo phpversion(); ?><br>
                        Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                    </p>
                </div>
                
                <div class="info-box">
                    <strong>ℹ️ Next Steps:</strong>
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Jika ada status ERROR, periksa konfigurasi dan path file</li>
                        <li>Pastikan semua model menampilkan status OK</li>
                        <li>Test authentication flow melalui admin panel</li>
                        <li>Verifikasi CRUD operations untuk setiap entitas</li>
                        <li>⚠️ <strong>HAPUS file ini setelah selesai debugging</strong> (security risk di production)</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
    
    <div class="footer">
        <p>Portal TPL OOP Refactoring - Debug System v1.0</p>
        <p style="margin-top: 5px; color: #dc3545; font-weight: 600;">
            ⚠️ Security Warning: Delete this file in production environment
        </p>
    </div>
</div>
</body>
</html>
<?php
// End output buffering and send
ob_end_flush();
?>
