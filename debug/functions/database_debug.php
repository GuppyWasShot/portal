<?php
/**
 * Debug Fungsi: Koneksi Database
 * Akses: /debug/functions/database_debug.php
 * Memastikan konfigurasi database sudah benar dan menampilkan informasi dasar.
 */

require_once __DIR__ . '/../../app/autoload.php';

echo '<h1>Debug Koneksi Database</h1>';

try {
    $db = Database::getInstance()->getConnection();
    echo '✅ Koneksi ke database berhasil.<br>';

    $status = $db->query('SELECT NOW() as current_time');
    if ($status && $row = $status->fetch_assoc()) {
        echo 'Waktu server database: ' . htmlspecialchars($row['current_time']) . '<br>';
    }

    $tables = $db->query("SHOW TABLES");
    echo '<h3>Daftar Tabel:</h3>';
    echo '<ul>';
    if ($tables) {
        while ($table = $tables->fetch_array()) {
            echo '<li>' . htmlspecialchars($table[0]) . '</li>';
        }
    }
    echo '</ul>';
} catch (Exception $e) {
    echo '❌ Gagal terhubung ke database: ' . htmlspecialchars($e->getMessage());
}
