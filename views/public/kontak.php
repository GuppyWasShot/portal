<?php
/**
 * Kontak TPL - Halaman Kontak
 */

$page_title = "Kontak";
$body_class = 'page-kontak';
$additional_stylesheets = ['assets/css/page-kontak.css'];
include __DIR__ . '/../layouts/header_public.php';
?>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="contact-hero-content">
            <h1>Hubungi <span class="highlight">Kami</span></h1>
            <p>Terhubung dengan tim Portal TPL untuk pertanyaan, saran, atau kolaborasi.</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-grid">
            <!-- Contact Info Cards -->
            <div class="contact-cards">
                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <h3>Telepon</h3>
                        <p><a href="tel:02518329101">(0251) 8329101</a></p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <h3>Email</h3>
                        <p><a href="mailto:portaltpl@gmail.com">portaltpl@gmail.com</a></p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="contact-info">
                        <h3>Alamat</h3>
                        <p>Jl. Kumbang No.14, Kelurahan Babakan, Kecamatan Bogor Tengah, Kota Bogor, Jawa Barat 16128</p>
                    </div>
                </div>
            </div>

            <!-- Contact Note -->
            <div class="contact-form">
                <div class="form-alert is-info">
                    <p>Untuk sementara kami belum menyediakan formulir kontak. Silakan hubungi kami melalui email <a href="mailto:portaltpl@gmail.com">portaltpl@gmail.com</a> atau telepon resmi yang tertera.</p>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <h2>Lokasi Kami</h2>
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.2626948058986!2d106.80755431477394!3d-6.608189395231588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c5d2e602b501%3A0x25a12f0f97fac4ee!2sSekolah%20Vokasi%20IPB!5e0!3m2!1sid!2sid!4v1234567890123!5m2!1sid!2sid" 
                    width="100%" 
                    height="100%" 
                    style="border:0; border-radius: 15px;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </main>

<?php include __DIR__ . '/../layouts/footer_public.php'; ?>

