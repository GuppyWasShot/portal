<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_section = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_section <= 0) {
    header("Location: ../../views/admin/kelola_tentang.php?error=invalid_id");
    exit();
}

$aboutModel = new About();

try {
    $result = $aboutModel->delete($id_section);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus section tentang ID: $id_section"
            );
        }
        
        header("Location: ../../views/admin/kelola_tentang.php?success=deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_tentang.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus about section: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_tentang.php?error=database_error");
    exit();
}
