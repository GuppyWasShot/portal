<?php
/**
 * Password Change Debug Tool
 * 
 * Purpose: Diagnose password change issues
 * Test Auth::changePassword() method thoroughly
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
    <title>Password Change Debugger</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Monaco', 'Courier New', monospace;
            background: #1a1a1a;
            color: #00ff00;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
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
        .code { color: #ff00ff; font-family: monospace; }
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
        .test-form {
            background: #1a1a2e;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px 0;
            background: #000;
            border: 1px solid #00ff00;
            color: #00ff00;
            font-family: monospace;
        }
        button {
            padding: 10px 20px;
            background: #003300;
            color: #00ff00;
            border: 1px solid #00ff00;
            cursor: pointer;
            border-radius: 4px;
            font-family: monospace;
            font-weight: bold;
        }
        button:hover { background: #005500; }
    </style>
</head>
<body>
<div class="container">
    <h1>>> PASSWORD CHANGE DEBUGGER <<</h1>
    <p style="color: #ffaa00;">⚠️ Debug Interface - <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php
    // Check session status
    echo '<div class="section">';
    echo '<h2>>> SESSION STATUS <<</h2>';
    
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        echo '<div class="test-result success">✓ Admin is logged in</div>';
        echo '<table>';
        echo '<tr><th>Session Variable</th><th>Value</th></tr>';
        
        $session_vars = ['admin_logged_in', 'id_admin', 'admin_id', 'username', 'admin_username'];
        foreach ($session_vars as $var) {
            $value = isset($_SESSION[$var]) ? $_SESSION[$var] : 'NOT SET';
            echo '<tr><td>' . $var . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        echo '</table>';
    } else {
        echo '<div class="test-result error">✗ Admin NOT logged in - session invalid</div>';
        echo '<p style="color: #ffaa00;">You must login first to test password change</p>';
    }
    echo '</div>';

    // Check Auth model
    echo '<div class="section">';
    echo '<h2>>> AUTH MODEL CHECK <<</h2>';
    
    try {
        $auth = new Auth();
        echo '<div class="test-result success">✓ Auth model instantiated</div>';
        
        // Check if method exists
        if (method_exists($auth, 'changePassword')) {
            echo '<div class="test-result success">✓ Auth::changePassword() method exists</div>';
        } else {
            echo '<div class="test-result error">✗ Auth::changePassword() method NOT FOUND</div>';
        }
        
        if (method_exists($auth, 'getCurrentAdmin')) {
            echo '<div class="test-result success">✓ Auth::getCurrentAdmin() method exists</div>';
            
            if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                $currentAdmin = $auth->getCurrentAdmin();
                if ($currentAdmin) {
                    echo '<div class="test-result success">✓ Current admin data retrieved</div>';
                    echo '<pre>';
                    print_r($currentAdmin);
                    echo '</pre>';
                } else {
                    echo '<div class="test-result error">✗ getCurrentAdmin() returned NULL</div>';
                }
            }
        }
        
    } catch (Exception $e) {
        echo '<div class="test-result error">✗ Auth model error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';

    // Check database
    echo '<div class="section">';
    echo '<h2>>> DATABASE CHECK <<</h2>';
    
    try {
        $db = Database::getInstance()->getConnection();
        echo '<div class="test-result success">✓ Database connection OK</div>';
        
        // Check admin table
        $result = $db->query("SELECT id_admin, username, password FROM tbl_admin LIMIT 1");
        if ($result) {
            $admin_sample = $result->fetch_assoc();
            echo '<div class="test-result success">✓ tbl_admin table accessible</div>';
            echo '<table>';
            echo '<tr><th>Column</th><th>Sample Value</th></tr>';
            echo '<tr><td>id_admin</td><td>' . $admin_sample['id_admin'] . '</td></tr>';
            echo '<tr><td>username</td><td>' . htmlspecialchars($admin_sample['username']) . '</td></tr>';
            echo '<tr><td>password hash</td><td>' . substr($admin_sample['password'], 0, 20) . '... (length: ' . strlen($admin_sample['password']) . ')</td></tr>';
            echo '</table>';
            
            // Check if password is hashed
            if (strlen($admin_sample['password']) >= 60 && substr($admin_sample['password'], 0, 4) === '$2y$') {
                echo '<div class="test-result success">✓ Password appears to be bcrypt hashed</div>';
            } else {
                echo '<div class="test-result error">✗ Password might not be properly hashed!</div>';
            }
        }
    } catch (Exception $e) {
        echo '<div class="test-result error">✗ Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';

    // Password change form
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        echo '<div class="section">';
        echo '<h2>>> TEST PASSWORD CHANGE <<</h2>';
        
        // Process test if submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_password_change'])) {
            echo '<div class="test-result info">Testing password change...</div>';
            
            $test_old = $_POST['test_old_password'];
            $test_new = $_POST['test_new_password'];
            $admin_id = $_POST['admin_id'];
            
            echo '<pre>';
            echo "Input Data:\n";
            echo "- Admin ID: " . $admin_id . "\n";
            echo "- Old Password: " . str_repeat('*', strlen($test_old)) . " (length: " . strlen($test_old) . ")\n";
            echo "- New Password: " . str_repeat('*', strlen($test_new)) . " (length: " . strlen($test_new) . ")\n";
            echo '</pre>';
            
            try {
                $auth = new Auth();
                $result = $auth->changePassword($admin_id, $test_old, $test_new);
                
                echo '<pre>';
                echo "Result:\n";
                print_r($result);
                echo '</pre>';
                
                if ($result['success']) {
                    echo '<div class="test-result success">✓ PASSWORD CHANGE SUCCESS!</div>';
                    echo '<p style="color: #00ff00;">Message: ' . $result['message'] . '</p>';
                } else {
                    echo '<div class="test-result error">✗ PASSWORD CHANGE FAILED</div>';
                    echo '<p style="color: #ff4444;">Error: ' . $result['message'] . '</p>';
                    
                    // Provide specific guidance
                    echo '<div class="test-result warning">';
                    echo '<strong>Troubleshooting:</strong><br>';
                    switch ($result['message']) {
                        case 'empty_field':
                            echo '→ One or more fields are empty<br>';
                            echo '→ Check that all password fields are filled';
                            break;
                        case 'password_too_short':
                            echo '→ New password is too short<br>';
                            echo '→ Minimum 6 characters required';
                            break;
                        case 'wrong_old_password':
                            echo '→ Old password is INCORRECT<br>';
                            echo '→ Double-check the current password<br>';
                            echo '→ Passwords are case-sensitive';
                            break;
                        case 'admin_not_found':
                            echo '→ Admin ID not found in database<br>';
                            echo '→ Check session data';
                            break;
                        case 'database_error':
                            echo '→ Database operation failed<br>';
                            echo '→ Check error logs for details';
                            break;
                    }
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-result error">✗ Exception: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
        }
        
        // Show form
        $currentAdmin = $auth->getCurrentAdmin();
        $admin_id = $currentAdmin['id_admin'] ?? 0;
        
        echo '<div class="test-form">';
        echo '<h3 style="color: #00aaff; margin-bottom: 15px;">Test Form</h3>';
        echo '<form method="POST">';
        echo '<input type="hidden" name="test_password_change" value="1">';
        
        echo '<label style="color: #00ff00;">Admin ID (from session):</label>';
        echo '<input type="number" name="admin_id" value="' . $admin_id . '" readonly>';
        
        echo '<label style="color: #00ff00;">Old Password (Current):</label>';
        echo '<input type="password" name="test_old_password" required>';
        
        echo '<label style="color: #00ff00;">New Password (min 6 chars):</label>';
        echo '<input type="password" name="test_new_password" required>';
        
        echo '<button type="submit">Test Password Change</button>';
        echo '</form>';
        echo '</div>';
        
        echo '</div>';
    }

    // Check controller file
    echo '<div class="section">';
    echo '<h2>>> CONTROLLER FILE CHECK <<</h2>';
    
    $controller_path = __DIR__ . '/controllers/admin/proses_ubah_password.php';
    if (file_exists($controller_path)) {
        echo '<div class="test-result success">✓ proses_ubah_password.php exists</div>';
        echo '<p>Path: ' . $controller_path . '</p>';
        
        // Check if readable
        if (is_readable($controller_path)) {
            echo '<div class="test-result success">✓ File is readable</div>';
        } else {
            echo '<div class="test-result error">✗ File is NOT readable</div>';
        }
    } else {
        echo '<div class="test-result error">✗ proses_ubah_password.php NOT FOUND</div>';
    }
    
    $view_path = __DIR__ . '/views/admin/ubah_password.php';
    if (file_exists($view_path)) {
        echo '<div class="test-result success">✓ ubah_password.php (view) exists</div>';
    } else {
        echo '<div class="test-result error">✗ ubah_password.php (view) NOT FOUND</div>';
    }
    echo '</div>';

    // Common issues
    echo '<div class="section">';
    echo '<h2>>> COMMON ISSUES & SOLUTIONS <<</h2>';
    
    echo '<div class="test-result info">';
    echo '<strong>Issue #1: "Wrong old password" error</strong><br>';
    echo '→ Most common cause: User typing wrong password<br>';
    echo '→ Passwords are case-sensitive<br>';
    echo '→ Check for extra spaces<br>';
    echo '→ Try resetting password via database if forgotten<br>';
    echo '</div>';
    
    echo '<div class="test-result info">';
    echo '<strong>Issue #2: Session expired</strong><br>';
    echo '→ Logout and login again<br>';
    echo '→ Check session configuration<br>';
    echo '→ Verify session cookie settings<br>';
    echo '</div>';
    
    echo '<div class="test-result info">';
    echo '<strong>Issue #3: Form not submitting</strong><br>';
    echo '→ Check form action path<br>';
    echo '→ Verify method="POST"<br>';
    echo '→ Check browser console for JavaScript errors<br>';
    echo '</div>';
    
    echo '<div class="test-result info">';
    echo '<strong>Issue #4: Password not updating</strong><br>';
    echo '→ Check database write permissions<br>';
    echo '→ Verify tbl_admin table structure<br>';
    echo '→ Check error logs<br>';
    echo '</div>';
    echo '</div>';

    // Manual SQL test
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        echo '<div class="section">';
        echo '<h2>>> MANUAL PASSWORD RESET (EMERGENCY) <<</h2>';
        echo '<div class="test-result warning">';
        echo '<strong>⚠️ Use this ONLY if normal password change fails</strong><br><br>';
        
        $admin_id = $currentAdmin['id_admin'] ?? 1;
        $new_hash = password_hash('newpassword123', PASSWORD_DEFAULT);
        
        echo 'Run this SQL in phpMyAdmin to reset password to "newpassword123":<br>';
        echo '<pre class="code">';
        echo "UPDATE tbl_admin SET password = '$new_hash' WHERE id_admin = $admin_id;";
        echo '</pre>';
        echo '<p style="color: #ffaa00;">After running SQL, login with password: <strong>newpassword123</strong></p>';
        echo '</div>';
        echo '</div>';
    }
    ?>

    <div style="margin-top: 30px; padding: 20px; border-top: 2px solid #00ff00;">
        <p style="color: #ffaa00;">⚠️ <strong>SECURITY WARNING</strong>: Delete this file after debugging!</p>
        <p style="color: #00aaff;">📝 Check Apache error logs: /var/log/apache2/error.log or xampp/apache/logs/error.log</p>
    </div>
</div>
</body>
</html>
