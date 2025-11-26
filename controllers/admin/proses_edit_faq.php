<?php
session_start();

require_once __DIR__ . '/../../app/autoload.php';
$auth = new Auth();
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/kelola_faq.php?error=invalid_request");
    exit();
}

$faqModel = new Faq();

$id_faq = isset($_POST['id_faq']) ? intval($_POST['id_faq']) : 0;

$data = [
    'pertanyaan' => isset($_POST['pertanyaan']) ? trim($_POST['pertanyaan']) : '',
    'jawaban' => isset($_POST['jawaban']) ? trim($_POST['jawaban']) : '',
    'kategori' => isset($_POST['kategori']) ? trim($_POST['kategori']) : '',
    'urutan' => isset($_POST['urutan']) ? intval($_POST['urutan']) : 0,
    'status' => isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active'
];

if ($id_faq <= 0 || empty($data['pertanyaan']) || empty($data['jawaban'])) {
    header("Location: ../../views/admin/kelola_faq.php?error=empty_field");
    exit();
}

try {
    $result = $faqModel->update($id_faq, $data);
    
    if ($result) {
        $currentAdmin = $auth->getCurrentAdmin();
        if ($currentAdmin) {
            $auth->logActivity(
                $currentAdmin['id_admin'],
                $currentAdmin['username'],
                "Mengubah FAQ ID: $id_faq"
            );
        }
        
        header("Location: ../../views/admin/kelola_faq.php?success=updated");
        exit();
    } else {
        header("Location: ../../views/admin/kelola_faq.php?error=not_found");
        exit();
    }
} catch (Exception $e) {
    error_log('Gagal mengedit FAQ: ' . $e->getMessage());
    header("Location: ../../views/admin/kelola_faq.php?error=database_error");
    exit();
}
