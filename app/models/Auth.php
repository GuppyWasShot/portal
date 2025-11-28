<?php
/**
 * Kelas Auth
 * Buat ngatur semua hal yang berhubungan dengan login, logout, dan keamanan admin
 * 
 * Cara pake:
 * $auth = new Auth();
 * $auth->login($username, $password, $ip);
 */
require_once __DIR__ . '/Database.php';

class Auth {
    private $db;
    
    // Konfigurasi keamanan
    private $max_failed_attempts = 5;  // maksimal salah login 5 kali
    private $lockout_minutes = 10;      // dikunci 10 menit kalo kebanyakan salah
    
    /**
     * Constructor - dipanggil otomatis pas bikin object
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Fungsi buat login admin
     * 
     * @param string $username Username yang diinput
     * @param string $password Password asli (belum di-hash)
     * @param string $ip_address IP address user
     * @return array Hasil login (success/gagal)
     */
    public function login($username, $password, $ip_address) {
        // Cek dulu IP-nya udah kena lock belum
        if ($this->isIPLocked($ip_address)) {
            return [
                'success' => false,
                'message' => 'terkunci',
                'admin' => null
            ];
        }
        
        // Validasi input dulu, jangan sampe kosong
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'input_kosong',
                'admin' => null
            ];
        }
        
        try {
            // Ambil data admin dari database
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
            
            // Cocokkin password yang diinput sama yang di database
            if (!password_verify($password, $admin['password'])) {
                $this->logLoginAttempt($username, $ip_address, 'Failed');
                return [
                    'success' => false,
                    'message' => 'gagal',
                    'admin' => null
                ];
            }
            
            // Kalo password bener, login berhasil!
            $this->logLoginAttempt($username, $ip_address, 'Success');
            $this->resetFailedAttempts($ip_address);
            
            // Bikin session buat nyimpen data login
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Catat aktivitas login ke database
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
     * Fungsi buat logout admin
     * 
     * @return bool True kalo berhasil logout
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Catat aktivitas logout sebelum session dihapus
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
        
        // Hapus semua data session
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
     * Cek apakah user udah login apa belum
     * 
     * @return bool True kalo udah login
     */
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Wajib login dulu sebelum akses halaman ini
     * Kalo belum login, langsung diarahin ke halaman login
     * 
     * @param string $redirect_url URL tujuan kalo belum login
     * @return bool True kalo udah login
     */
    public function requireAuth($redirect_url = '../../views/admin/login.php?error=belum_login') {
        if (!$this->isLoggedIn()) {
            header("Location: $redirect_url");
            exit();
        }
        return true;
    }
    
    /**
     * Ambil data admin yang lagi login sekarang
     * 
     * @return array|null Data admin atau null kalo belum login
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
     * Ganti password admin
     * 
     * @param int $admin_id ID admin yang mau ganti password
     * @param string $old_password Password lama (belum di-hash)
     * @param string $new_password Password baru (belum di-hash)
     * @return array Hasil (success/gagal beserta pesannya)
     */
    public function changePassword($admin_id, $old_password, $new_password) {
        // Cek dulu inputannya jangan sampe ada yang kosong
        if (empty($old_password) || empty($new_password)) {
            return ['success' => false, 'message' => 'empty_field'];
        }
        
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'password_too_short'];
        }
        
        try {
            // Ambil password hash yang sekarang dari database
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
            
            // Cek password lama bener apa ngga
            if (!password_verify($old_password, $admin['password'])) {
                return ['success' => false, 'message' => 'wrong_old_password'];
            }
            
            // Hash password baru biar aman
            $new_hash = $this->hashPassword($new_password);
            
            // Update password di database
            $stmt = $this->db->prepare(
                "UPDATE tbl_admin SET password = ? WHERE id_admin = ?"
            );
            $stmt->bind_param("si", $new_hash, $admin_id);
            $success = $stmt->execute();
            $stmt->close();
            
            if ($success) {
                // Catat aktivitas ganti password
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
     * Enkripsi password pake bcrypt biar aman
     * 
     * @param string $password Password asli
     * @return string Password yang udah di-hash
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Cek password cocok sama hash-nya apa ngga
     * 
     * @param string $password Password asli
     * @param string $hash Password yang udah di-hash
     * @return bool True kalo cocok
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Cek IP udah dikunci belum gara-gara kebanyakan salah login
     * 
     * @param string $ip_address IP address yang mau dicek
     * @return bool True kalo udah dikunci
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
     * Catat percobaan login ke database (buat tracking)
     * 
     * @param string $username Username yang dicoba
     * @param string $ip_address IP address-nya
     * @param string $status 'Success' atau 'Failed'
     * @return bool True kalo berhasil dicatat
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
     * Reset counter percobaan login yang gagal buat IP tertentu
     * 
     * @param string $ip_address IP address yang mau di-reset
     * @return bool True kalo berhasil
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
     * Catat aktivitas admin (buat audit trail)
     * 
     * @param int $admin_id ID admin yang ngelakuin aktivitas
     * @param string $username Username admin
     * @param string $action Deskripsi aktivitas yang dilakukan
     * @param int|null $project_id ID project terkait (opsional)
     * @return bool True kalo berhasil dicatat
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
     * Cek berapa kali lagi boleh salah login sebelum dikunci
     * 
     * @param string $ip_address IP address yang dicek
     * @return int Sisa kesempatan login
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
