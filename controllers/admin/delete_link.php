<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_link = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_link <= 0) {
    header("Location: ../../views/admin/kelola_karya.php?error=invalid_id");
    exit();
}

$karyaModel = new Karya();

// Get link data before deletion (for logging)
$link = $karyaModel->getLinkById($id_link);

if (!$link) {
    header("Location: ../../views/admin/kelola_karya.php?error=not_found");
    exit();
}

try {
    $result = $karyaModel->deleteLink($id_link);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus link dari project ID {$link['id_project']}: {$link['label']}"
            );
        }
        
        // ALWAYS redirect back to edit form using the link's project ID
        header("Location: ../../views/admin/form_edit_karya.php?id=" . $link['id_project'] . "&success=link_deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_karya.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus link: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_karya.php?error=database_error");
    exit();
}