<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_kategori = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_kategori <= 0) {
    header("Location: ../../views/admin/kelola_kategori.php?error=invalid_id");
    exit();
}

$categoryModel = new Category();

try {
    $result = $categoryModel->delete($id_kategori);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus kategori ID: $id_kategori"
            );
        }
        
        header("Location: ../../views/admin/kelola_kategori.php?success=deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_kategori.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus kategori: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_kategori.php?error=database_error");
    exit();
}
