<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_matkul = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_matkul <= 0) {
    header("Location: ../../views/admin/kelola_matkul.php?error=invalid_id");
    exit();
}

$matkulModel = new Matkul();

try {
    $result = $matkulModel->delete($id_matkul);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus mata kuliah ID: $id_matkul"
            );
        }
        
        header("Location: ../../views/admin/kelola_matkul.php?success=deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_matkul.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus mata kuliah: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_matkul.php?error=database_error");
    exit();
}
