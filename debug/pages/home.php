<?php
/**
 * Debug Page: Beranda Publik
 * Akses: /debug/pages/home.php
 * Menjalankan tampilan beranda dengan data sample untuk memastikan komponen tampil sempurna.
 */

require_once __DIR__ . '/../../app/autoload.php';

// Tidak ada parameter khusus untuk halaman beranda.
// Skrip ini hanya akan memuat view utama untuk mempermudah proses debugging tampilan.

include __DIR__ . '/../../views/public/index.php';
