<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../views/admin/login.php?error=belum_login");
    exit();
}

$id_section = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_section <= 0) {
    header("Location: ../../views/admin/kelola_tentang.php?error=invalid_id");
    exit();
}

require_once __DIR__ . '/../../app/autoload.php';
require_once __DIR__ . '/../../config/db_connect.php';

$db = Database::getInstance()->getConnection();
$conn = $db;

try {
    $stmt = $conn->prepare("DELETE FROM tbl_about_sections WHERE id_section = ?");
    $stmt->bind_param("i", $id_section);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        header("Location: ../../views/admin/kelola_tentang.php?error=not_found");
        exit();
    }

    $stmt->close();

    logActivity(
        $conn,
        $_SESSION['admin_id'],
        $_SESSION['admin_username'],
        "Menghapus section Tentang ID: $id_section"
    );

    header("Location: ../../views/admin/kelola_tentang.php?success=deleted");
    exit();
} catch (Exception $e) {
    error_log('Gagal menghapus section Tentang: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_tentang.php?error=database_error");
    exit();
}

