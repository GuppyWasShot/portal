<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?error=belum_login");
    exit();
}

include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kelola_karya.php?error=invalid_request");
    exit();
}

// Ambil data dari form
$id_project = intval($_POST['id_project']);
$judul = trim($_POST['judul']);
$pembuat = trim($_POST['pembuat']);
$deskripsi = trim($_POST['deskripsi']);
$tanggal_selesai = $_POST['tanggal_selesai'];
$kategori_array = isset($_POST['kategori']) ? $_POST['kategori'] : [];
$action = $_POST['action'];

// Tentukan status
$status = ($action === 'publish') ? 'Published' : 'Draft';

// Validasi
if (empty($judul) || empty($pembuat) || empty($deskripsi) || empty($tanggal_selesai)) {
    header("Location: form_edit_karya.php?id=$id_project&error=empty_field");
    exit();
}

if (empty($kategori_array)) {
    header("Location: form_edit_karya.php?id=$id_project&error=no_category");
    exit();
}

// Siapkan data link tambahan
$link_labels = isset($_POST['link_label']) ? $_POST['link_label'] : [];
$link_urls = isset($_POST['link_url']) ? $_POST['link_url'] : [];

mysqli_begin_transaction($conn);

try {
    // 1. Update tbl_project
    $stmt = $conn->prepare(
        "UPDATE tbl_project 
         SET judul = ?, deskripsi = ?, pembuat = ?, tanggal_selesai = ?, status = ? 
         WHERE id_project = ?"
    );
    $stmt->bind_param("sssssi", $judul, $deskripsi, $pembuat, $tanggal_selesai, $status, $id_project);
    $stmt->execute();
    $stmt->close();
    
    // 2. Update kategori (hapus lalu insert ulang)
    $stmt = $conn->prepare("DELETE FROM tbl_project_category WHERE id_project = ?");
    $stmt->bind_param("i", $id_project);
    $stmt->execute();
    $stmt->close();
    
    $stmt_kategori = $conn->prepare(
        "INSERT INTO tbl_project_category (id_project, id_kategori) VALUES (?, ?)"
    );
    foreach ($kategori_array as $id_kategori) {
        $stmt_kategori->bind_param("ii", $id_project, $id_kategori);
        $stmt_kategori->execute();
    }
    $stmt_kategori->close();
    
    // 3. Insert link tambahan baru
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
    
    // 4. Handle upload snapshot baru
    if (isset($_FILES['snapshots']) && !empty($_FILES['snapshots']['name'][0])) {
        $upload_dir = '../uploads/snapshots/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $max_size = 2 * 1024 * 1024;
        
        foreach ($_FILES['snapshots']['name'] as $idx => $original_name) {
            if ($_FILES['snapshots']['error'][$idx] === UPLOAD_ERR_OK) {
                $file_type = $_FILES['snapshots']['type'][$idx];
                $file_size = $_FILES['snapshots']['size'][$idx];
                $file_tmp = $_FILES['snapshots']['tmp_name'][$idx];
                
                if (!in_array($file_type, $allowed_types) || $file_size > $max_size) {
                    continue;
                }
                
                $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $file_name = 'snapshot_' . $id_project . '_' . time() . '_' . $idx . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $db_path = 'uploads/snapshots/' . $file_name;
                    
                    $stmt_snapshot = $conn->prepare(
                        "INSERT INTO tbl_project_files (id_project, label, nama_file, file_path, file_size, mime_type) 
                         VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    $label_snapshot = "Snapshot " . ($idx + 1);
                    $stmt_snapshot->bind_param("isssis", $id_project, $label_snapshot, $original_name, $db_path, $file_size, $file_type);
                    $stmt_snapshot->execute();
                    $stmt_snapshot->close();
                }
            }
        }
    }
    
    // 5. Handle file pendukung baru
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
                
                if ($file_size > 5 * 1024 * 1024) {
                    continue;
                }
                
                $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $file_name = 'file_' . $id_project . '_' . time() . '_' . $idx . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;
                
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
    
    // 6. Log aktivitas
    logActivity(
        $conn, 
        $_SESSION['admin_id'], 
        $_SESSION['admin_username'], 
        "Mengupdate karya: $judul (Status: $status)", 
        $id_project
    );
    
    mysqli_commit($conn);
    
    header("Location: kelola_karya.php?success=edit");
    exit();
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: form_edit_karya.php?id=$id_project&error=database_error&msg=" . urlencode($e->getMessage()));
    exit();
}

$conn->close();
?>