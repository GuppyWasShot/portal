<?php
/**
 * Debug Page: Galeri Publik
 * Akses: /debug/pages/galeri.php
 * Mendemokan halaman galeri dengan opsi query string (search, sort, kategori, page).
 */

require_once __DIR__ . '/../../app/autoload.php';

// Galeri menggunakan parameter GET, sehingga kita cukup meneruskan parameter yang diberikan.
// Contoh: /debug/pages/galeri.php?search=ui&sort=terbaru

include __DIR__ . '/../../views/public/galeri.php';
