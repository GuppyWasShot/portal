<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_matkul.php?error=invalid_request");
    exit();
}

$matkulModel = new Matkul();

$id_matkul = isset($_POST['id_matkul']) ? intval($_POST['id_matkul']) : 0;

$data = [
    'kode' => isset($_POST['kode']) ? trim($_POST['kode']) : '',
    'nama' => isset($_POST['nama']) ? trim($_POST['nama']) : '',
    'sks' => isset($_POST['sks']) ? intval($_POST['sks']) : 0,
    'semester' => isset($_POST['semester']) ? intval($_POST['semester']) : 0,
    'kategori' => isset($_POST['kategori']) ? trim($_POST['kategori']) : '',
    'deskripsi' => isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '',
    'urutan' => isset($_POST['urutan']) ? intval($_POST['urutan']) : 0,
    'status' => isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active'
];

if ($id_matkul <= 0 || empty($data['kode']) || empty($data['nama']) || $data['semester'] <= 0) {
    header("Location: ../../views/admin/kelola_matkul.php?error=empty_field");
    exit();
}

try {
    $result = $matkulModel->update($id_matkul, $data);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Mengubah mata kuliah: {$data['kode']} - {$data['nama']}"
            );
        }
        
        header("Location: ../../views/admin/kelola_matkul.php?success=updated");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_matkul.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal mengedit mata kuliah: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_matkul.php?error=database_error");
    exit();
}
