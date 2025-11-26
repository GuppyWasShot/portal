<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_project = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_project <= 0) {
    header("Location: ../../views/admin/kelola_karya.php?error=invalid_id");
    exit();
}

$karyaModel = new Karya();

// Get project data before deletion (for file cleanup and logging)
$project = $karyaModel->getKaryaById($id_project);

if (!$project) {
    header("Location: ../../views/admin/kelola_karya.php?error=not_found");
    exit();
}

try {
    // Get all files for physical deletion
    $files = $karyaModel->getFiles($id_project);
    
    // Delete project using model (CASCADE will handle related data)
    $result = $karyaModel->hapusKarya($id_project);
    
    if ($result) {
        // Delete snapshot if exists
        if (!empty($project['snapshot_url'])) {
            $snapshot_path = __DIR__ . '/../../' . $project['snapshot_url'];
            if (file_exists($snapshot_path) && is_file($snapshot_path)) {
                unlink($snapshot_path);
            }
        }
        
        // Delete all project files
        foreach ($files as $file) {
            if (!empty($file['file_path'])) {
                $file_path_full = __DIR__ . '/../../' . $file['file_path'];
                if (file_exists($file_path_full) && is_file($file_path_full)) {
                    unlink($file_path_full);
                }
            }
        }
        
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus project: {$project['judul']} (ID: $id_project)",
                $id_project
            );
        }
        
        header("Location: ../../views/admin/kelola_karya.php?success=deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_karya.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus karya: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_karya.php?error=database_error");
    exit();
}