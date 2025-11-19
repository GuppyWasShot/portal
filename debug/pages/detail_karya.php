<?php
/**
 * Debug Page: Detail Karya
 * Akses: /debug/pages/detail_karya.php?id=ID_PROJECT
 * Jika parameter id tidak diberikan, skrip akan mencoba memuat karya dengan ID 1.
 */

require_once __DIR__ . '/../../app/autoload.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_GET['id'] = 1;
}

include __DIR__ . '/../../views/public/detail_karya.php';
