<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../views/admin/login.php?error=belum_login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_kategori.php?error=invalid_request");
    exit();
}

require_once __DIR__ . '/../../app/autoload.php';
require_once __DIR__ . '/../../config/db_connect.php';
$db = Database::getInstance()->getConnection();
$conn = $db;

$nama_kategori = isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '';
$warna_hex = isset($_POST['warna_hex']) ? trim($_POST['warna_hex']) : '#6366F1';

if (empty($nama_kategori)) {
    header("Location: ../../views/admin/kelola_kategori.php?error=empty_field");
    exit();
}

$warna_hex = strtoupper($warna_hex);
if (!preg_match('/^#[0-9A-F]{6}$/', $warna_hex)) {
    header("Location: ../../views/admin/kelola_kategori.php?error=invalid_color");
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO tbl_category (nama_kategori, warna_hex) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_kategori, $warna_hex);
    $stmt->execute();
    $stmt->close();

    logActivity(
        $conn,
        $_SESSION['admin_id'],
        $_SESSION['admin_username'],
        "Menambah kategori: $nama_kategori"
    );

    header("Location: ../../views/admin/kelola_kategori.php?success=created");
    exit();
} catch (Exception $e) {
    error_log('Gagal menambah kategori: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_kategori.php?error=database_error");
    exit();
}

