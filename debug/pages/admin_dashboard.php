<?php
/**
 * Debug Page: Dashboard Admin
 * Akses: /debug/pages/admin_dashboard.php
 * Mengaktifkan sesi admin secara otomatis untuk meninjau tampilan dashboard tanpa proses login.
 */

session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = $_SESSION['admin_username'] ?? 'debug-admin';

require_once __DIR__ . '/../../app/autoload.php';

include __DIR__ . '/../../views/admin/index.php';
