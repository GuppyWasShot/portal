<?php
session_start();
include '../config/db_connect.php';

// Validasi request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: galeri.php");
    exit();
}

// Generate atau ambil UUID user
if (!isset($_SESSION['user_uuid'])) {
    $_SESSION['user_uuid'] = uniqid('user_', true);
}

$id_project = isset($_POST['id_project']) ? intval($_POST['id_project']) : 0;
$skor = isset($_POST['skor']) ? intval($_POST['skor']) : 0;
$user_uuid = $_SESSION['user_uuid'];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Validasi input
if ($id_project <= 0 || $skor < 1 || $skor > 5) {
    header("Location: detail_karya.php?id=$id_project&error=invalid_rating");
    exit();
}

// Cek apakah project exists dan published
$stmt = $conn->prepare("SELECT id_project FROM tbl_project WHERE id_project = ? AND status = 'Published'");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: galeri.php?error=project_not_found");
    exit();
}
$stmt->close();

// Cek apakah user sudah pernah rating (berdasarkan UUID atau IP)
$stmt = $conn->prepare("SELECT id_rating FROM tbl_rating WHERE id_project = ? AND (uuid_user = ? OR ip_address = ?)");
$stmt->bind_param("iss", $id_project, $user_uuid, $ip_address);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User sudah pernah rating, tidak boleh rating lagi
    $stmt->close();
    header("Location: detail_karya.php?id=$id_project&error=already_rated");
    exit();
}
$stmt->close();

// Insert rating baru
$stmt = $conn->prepare("INSERT INTO tbl_rating (id_project, uuid_user, ip_address, skor) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $id_project, $user_uuid, $ip_address, $skor);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: detail_karya.php?id=$id_project&success=rating_submitted");
    exit();
} else {
    $stmt->close();
    header("Location: detail_karya.php?id=$id_project&error=rating_failed");
    exit();
}
?>