<?php
session_start();

// Use Auth model for authentication
require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

// Get ID from GET parameter
$id_dosen = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_dosen <= 0) {
    header("Location: ../../views/admin/kelola_dosen.php?error=invalid_id");
    exit();
}

// Initialize Dosen model
$dosenModel = new Dosen();

// Get existing dosen data to get foto_url for deletion
$dosen = $dosenModel->getById($id_dosen);

if (!$dosen) {
    header("Location: ../../views/admin/kelola_dosen.php?error=not_found");
    exit();
}

// Delete dosen using model
try {
    $result = $dosenModel->delete($id_dosen);
    
    if ($result) {
        // Delete photo file if exists
        if (!empty($dosen['foto_url'])) {
            $photo_path = __DIR__ . '/../../' . $dosen['foto_url'];
            if (file_exists($photo_path) && is_file($photo_path)) {
                unlink($photo_path);
            }
        }
        
        // Log activity using Auth model
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus data dosen ID: $id_dosen"
            );
        }
        
        header("Location: ../../views/admin/kelola_dosen.php?success=deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_dosen.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus dosen: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_dosen.php?error=database_error");
    exit();
}
