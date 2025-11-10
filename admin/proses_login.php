<?php
declare(strict_types=1);
session_start();
include '../config/db_connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Ambil IP address pengguna
$ip_address = $_SERVER['REMOTE_ADDR'];

// Cek apakah IP sedang terkunci (database-based, bukan session)
if (isIPLocked($conn, $ip_address)) {
    header("Location: login.php?error=terkunci");
    exit();
}

// Validasi input POST
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: login.php?error=input_kosong");
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Validasi input tidak boleh kosong
if (empty($username) || empty($password)) {
    header("Location: login.php?error=input_kosong");
    exit();
}

// Cek username di database (Gunakan Prepared Statement)
$stmt = $conn->prepare("SELECT id_admin, username, password FROM tbl_admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    
    // Verifikasi password HASH
    if (password_verify($password, $admin['password'])) {
        // --- LOGIN BERHASIL ---
        
        // Catat log BERHASIL
        logLoginAttempt($conn, $username, $ip_address, 'Success');
        
        // Reset counter gagal dari database
        resetFailedAttempts($conn, $ip_address);
        
        // Buat session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_username'] = $admin['username'];
        
        // Log aktivitas login
        logActivity($conn, $admin['id_admin'], $admin['username'], 'Login ke sistem');
        
        // Arahkan ke dashboard
        header("Location: index.php");
        exit();
        
    } else {
        // Password salah
        gagalLogin($conn, $username, $ip_address);
    }
    
} else {
    // Username tidak ditemukan
    gagalLogin($conn, $username, $ip_address);
}

// Fungsi untuk menangani login gagal
function gagalLogin($conn, $username, $ip) {
    // Catat log GAGAL
    logLoginAttempt($conn, $username, $ip, 'Failed');
    
    // Cek berapa kali sudah gagal
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as attempts 
         FROM tbl_admin_logs 
         WHERE ip_address = ? 
         AND status = 'Failed' 
         AND log_time > DATE_SUB(NOW(), INTERVAL 10 MINUTE)"
    );
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $remaining = 5 - $row['attempts'];
    
    if ($remaining <= 0) {
        header("Location: login.php?error=terkunci");
    } else {
        header("Location: login.php?error=gagal&sisa=$remaining");
    }
    exit();
}

$stmt->close();
$conn->close();
?>