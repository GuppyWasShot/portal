<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?error=belum_login");
    exit();
}

include '../config/db_connect.php';

$id_project = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_project <= 0) {
    header("Location: kelola_karya.php?error=invalid_id");
    exit();
}

// Ambil data karya
$stmt = $conn->prepare("SELECT judul FROM tbl_project WHERE id_project = ?");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
$karya = $result->fetch_assoc();
$stmt->close();

if (!$karya) {
    header("Location: kelola_karya.php?error=not_found");
    exit();
}

// Ambil file yang akan dihapus
$stmt = $conn->prepare("SELECT file_path FROM tbl_project_files WHERE id_project = ?");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
$files_to_delete = [];
while ($row = $result->fetch_assoc()) {
    $files_to_delete[] = '../' . $row['file_path'];
}
$stmt->close();

mysqli_begin_transaction($conn);

try {
    // Hapus file fisik
    foreach ($files_to_delete as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Hapus dari database (CASCADE akan menghapus relasi)
    $stmt = $conn->prepare("DELETE FROM tbl_project WHERE id_project = ?");
    $stmt->bind_param("i", $id_project);
    $stmt->execute();
    $stmt->close();
    
    // Log aktivitas
    logActivity(
        $conn, 
        $_SESSION['admin_id'], 
        $_SESSION['admin_username'], 
        "Menghapus karya: " . $karya['judul']
    );
    
    mysqli_commit($conn);
    
    header("Location: kelola_karya.php?tab=daftar&success=hapus");
    exit();
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: kelola_karya.php?tab=daftar&error=delete_failed");
    exit();
}
?>