<?php
/**
 * Auth Class
 * Menangani semua operasi terkait Authentication dan Authorization
 * 
 * Usage:
 * $auth = new Auth();
 * $auth->login($username, $password, $ip);
 */
require_once __DIR__ . '/Database.php';

class Auth {
    private $db;
    
    // Configuration
    private $max_failed_attempts = 5;
    private $lockout_minutes = 10;
    
    /**
     * Constructor
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Login admin user
     * 
     * @param string $username Username
     * @param string $password Password (plain text)
     * @param string $ip_address IP address
     * @return array Result array with 'success' (bool), 'message' (string), 'admin' (array if success)
     */
    public function login($username, $password, $ip_address) {
        // Check if IP is locked
        if ($this->isIPLocked($ip_address)) {
            return [
                'success' => false,
                'message' => 'terkunci',
                'admin' => null
            ];
        }
        
        // Validate input
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'input_kosong',
                'admin' => null
            ];
        }
        
        try {
            // Get admin data
            $stmt = $this->db->prepare(
                "SELECT id_admin, username, password FROM tbl_admin WHERE username = ?"
            );
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $stmt->close();
                $this->logLoginAttempt($username, $ip_address, 'Failed');
                return [
                    'success' => false,
                    'message' => 'gagal',
                    'admin' => null
                ];
            }
            
            $admin = $result->fetch_assoc();
            $stmt->close();
            
            // Verify password
            if (!password_verify($password, $admin['password'])) {
                $this->logLoginAttempt($username, $ip_address, 'Failed');
                return [
                    'success' => false,
                    'message' => 'gagal',
                    'admin' => null
                ];
            }
            
            // Login successful
            $this->logLoginAttempt($username, $ip_address, 'Success');
            $this->resetFailedAttempts($ip_address);
            
            // Create session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Log activity
            $this->logActivity($admin['id_admin'], $admin['username'], 'Login ke sistem');
            
            return [
                'success' => true,
                'message' => 'success',
                'admin' => $admin
            ];
            
        } catch (Exception $e) {
            error_log("Auth::login() error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'database_error',
                'admin' => null
            ];
        }
    }
    
    /**
     * Logout admin user
     * 
     * @return bool True if successful
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Log activity before destroying session
        if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_username'])) {
            try {
                $this->logActivity(
                    $_SESSION['admin_id'], 
                    $_SESSION['admin_username'], 
                    'Logout dari sistem'
                );
            } catch (Exception $e) {
                error_log("Auth::logout() - Failed to log activity: " . $e->getMessage());
            }
        }
        
        // Destroy session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        return true;
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if logged in
     */
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Require authentication (middleware)
     * Redirect to login if not authenticated
     * 
     * @param string $redirect_url URL to redirect if not authenticated
     * @return bool True if authenticated
     */
    public function requireAuth($redirect_url = '../../views/admin/login.php?error=belum_login') {
        if (!$this->isLoggedIn()) {
            header("Location: $redirect_url");
            exit();
        }
        return true;
    }
    
    /**
     * Get current admin data from session
     * 
     * @return array|null Admin data or null if not logged in
     */
    public function getCurrentAdmin() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id_admin' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null
        ];
    }
    
    /**
     * Change admin password
     * 
     * @param int $admin_id Admin ID
     * @param string $old_password Old password (plain text)
     * @param string $new_password New password (plain text)
     * @return array Result with 'success' and 'message'
     */
    public function changePassword($admin_id, $old_password, $new_password) {
        // Validate input
        if (empty($old_password) || empty($new_password)) {
            return ['success' => false, 'message' => 'empty_field'];
        }
        
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'password_too_short'];
        }
        
        try {
            // Get current password hash
            $stmt = $this->db->prepare(
                "SELECT password, username FROM tbl_admin WHERE id_admin = ?"
            );
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $stmt->close();
                return ['success' => false, 'message' => 'admin_not_found'];
            }
            
            $admin = $result->fetch_assoc();
            $stmt->close();
            
            // Verify old password
            if (!password_verify($old_password, $admin['password'])) {
                return ['success' => false, 'message' => 'wrong_old_password'];
            }
            
            // Hash new password
            $new_hash = $this->hashPassword($new_password);
            
            // Update password
            $stmt = $this->db->prepare(
                "UPDATE tbl_admin SET password = ? WHERE id_admin = ?"
            );
            $stmt->bind_param("si", $new_hash, $admin_id);
            $success = $stmt->execute();
            $stmt->close();
            
            if ($success) {
                // Log activity
                $this->logActivity($admin_id, $admin['username'], 'Mengubah password akun');
                return ['success' => true, 'message' => 'success'];
            }
            
            return ['success' => false, 'message' => 'database_error'];
            
        } catch (Exception $e) {
            error_log("Auth::changePassword() error: " . $e->getMessage());
            return ['success' => false, 'message' => 'database_error'];
        }
    }
    
    /**
     * Hash password using password_hash
     * 
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password against hash
     * 
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if matches
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Check if IP is locked due to failed login attempts
     * 
     * @param string $ip_address IP address
     * @return bool True if locked
     */
    public function isIPLocked($ip_address) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as attempts 
                 FROM tbl_admin_logs 
                 WHERE ip_address = ? 
                 AND status = 'Failed' 
                 AND log_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
            );
            $stmt->bind_param("si", $ip_address, $this->lockout_minutes);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return (int)$row['attempts'] >= $this->max_failed_attempts;
        } catch (Exception $e) {
            error_log("Auth::isIPLocked() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log login attempt to database
     * 
     * @param string $username Username attempted
     * @param string $ip_address IP address
     * @param string $status 'Success' or 'Failed'
     * @return bool True if logged successfully
     */
    public function logLoginAttempt($username, $ip_address, $status) {
        try {
            $status = ($status === 'Success') ? 'Success' : 'Failed';
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_admin_logs (username_attempt, ip_address, status) 
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $username, $ip_address, $status);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Auth::logLoginAttempt() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reset failed login attempts for an IP
     * 
     * @param string $ip_address IP address
     * @return bool True if successful
     */
    public function resetFailedAttempts($ip_address) {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM tbl_admin_logs 
                 WHERE ip_address = ? 
                 AND status = 'Failed'"
            );
            $stmt->bind_param("s", $ip_address);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Auth::resetFailedAttempts() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log admin activity
     * 
     * @param int $admin_id Admin ID
     * @param string $username Admin username
     * @param string $action Action description
     * @param int|null $project_id Related project ID (optional)
     * @return bool True if logged successfully
     */
    public function logActivity($admin_id, $username, $action, $project_id = null) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_activity_logs (id_admin, username, action, id_project) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("issi", $admin_id, $username, $action, $project_id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Auth::logActivity() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get remaining login attempts before IP lockout
     * 
     * @param string $ip_address IP address
     * @return int Remaining attempts
     */
    public function getRemainingAttempts($ip_address) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as attempts 
                 FROM tbl_admin_logs 
                 WHERE ip_address = ? 
                 AND status = 'Failed' 
                 AND log_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
            );
            $stmt->bind_param("si", $ip_address, $this->lockout_minutes);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            $attempts = (int)$row['attempts'];
            $remaining = $this->max_failed_attempts - $attempts;
            return max(0, $remaining);
        } catch (Exception $e) {
            error_log("Auth::getRemainingAttempts() error: " . $e->getMessage());
            return 0;
        }
    }
}
