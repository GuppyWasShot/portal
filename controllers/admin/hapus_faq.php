<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

$id_faq = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_faq <= 0) {
    header("Location: ../../views/admin/kelola_faq.php?error=invalid_id");
    exit();
}

$faqModel = new Faq();

try {
    $result = $faqModel->delete($id_faq);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menghapus FAQ ID: $id_faq"
            );
        }
        
        header("Location: ../../views/admin/kelola_faq.php?success=deleted");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_faq.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menghapus FAQ: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_faq.php?error=database_error");
    exit();
}
