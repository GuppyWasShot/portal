<?php
session_start();

// Use Auth model for authentication
require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_dosen.php?error=invalid_request");
    exit();
}

require_once __DIR__ . '/../../helpers/text_helper.php';

// Initialize Dosen model
$dosenModel = new Dosen();

// Get ID and prepare data from POST
$id_dosen = isset($_POST['id_dosen']) ? intval($_POST['id_dosen']) : 0;

$data = [
    'nama' => isset($_POST['nama']) ? trim($_POST['nama']) : '',
    'gelar' => isset($_POST['gelar']) ? trim($_POST['gelar']) : '',
    'jabatan' => isset($_POST['jabatan']) ? trim($_POST['jabatan']) : '',
    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
    'deskripsi' => isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '',
    'urutan' => isset($_POST['urutan']) ? intval($_POST['urutan']) : 0,
    'status' => isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active'
];

// Validate
if ($id_dosen <= 0 || empty($data['nama'])) {
    header("Location: ../../views/admin/kelola_dosen.php?error=empty_field");
    exit();
}

// Get existing dosen data
$existing = $dosenModel->getById($id_dosen);
if (!$existing) {
    header("Location: ../../views/admin/kelola_dosen.php?error=not_found");
    exit();
}

// Handle file upload (controller responsibility)
if (isset($_FILES['foto']) && !empty($_FILES['foto']['name'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 2 * 1024 * 1024;
    $file_type = $_FILES['foto']['type'];
    $file_size = $_FILES['foto']['size'];
    $file_tmp = $_FILES['foto']['tmp_name'];
    $original_name = $_FILES['foto']['name'];

    if (!in_array($file_type, $allowed_types) || $file_size > $max_size) {
        header("Location: ../../views/admin/kelola_dosen.php?error=invalid_file");
        exit();
    }

    // Use ABSOLUTE PATH with __DIR__ - Changed to uploads/dosen/
    $upload_dir = __DIR__ . '/../../uploads/dosen/';
    
    // Auto-create directory if not exists
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            error_log("Failed to create dosen upload directory: " . $upload_dir);
            header("Location: ../../views/admin/kelola_dosen.php?error=upload_dir_create_failed");
            exit();
        }
    }

    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        error_log("Dosen upload directory not writable: " . $upload_dir);
        header("Location: ../../views/admin/kelola_dosen.php?error=upload_dir_not_writable");
        exit();
    }

    $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $slug = slugify_text($data['nama'], 60);
    $file_name = 'dosen_' . $slug . '_' . time() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($file_tmp, $file_path)) {
        // Delete old photo if exists
        if (!empty($existing['foto_url'])) {
            $old_path = __DIR__ . '/../../' . $existing['foto_url'];
            if (file_exists($old_path) && is_file($old_path)) {
                unlink($old_path);
            }
        }
        // Store relative path for database - Changed to uploads/dosen/
        $data['foto_url'] = 'uploads/dosen/' . $file_name;
    } else {
        error_log("Failed to upload dosen photo: " . $file_name);
        header("Location: ../../views/admin/kelola_dosen.php?error=upload_failed");
        exit();
    }
}

// Update dosen using model
try {
    $result = $dosenModel->update($id_dosen, $data);
    
    if ($result) {
        // Log activity using Auth model
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Mengubah data dosen: {$data['nama']}"
            );
        }
        
        header("Location: ../../views/admin/kelola_dosen.php?success=updated");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_dosen.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal mengedit dosen: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_dosen.php?error=database_error");
    exit();
}
