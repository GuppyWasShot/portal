<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_tentang.php?error=invalid_request");
    exit();
}

$aboutModel = new About();

$id_section = isset($_POST['id_section']) ? intval($_POST['id_section']) : 0;

$data = [
    'judul' => isset($_POST['judul']) ? trim($_POST['judul']) : '',
    'konten' => isset($_POST['konten']) ? trim($_POST['konten']) : '',
    'slug' => isset($_POST['slug']) ? trim($_POST['slug']) : '',
    'urutan' => isset($_POST['urutan']) ? intval($_POST['urutan']) : 0,
    'status' => isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active'
];

if ($id_section <= 0 || empty($data['judul']) || empty($data['konten'])) {
    header("Location: ../../views/admin/kelola_tentang.php?error=empty_field");
    exit();
}

try {
    $result = $aboutModel->update($id_section, $data);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Mengubah section tentang ID: $id_section"
            );
        }
        
        header("Location: ../../views/admin/kelola_tentang.php?success=updated");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_tentang.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal mengedit about section: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_tentang.php?error=database_error");
    exit();
}
