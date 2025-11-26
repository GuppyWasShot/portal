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

// Prepare data from POST
$data = [
    'nama' => isset($_POST['nama']) ? trim($_POST['nama']) : '',
    'gelar' => isset($_POST['gelar']) ? trim($_POST['gelar']) : '',
    'jabatan' => isset($_POST['jabatan']) ? trim($_POST['jabatan']) : '',
    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
    'deskripsi' => isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '',
    'urutan' => isset($_POST['urutan']) ? intval($_POST['urutan']) : 0,
    'status' => isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active'
];

// Validate required fields
if (empty($data['nama'])) {
    header("Location: ../../views/admin/kelola_dosen.php?error=empty_field");
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
        // Store relative path for database (for web display) - Changed to uploads/dosen/
        $data['foto_url'] = 'uploads/dosen/' . $file_name;
    } else {
        error_log("Failed to upload dosen photo: " . $file_name);
        header("Location: ../../views/admin/kelola_dosen.php?error=upload_failed");
        exit();
    }
}

// Create dosen using model
try {
    $result = $dosenModel->create($data);
    
    if ($result) {
        // Log activity using Auth model
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menambah data dosen: {$data['nama']}"
            );
        }
        
        header("Location: ../../views/admin/kelola_dosen.php?success=created");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_dosen.php?error=database_error");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menambah dosen: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_dosen.php?error=database_error");
    exit();
}
