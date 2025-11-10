<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?error=belum_login");
    exit();
}

include '../config/db_connect.php';

$id_link = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_project = isset($_GET['project']) ? intval($_GET['project']) : 0;

if ($id_link <= 0 || $id_project <= 0) {
    header("Location: kelola_karya.php?error=invalid_id");
    exit();
}

// Ambil data link
$stmt = $conn->prepare("SELECT * FROM tbl_project_links WHERE id_link = ?");
$stmt->bind_param("i", $id_link);
$stmt->execute();
$result = $stmt->get_result();
$link = $result->fetch_assoc();
$stmt->close();

if (!$link) {
    header("Location: form_edit_karya.php?id=$id_project&error=link_not_found");
    exit();
}

// Cegah hapus link utama
if ($link['is_primary']) {
    header("Location: form_edit_karya.php?id=$id_project&error=cannot_delete_primary");
    exit();
}

// Hapus dari database
$stmt = $conn->prepare("DELETE FROM tbl_project_links WHERE id_link = ?");
$stmt->bind_param("i", $id_link);
$stmt->execute();
$stmt->close();

// Log aktivitas
logActivity(
    $conn, 
    $_SESSION['admin_id'], 
    $_SESSION['admin_username'], 
    "Menghapus link: " . $link['label'] . " dari project ID: $id_project"
);

header("Location: form_edit_karya.php?id=$id_project&success=link_deleted");
exit();
?>