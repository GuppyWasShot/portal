<?php
/**
 * Mata Kuliah TPL - Halaman Daftar Mata Kuliah
 */

$page_title = "Mata Kuliah";
$body_class = 'page-matkul';
$additional_stylesheets = ['assets/css/page-matkul.css'];
include __DIR__ . '/../layouts/header_public.php';
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Mata <span class="highlight">Kuliah</span></h1>
            <p>Daftar Mata Kuliah Prodi Teknologi Rekayasa Perangkat Lunak</p>
        </div>
    </section>

    <!-- container content -->
    <div class="container-content">
        <div class="semester-section">
            <h2>Semester 1</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 1" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>TPL1101</td><td>Berpikir Komputasional</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1102</td><td>Dasar Pemrograman</td><td>3(1-2)</td></tr>
                    <tr><td>TPL2101</td><td>Logika Informatika</td><td>3(2-1)</td></tr>
                    <tr><td>SVI1100</td><td>Bahasa Inggris</td><td>2(1-1)</td></tr>
                    <tr><td>SVI1101</td><td>Pendidikan Agama Islam</td><td>3(2-1)</td></tr>
                    <tr><td>SVI1109</td><td>Bahasa Indonesia</td><td>2(1-1)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>16(7-9)</td></tr></tfoot>
            </table>
            </div>
        </div>

        <div class="semester-section">
            <h2>Semester 2</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 2" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>TPL1107</td><td>Matematika Terapan</td><td>3(2-1)</td></tr>
                    <tr><td>TPL1109</td><td>Algoritma dan Struktur Data</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1105</td><td>Teknologi Multimedia</td><td>3(1-2)</td></tr>
                    <tr><td>TPL2102</td><td>Perancangan Web</td><td>3(2-1)</td></tr>
                    <tr><td>TPL1207</td><td>Probabilitas dan Statistika</td><td>2(1-1)</td></tr>
                    <tr><td>SVI1107</td><td>Pendidikan Pancasila</td><td>1(1-0)</td></tr>
                    <tr><td>SVI1108</td><td>Pendidikan Kewarganegaraan</td><td>2(1-1)</td></tr>
                    <tr><td>MNI1101</td><td>Pertanian Inovatif</td><td>2(2-0)</td></tr> 
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>19(10-9)</td></tr></tfoot>
            </table>
            </div>
        </div>

        <div class="semester-section">
            <h2>Semester 3</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 3" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>TPL1112</td><td>Rekayasa Kebutuhan Perangkat Lunak</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1202</td><td>Matematika Informatika</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1103</td><td>Komunikasi Data dan Jaringan</td><td>3(1-2)</td></tr>
                    <tr><td>TPL2201</td><td>Pengalaman Pengguna</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1206</td><td>Analisis dan Perancangan Perangkat Lunak</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1111</td><td>Sistem Basis Data</td><td>3(1-2)</td></tr>
                    <tr><td>TPL2202</td><td>Keamanan Perangkat Lunak</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1110</td><td>Pemrograman Berorientasi Objek</td><td>3(1-2)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>24(9-15)</td></tr></tfoot>
            </table>
            </div>
        </div>

        <div class="semester-section">
            <h2>Semester 4</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 4" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>TPL1209</td><td>Sistem informasi</td><td>2(1-1)</td></tr>
                    <tr><td>TPL1205</td><td>Komputasi Awan</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1210</td><td>Teknologi Virtual</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1212</td><td>Pengembangan Karakter dan Etika Profesi Bidang Teknologi Informasi</td><td>2(1-1)</td></tr>
                    <tr><td>TPL2306</td><td>Thecnopreneurship</td><td>2(1-1)</td></tr>
                    <tr><td>TPL1203</td><td>Pemrogaman Web</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1204</td><td>Pemrogaman Mobile</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1301</td><td>Analasis dan Visualisasi Data</td><td>3(1-2)</td></tr>
                    <tr><td>TPL1304</td><td>Teknik Penambangan Data</td><td>3(1-2)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>24(9-15)</td></tr></tfoot>
            </table>
            </div>
        </div>

        <div class="semester-section">
            <h2>Semester 5</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 5" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>TPL1303</td><td>Manajemen Proyek Teknologi </td><td>2(1-1)</td></tr>
                    <tr><td>TPL1302</td><td>Pemrosesan Citra Terapan </td><td>3(1-2)</td></tr>
                    <tr><td>TPL1309</td><td>Teknologi Big Data </td><td>2(1-1)</td></tr>
                    <tr><td>TPL1211</td><td>Sistem Informasi Geografis </td><td>2(1-1)</td></tr>
                    <tr><td>TPL2310</td><td>Visual Komputer Cerdas </td><td>2(1-1)</td></tr>
                    <tr><td>TPL1305</td><td>Pengujian dan Pemjaminan Kualitas Perangkat Lunak </td><td>2(1-1)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>17(6-11)</td></tr></tfoot>
            </table>
            </div>
        </div>
              
        <div class="semester-section">
            <h2>Semester 6</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 6" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>-</td><td>Enrichment Course</td><td>22(0-22)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>22(0-22)</td></tr></tfoot>
            </table>
            </div>
        </div>

        <div class="semester-section">
            <h2>Semester 7</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 7" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>SVI2401</td><td>Immersive Program</td><td>14(0-14)</td></tr>
                    <tr><td>SVI24012</td><td>Work Plan</td><td>1(0-1)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>15(0-15)</td></tr></tfoot>
            </table>
            </div>
        </div>
              
        <div class="semester-section">
            <h2>Semester 8</h2>
            <div class="table-wrapper" role="region" aria-label="Tabel Semester 8" tabindex="0">
            <table>
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th>SKS</th></tr></thead>
                <tbody>
                    <tr><td>SVI2403</td><td>Seminar</td><td>1(0-1)</td></tr>
                    <tr><td>SVI2404</td><td>Laporan Proyek Akhir</td><td>6(0-6)</td></tr>
                </tbody>
                <tfoot><tr><td colspan="2">Total</td><td>7(0-7)</td></tr></tfoot>
            </table>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layouts/footer_public.php'; ?>

