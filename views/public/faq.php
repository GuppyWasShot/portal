<?php

// FAQ - Frequently Asked Questions

// Autoload classes
require_once __DIR__ . '/../../app/autoload.php';

$page_title = "FAQ";
$body_class = 'page-faq';
$additional_stylesheets = ['assets/css/page-faq.css'];
include __DIR__ . '/../layouts/header_public.php';
?>

<!-- ===== Bagian: FAQ Hero ===== -->
<section class="faq-hero">
    <div class="faq-hero-content">
        <h1>Frequently Asked<br><span class="highlight">Questions</span></h1>
        <p>Temukan jawaban untuk pertanyaan yang sering diajukan tentang Portal TPL</p>
    </div>
</section>

<!-- ===== Bagian: Daftar FAQ ===== -->
<section class="faq-wrapper">
    <h2>Pertanyaan yang <span class="highlight">Sering Diajukan</span></h2>
    <div class="faq-container">
        <div class="faq-card">
            <div class="faq-question">
                Bagaimana cara mencari atau memfilter karya?
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Portal TPL menyediakan berbagai cara untuk mencari dan memfilter karya:</p>
                <ul>
                    <li><strong>Pencarian Teks:</strong> Gunakan search bar di halaman Galeri untuk mencari berdasarkan judul, nama pembuat, atau deskripsi karya</li>
                    <li><strong>Filter Kategori:</strong> Pilih kategori karya menggunakan bubble berwarna yang tersedia (Web, Mobile, IoT, dll)</li>
                    <li><strong>Urutkan Karya:</strong> Pilih opsi sorting untuk mengurutkan karya berdasarkan:
                        <ul>
                            <li>Terbaru (default)</li>
                            <li>Terlama</li>
                            <li>Judul A-Z atau Z-A</li>
                            <li>Rating Tertinggi</li>
                        </ul>
                    </li>
                    <li><strong>Kombinasi Filter:</strong> Anda dapat menggabungkan pencarian, filter kategori, dan sorting sekaligus</li>
                </ul>
                <p>Semua filter akan tetap aktif saat Anda berpindah halaman (pagination).</p>
                <div class="faq-tags">
                    <span class="tag">Pencarian</span>
                    <span class="tag">Filter</span>
                    <span class="tag">Galeri</span>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <div class="faq-question">
                Bagaimana cara memberi rating pada karya?
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Untuk memberikan atau mengubah rating pada karya:</p>
                <ol>
                    <li>Buka halaman detail karya yang ingin Anda beri rating</li>
                    <li>Scroll ke bagian "Penilaian Rata-Rata"</li>
                    <li>Klik bintang sesuai penilaian Anda (1-5 bintang)</li>
                    <li>Klik tombol "Kirim Penilaian" atau "Ubah Penilaian"</li>
                </ol>
                <p><strong>Fitur Rating:</strong></p>
                <ul>
                    <li>Setiap pengguna dapat memberikan rating sekali per karya (berdasarkan IP address)</li>
                    <li>Jika Anda sudah pernah memberikan rating, Anda dapat mengubah atau membatalkan rating tersebut</li>
                    <li>Rating rata-rata dan jumlah penilaian akan ditampilkan di halaman detail dan galeri</li>
                    <li>Karya dengan rating tertinggi dapat diurutkan di halaman galeri</li>
                </ul>
                <div class="faq-tags">
                    <span class="tag">Rating</span>
                    <span class="tag">Interaksi</span>
                    <span class="tag">Detail Karya</span>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <div class="faq-question">
                Apakah saya perlu login untuk melihat karya?
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Tidak, Anda tidak perlu login untuk melihat karya di Portal TPL. Semua karya yang sudah dipublikasikan dapat diakses secara bebas oleh publik.</p>
                <p>Login hanya diperlukan untuk admin yang ingin mengelola karya (menambah, mengedit, atau menghapus karya).</p>
                <div class="faq-tags">
                    <span class="tag">Akses</span>
                    <span class="tag">Visibilitas</span>
                    <span class="tag">Publik</span>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <div class="faq-question">
                Apa saja kategori karya yang tersedia?
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Portal TPL menampilkan berbagai kategori karya mahasiswa Teknologi Rekayasa Perangkat Lunak, antara lain:</p>
                <ul>
                    <li><strong>Aplikasi Mobile:</strong> Aplikasi untuk platform Android, iOS, atau cross-platform</li>
                    <li><strong>Website & Sistem Informasi:</strong> Website, portal, sistem informasi, dan aplikasi berbasis web</li>
                    <li><strong>Artikel Ilmiah & Jurnal:</strong> Karya tulis ilmiah, jurnal penelitian, dan dokumentasi akademis</li>
                    <li><strong>Hardware & IoT:</strong> Proyek Internet of Things, embedded systems, dan perangkat keras</li>
                    <li><strong>Multimedia & Presentasi:</strong> Konten multimedia, video, animasi, dan presentasi interaktif</li>
                    <li><strong>Dan kategori lainnya</strong> sesuai perkembangan teknologi</li>
                </ul>
                <p>Setiap kategori memiliki warna identitas yang berbeda. Anda dapat memfilter karya berdasarkan kategori di halaman Galeri dengan mengklik bubble kategori berwarna. Kategori dapat dipilih lebih dari satu untuk melihat karya yang memiliki beberapa kategori sekaligus.</p>
                <div class="faq-tags">
                    <span class="tag">Kategori</span>
                    <span class="tag">Jenis Karya</span>
                    <span class="tag">Filter</span>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <div class="faq-question">
                Apakah saya bisa mengunduh file dari karya?
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Ya, jika karya memiliki file pendukung yang dapat diunduh, Anda dapat mengunduhnya langsung dari halaman detail karya. File yang tersedia biasanya berupa:</p>
                <ul>
                    <li><strong>Dokumen PDF:</strong> Laporan proyek, jurnal penelitian, dokumentasi teknis, atau artikel ilmiah</li>
                    <li><strong>File Presentasi:</strong> Slide presentasi, pitch deck, atau materi presentasi</li>
                    <li><strong>Dokumen Pendukung:</strong> Dokumentasi API, user manual, atau file teknis lainnya</li>
                </ul>
                <p><strong>Cara Mengunduh:</strong></p>
                <ol>
                    <li>Buka halaman detail karya yang memiliki file pendukung</li>
                    <li>Scroll ke bagian "File Pendukung"</li>
                    <li>Klik tombol download (ikon 📄) pada file yang ingin Anda unduh</li>
                    <li>File akan terunduh langsung ke perangkat Anda</li>
                </ol>
                <p><strong>Catatan:</strong> Tidak semua karya memiliki file yang dapat diunduh. Tergantung pada jenis karya dan file yang diupload oleh admin saat mempublikasikan karya.</p>
                <div class="faq-tags">
                    <span class="tag">Download</span>
                    <span class="tag">File</span>
                    <span class="tag">Dokumen</span>
                </div>
            </div>
        </div>

        <div class="faq-card">
            <div class="faq-question">
                Bagaimana sistem rating bekerja?
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Sistem rating di Portal TPL dirancang untuk memberikan penilaian yang adil dan akurat:</p>
                <ol>
                    <li><strong>Skala Rating:</strong> Setiap pengunjung dapat memberikan rating 1-5 bintang untuk setiap karya</li>
                    <li><strong>Batasan Rating:</strong> Setiap pengguna hanya dapat memberikan rating sekali per karya (berdasarkan IP address dan UUID browser)</li>
                    <li><strong>Update Rating:</strong> Jika Anda sudah pernah memberikan rating, Anda dapat mengubah penilaian atau membatalkannya</li>
                    <li><strong>Perhitungan:</strong> Rating rata-rata dihitung otomatis dari semua rating yang diberikan dan ditampilkan dalam format "Rating X.X/5 ⭐ (jumlah penilaian)"</li>
                    <li><strong>Sorting:</strong> Di halaman Galeri, Anda dapat mengurutkan karya berdasarkan "Rating Tertinggi" untuk menemukan karya terbaik</li>
                    <li><strong>Display:</strong> Rating ditampilkan di halaman detail karya dan sebagai badge di card karya di halaman galeri</li>
                </ol>
                <p>Rating membantu pengunjung menemukan karya terbaik berdasarkan penilaian komunitas dan memberikan feedback yang berharga bagi mahasiswa pembuat karya.</p>
                <div class="faq-tags">
                    <span class="tag">Rating</span>
                    <span class="tag">Sistem</span>
                    <span class="tag">Penilaian</span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // ===== Bagian: FAQ Accordion =====
    const faqItems = document.querySelectorAll('.faq-card');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // ===== Bagian: Tutup Semua FAQ =====
            faqItems.forEach(faq => faq.classList.remove('active'));
            
            // ===== Bagian: Buka FAQ Terpilih =====
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
</script>

<?php include __DIR__ . '/../layouts/footer_public.php'; ?>

