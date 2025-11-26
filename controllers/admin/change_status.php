<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_karya.php?error=invalid_request");
    exit();
}

$karyaModel = new Karya();

$id_project = isset($_POST['id_project']) ? intval($_POST['id_project']) : 0;
$new_status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id_project <= 0 || empty($new_status)) {
    header("Location: ../../views/admin/kelola_karya.php?error=invalid_data");
    exit();
}

try {
    $result = $karyaModel->updateStatus($id_project, $new_status);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Mengubah status project ID $id_project menjadi: $new_status"
            );
        }
        
        header("Location: ../../views/admin/kelola_karya.php?success=status_updated");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_karya.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal mengubah status: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_karya.php?error=database_error");
    exit();
}