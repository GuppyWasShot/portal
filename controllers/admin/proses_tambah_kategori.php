<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_kategori.php?error=invalid_request");
    exit();
}

$categoryModel = new Category();

$data = [
    'nama_kategori' => isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '',
    'warna_hex' => isset($_POST['warna_hex']) ? trim($_POST['warna_hex']) : '#6366F1'
];

if (empty($data['nama_kategori'])) {
    header("Location: ../../views/admin/kelola_kategori.php?error=empty_field");
    exit();
}

try {
    $result = $categoryModel->create($data);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Menambah kategori: {$data['nama_kategori']}"
            );
        }
        
        header("Location: ../../views/admin/kelola_kategori.php?success=created");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_kategori.php?error=database_error");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal menambah kategori: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_kategori.php?error=database_error");
    exit();
}
