<?php
/**
 * Tentang TPL - Refactored dengan OOP
 * Mengintegrasikan design dari front/tentang.html
 */

// Autoload classes
require_once __DIR__ . '/../../app/autoload.php';

$page_title = "Tentang TPL";
$body_class = 'page-tentang';
$additional_stylesheets = ['assets/css/page-tentang.css'];
include __DIR__ . '/../layouts/header_public.php';
?>

    <!-- ===== Bagian: Hero Tentang ===== -->
    <section class="hero">
        <div class="hero-content">
            <h1>Tentang <span class="highlight">TPL</span></h1>
            <p>Mari mengenal program studi Teknologi Rekayasa Perangkat Lunak Sekolah Vokasi IPB University.</p>
            <div class="hero-buttons">
                <a href="matkul.php" class="btn-outline">Mata Kuliah</a>
                <a href="dosen.php" class="btn-filled">Daftar Dosen</a>
            </div>
        </div>
    </section>

    <!-- ===== Bagian: Konten Tentang ===== -->
    <section class="content">
        <h2>Profil Umum <span class="highlight">D4 Teknologi Rekayasa Perangkat Lunak</span></h2>
        
        <div class="profile-intro">
            <ol>
                <li>Menyelenggarakan pendidikan vokasi yang berkualitas untuk menyiapkan tenaga yang terampil dan terdidik di bidang Teknologi Rekayasa Perangkat Lunak yang berkontribusi terhadap bidang pertanian dalam arti luas sesuai dengan kebutuhan dunia Kerja.</li>
                <li>Menyelenggarakan penelitian terapan di bidang informatika mengacu pada kebutuhan, ilmu dan teknologi yang terus berkembang serta berkontribusi dalam bidang pertanian, kelautan, dan biosains tropika.</li>
                <li>Menyelenggarakan pengabdian kepada masyarakat dalam menyebarluaskan hasil pendidikan dan hasil penelitian terapan di bidang informatika.</li>
                <li>Menjalin kerjasama dengan lembaga pemerintahan dan/atau instansi terkait dengan pencapaian kompetensi mahasiswa, penelitian terapan, pengabdian kepada masyarakat, dan lapangan pekerjaan bagi lulusan.</li>
            </ol>
        </div>

        <h3>Visi</h3>
        <p>Menjadi program studi yang terdepan dan unggul di Indonesia dalam menyiapkan tenaga profesional sebagai Sarjana Terapan bidang Teknologi Rekayasa Perangkat Lunak yang ikut mendukung penerapan teknologi di bidang pertanian, kelautan, dan biosains tropika tahun 2030.</p>

        <h3>Misi</h3>
        <ol>
            <li>Menyelenggarakan pendidikan vokasi yang berkualitas untuk menyiapkan tenaga yang terampil dan terdidik di bidang Teknologi Rekayasa Perangkat Lunak yang berkontribusi terhadap bidang pertanian dalam arti luas sesuai dengan kebutuhan dunia kerja.</li>
            <li>Menyelenggarakan penelitian terapan di bidang informatika mengacu pada kebutuhan, ilmu dan teknologi yang terus berkembang serta berkontribusi dalam bidang pertanian, kelautan, dan biosains tropika.</li>
            <li>Menyelenggarakan pengabdian kepada masyarakat dalam menyebarluaskan hasil pendidikan dan hasil penelitian terapan di bidang informatika.</li>
            <li>Menjalin kerjasama dengan lembaga pemerintahan dan/atau instansi terkait dengan pencapaian kompetensi mahasiswa, penelitian terapan, pengabdian kepada masyarakat, dan lapangan pekerjaan bagi lulusan.</li>
        </ol>

        <h3>Capaian Pembelajaran</h3>
        <ol>
            <li>Mampu menunjukkan perilaku sesuai nilai agama, hukum, kemanusiaan, dan etika sesuai bidang keprofesian teknologi informatika</li>
            <li>Mampu melakukan diri dalam pekerjaan yang bertanggung(awab), disiplin, serta berkepemimpinan</li>
            <li>Mampu menganalisis kebutuhan guna mendukung penyelesaian permasalahan di bidang teknologi informatika</li>
            <li>Mampu memanfaatkan data yang digunakan dalam sistem informasi</li>
            <li>Mampu merancang arsitektur sistem sesuai dengan kaidah kewirausahaan</li>
            <li>Mampu menggunakan teknik dan perangkat yang sesuai guna mendukung penyelesaian permasalahan teknologi informatika</li>
            <li>Mampu mempresentasikan hasil pekerjaan dan/atau luaran sesuai bidang kajian</li>
            <li>Mampu mengelola proses pengembangan produk digital yang mendukung implementasi agrosystem</li>
            <li>Mampu menciptakan desain dan produk berbasis teknologi secara interdisiplin</li>
            <li>Mampu membangun portofolio visualisasi data</li>
        </ol>

        <h3>Mandat</h3>
        <p>Menyelenggarakan pendidikan dan penelitian terapan serta pengabdian masyarakat dalam bidang rekayasa perangkat lunak yang inovatif secara terpadu dengan melakukan kolaborasi bidang ilmu sehingga dapat dimanfaatkan oleh masyarakat.</p>

        <div class="info-box">
            <h4>Lama Studi</h4>
            <p>8 semester (4 tahun)</p>
        </div>

        <div class="info-box">
            <h4>Gelar Kelulusan</h4>
            <p>Sarjana Terapan Komputer (S.Tr.Kom.)</p>
        </div>
    </section>

    <script>
        // ===== Bagian: Animasi Scroll =====
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero');
            if (parallax) {
                parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>

<?php include __DIR__ . '/../layouts/footer_public.php'; ?>

