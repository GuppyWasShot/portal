<?php
/**
 * Debug Page: Form Login Admin
 * Akses: /debug/pages/admin_login.php
 * Memuat tampilan login admin tanpa perlu mengakses routing utama.
 */

session_start();
require_once __DIR__ . '/../../app/autoload.php';

include __DIR__ . '/../../views/admin/login.php';
