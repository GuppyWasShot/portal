<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_file = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_file <= 0) {
    header("Location: ../../views/admin/kelola_karya.php?error=invalid_id");
    exit();
}

$karyaModel = new Karya();

// Get file data before deletion (for physical file removal)
$file = $karyaModel->getFileById($id_file);

if (!$file) {
    header("Location: ../../views/admin/kelola_karya.php?error=not_found");
    exit();
}

try {
    $result = $karyaModel->deleteFile($id_file);
    
    if ($result) {
        // Delete physical file if exists
        if (!empty($file['file_path'])) {
            $file_path_full = __DIR__ . '/../../' . $file['file_path'];
            if (file_exists($file_path_full) && is_file($file_path_full)) {
                unlink($file_path_full);
            }
        }
        
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus file dari project ID {$file['id_project']}: {$file['nama_file']}"
            );
        }
        
        // ALWAYS redirect back to edit form using the file's project ID
        header("Location: ../../views/admin/form_edit_karya.php?id=" . $file['id_project'] . "&success=file_deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_karya.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus file: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_karya.php?error=database_error");
    exit();
}