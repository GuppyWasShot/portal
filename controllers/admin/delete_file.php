<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../views/admin/login.php?error=belum_login");
    exit();
}

require_once __DIR__ . '/../../app/autoload.php';
require_once __DIR__ . '/../../config/db_connect.php';
$db = Database::getInstance()->getConnection();
$conn = $db;

$id_file = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_project = isset($_GET['project']) ? intval($_GET['project']) : 0;

if ($id_file <= 0 || $id_project <= 0) {
    header("Location: ../../views/admin/kelola_karya.php?error=invalid_id");
    exit();
}

// Ambil data file
$stmt = $conn->prepare("SELECT * FROM tbl_project_files WHERE id_file = ?");
$stmt->bind_param("i", $id_file);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();
$stmt->close();

if (!$file) {
    header("Location: ../../views/admin/form_edit_karya.php?id=$id_project&error=file_not_found");
    exit();
}

// Hapus file fisik
$file_path = '../' . $file['file_path'];
if (file_exists($file_path)) {
    unlink($file_path);
}

// Hapus dari database
$stmt = $conn->prepare("DELETE FROM tbl_project_files WHERE id_file = ?");
$stmt->bind_param("i", $id_file);
$stmt->execute();
$stmt->close();

// Log aktivitas
logActivity(
    $conn, 
    $_SESSION['admin_id'], 
    $_SESSION['admin_username'], 
    "Menghapus file: " . $file['nama_file'] . " dari project ID: $id_project"
);

header("Location: ../../views/admin/form_edit_karya.php?id=$id_project&success=file_deleted");
exit();
?>