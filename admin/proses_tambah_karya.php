<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?error=belum_login");
    exit();
}

include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kelola_karya.php?tab=tambah&error=invalid_request");
    exit();
}

// Ambil data dari form
$judul = trim($_POST['judul']);
$pembuat = trim($_POST['pembuat']);
$deskripsi = trim($_POST['deskripsi']);
$tanggal_selesai = $_POST['tanggal_selesai'];
$link_utama = trim($_POST['link_utama']);
$kategori_array = isset($_POST['kategori']) ? $_POST['kategori'] : [];
$action = $_POST['action']; // draft atau publish

// Tentukan status
$status = ($action === 'publish') ? 'Published' : 'Draft';

// Validasi input wajib
if (empty($judul) || empty($pembuat) || empty($deskripsi) || empty($tanggal_selesai) || empty($link_utama)) {
    header("Location: form_tambah_karya.php?error=empty_field");
    exit();
}

// Siapkan data link tambahan
$link_labels = isset($_POST['link_label']) ? $_POST['link_label'] : [];
$link_urls = isset($_POST['link_url']) ? $_POST['link_url'] : [];

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // 1. Insert ke tbl_project - DIPERBAIKI SYNTAX ERROR
    $stmt = $conn->prepare(
        "INSERT INTO tbl_project (judul, deskripsi, pembuat, tanggal_selesai, status, link_external) 
         VALUES (?, ?, ?, ?, ?, NULL)"
    );
    $stmt->bind_param("sssss", $judul, $deskripsi, $pembuat, $tanggal_selesai, $status);
    $stmt->execute();
    $id_project = $stmt->insert_id;
    $stmt->close();
    
    // 2. Insert link utama ke tbl_project_links
    $stmt_link = $conn->prepare(
        "INSERT INTO tbl_project_links (id_project, label, url, is_primary) VALUES (?, ?, ?, 1)"
    );
    $label_utama = "Link Utama";
    $stmt_link->bind_param("iss", $id_project, $label_utama, $link_utama);
    $stmt_link->execute();
    $stmt_link->close();
    
    // 3. Insert link tambahan
    if (!empty($link_labels) && !empty($link_urls)) {
        $stmt_link = $conn->prepare(
            "INSERT INTO tbl_project_links (id_project, label, url, is_primary) VALUES (?, ?, ?, 0)"
        );
        
        foreach ($link_labels as $idx => $label) {
            if (!empty($label) && !empty($link_urls[$idx])) {
                $stmt_link->bind_param("iss", $id_project, $label, $link_urls[$idx]);
                $stmt_link->execute();
            }
        }
        $stmt_link->close();
    }
    
    // 4. Insert kategori
    $stmt_kategori = $conn->prepare(
        "INSERT INTO tbl_project_category (id_project, id_kategori) VALUES (?, ?)"
    );
    
    foreach ($kategori_array as $id_kategori) {
        $stmt_kategori->bind_param("ii", $id_project, $id_kategori);
        $stmt_kategori->execute();
    }
    $stmt_kategori->close();
    
    // 5. Handle multiple snapshot upload
    if (isset($_FILES['snapshots']) && !empty($_FILES['snapshots']['name'][0])) {
        $upload_dir = '../uploads/snapshots/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        foreach ($_FILES['snapshots']['name'] as $idx => $original_name) {
            if ($_FILES['snapshots']['error'][$idx] === UPLOAD_ERR_OK) {
                $file_type = $_FILES['snapshots']['type'][$idx];
                $file_size = $_FILES['snapshots']['size'][$idx];
                $file_tmp = $_FILES['snapshots']['tmp_name'][$idx];
                
                // Validasi tipe dan ukuran
                if (!in_array($file_type, $allowed_types)) {
                    continue; // Skip file yang tidak valid
                }
                
                if ($file_size > $max_size) {
                    continue; // Skip file yang terlalu besar
                }
                
                // Generate nama file unik
                $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $file_name = 'snapshot_' . $id_project . '_' . time() . '_' . $idx . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;
                
                // Upload file
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $db_path = 'uploads/snapshots/' . $file_name;
                    
                    // Simpan ke database sebagai file
                    $stmt_snapshot = $conn->prepare(
                        "INSERT INTO tbl_project_files (id_project, label, nama_file, file_path, file_size, mime_type) 
                         VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    $label_snapshot = "Snapshot " . ($idx + 1);
                    $stmt_snapshot->bind_param("isssis", $id_project, $label_snapshot, $original_name, $db_path, $file_size, $file_type);
                    $stmt_snapshot->execute();
                    $stmt_snapshot->close();
                    
                    // Update snapshot_url di tbl_project (hanya yang pertama)
                    if ($idx === 0) {
                        $stmt_update = $conn->prepare("UPDATE tbl_project SET snapshot_url = ? WHERE id_project = ?");
                        $stmt_update->bind_param("si", $db_path, $id_project);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                }
            }
        }
    }
    
    // 6. Handle file pendukung upload
    if (isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'][0])) {
        $file_labels = isset($_POST['file_label']) ? $_POST['file_label'] : [];
        
        $upload_dir = '../uploads/files/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $stmt_file = $conn->prepare(
            "INSERT INTO tbl_project_files (id_project, label, nama_file, file_path, file_size, mime_type) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        foreach ($_FILES['file_upload']['name'] as $idx => $original_name) {
            if ($_FILES['file_upload']['error'][$idx] === UPLOAD_ERR_OK) {
                $file_size = $_FILES['file_upload']['size'][$idx];
                $file_type = $_FILES['file_upload']['type'][$idx];
                $file_tmp = $_FILES['file_upload']['tmp_name'][$idx];
                
                // Validasi ukuran (max 5MB)
                if ($file_size > 5 * 1024 * 1024) {
                    continue; // Skip file yang terlalu besar
                }
                
                // Generate nama file unik
                $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $file_name = 'file_' . $id_project . '_' . time() . '_' . $idx . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;
                
                // Upload file
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $label = !empty($file_labels[$idx]) ? $file_labels[$idx] : $original_name;
                    $db_path = 'uploads/files/' . $file_name;
                    
                    $stmt_file->bind_param("isssis", $id_project, $label, $original_name, $db_path, $file_size, $file_type);
                    $stmt_file->execute();
                }
            }
        }
        $stmt_file->close();
    }
    
    // 7. Log aktivitas
    logActivity(
        $conn, 
        $_SESSION['admin_id'], 
        $_SESSION['admin_username'], 
        "Menambahkan karya: $judul (Status: $status)", 
        $id_project
    );
    
    // Commit transaction
    mysqli_commit($conn);
    
    header("Location: kelola_karya.php?success=tambah");
    exit();
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    
    // Hapus file yang sudah diupload jika ada error
    if (isset($upload_dir) && is_dir($upload_dir)) {
        $files = glob($upload_dir . '*_' . $id_project . '_*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
    }
    
    header("Location: form_tambah_karya.php?error=database_error&msg=" . urlencode($e->getMessage()));
    exit();
}

$conn->close();
?>