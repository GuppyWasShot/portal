<?php
/**
 * Debug Fungsi: Rating Karya
 * Akses: /debug/functions/rating_debug.php?id=ID_PROJECT&score=5
 * Membantu mengecek proses submit/update/cancel rating secara manual melalui skrip sederhana.
 */

require_once __DIR__ . '/../../app/autoload.php';
require_once __DIR__ . '/../../config/db_connect.php';

$projectId = isset($_GET['id']) ? (int) $_GET['id'] : 1;
$score     = isset($_GET['score']) ? (int) $_GET['score'] : 5;
$userUuid  = $_GET['uuid'] ?? 'debug-user';
$ip        = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

$rating = new Rating();

echo '<h1>Debug Rating Karya</h1>';
echo '<p>Project ID: ' . htmlspecialchars($projectId) . '</p>';
echo '<p>Score yang akan dikirim: ' . htmlspecialchars($score) . '</p>';
echo '<p>UUID: ' . htmlspecialchars($userUuid) . ' | IP: ' . htmlspecialchars($ip) . '</p>';

echo '<h2>1. Cek Rating Saat Ini</h2>';
$current = $rating->getUserRating($projectId, $userUuid, $ip);
if ($current) {
    echo '✅ Sudah ada rating: ' . $current['skor'] . '<br>';
} else {
    echo 'ℹ️ Belum ada rating untuk kombinasi pengguna ini.<br>';
}

echo '<h2>2. Proses Submit/Update Rating</h2>';
$result = $rating->submitRating($projectId, $score, $userUuid, $ip);
echo $result ? '✅ Submit/Update berhasil<br>' : '❌ Submit gagal<br>';

echo '<h2>3. Nilai Setelah Update</h2>';
$updated = $rating->getUserRating($projectId, $userUuid, $ip);
if ($updated) {
    echo 'Skor terbaru di DB: ' . $updated['skor'] . '<br>';
}

echo '<h2>4. Opsi Hapus Rating</h2>';
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    $rating->deleteRating($projectId, $userUuid, $ip);
    echo '✅ Rating dihapus untuk user ini.<br>';
} else {
    echo 'Tambahkan parameter <code>&reset=1</code> untuk menghapus rating.<br>';
}

