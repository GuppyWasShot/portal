-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql109.infinityfree.com
-- Generation Time: Nov 22, 2025 at 03:32 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40385611_portal_tpl`
--

--
-- Dumping data for table `tbl_project`
--

INSERT INTO `tbl_project` (`id_project`, `judul`, `deskripsi`, `pembuat`, `tanggal_selesai`, `link_external`, `snapshot_url`, `status`) VALUES
(23, 'Website Koperasi Okiagaru Indonesia Agricoop', 'Website Koperasi Okiagaru Indonesia Agricoop merupakan penerapan digital marketing dan media membangun branding terhadap Pemasaran Komoditi Pertanian dan Produk Olahan yang di peroleh dari seluruh anggota Koperasi Okiagaru Indonesia Agricoop.', 'Dr. Veralianta Br Sebayang, SP.,  M.Si.; Dr. Doni Sahat Tua Manalu, SE.,  M.Si.;  Dr. Ir. Suharno, M.Adev; Aditya Wicaksono, S.Komp.,  M.Kom.', '2023-12-13', NULL, NULL, 'Published'),
(24, 'Sistem Kesesuaian Lahan Berbasis Rule', 'Sistem Kesesuaian Lahan Berbasis Rule merupakan sistem yang dapat memberikan rekomendasi tanaman apa yang sesuai dengan karakteristik lahan yang ada dengan pendekatan Expert System dan Decision Support System. Kualitas Lahan, Karakteristik Lahan, Kelompok tanaman dan varian tanaman dapat diperkaya dengan menambah knowledge base.', 'Aditya Wicaksono, S.Komp., M.Kom.; Dr. Veralianta Br Sebayang, SP., M.Si.; Dr. Doni Sahat Tua Manalu, SE., M.Si.', '2024-01-22', NULL, NULL, 'Published'),
(25, 'Sistem Informasi Lab Vokasi IPB (SILVI)', 'Sistem Informasi Lab Vokasi IPB (SILVI) merupakan sistem yang bertujuan untuk membantu pelaksanaan operasional Lab di lingkungan Sekolah Vokasi IPB. SILVI terdiri dari beberapa modul, diantaranya Modul Manajemen Aset, Modul Bebas Administrasi, Modul Penjadwalan, dan Modul Peminjaman.', 'Aditya Wicaksono, S.Komp., M.Kom.', '2024-06-21', NULL, NULL, 'Published'),
(26, 'Aplikasi Pencatatan Pemesanan Jahit di Fauzi Jahit', 'Aplikasi Pencatatan Pemesanan Jahit di Fauzi Jahit digunakan untuk mencatat segala transaksi jahit yang berlaku di Fauzi Jahit. Aplikasi ini tidak hanya digunakan oleh Fauzi Jahit saja, Pelanggan dari Fauzi Jahit juga dapat menggunakan aplikasi ini untuk melihat pesanan mereka sudah selesai atau belum. Tak hanya itu, Aplikasi ini juga menyediakan rangkuman singkat tentang pendapatan yang didapat oleh Fauzi Jahit dari pesanan yang telah mereka selesaikan. Manfaat dari aplikasi ini adalah meningkatkan efisiensi dari pencatatan jahit di Fauzi Jahit dan juga meminimalisir kurangnya pencatatan. Selain itu dengan adanya aplikasi ini pelanggan Fauzi Jahit juga tidak perlu repot repot datang hanya untuk mengetahui status pesanan mereka.', 'Irham Maulana Johani; Miftakhul Ushbah Nastaftian; Arjuna Olanda Putra; Muh Fahrul Fahrezi; Arlyn Stefanny; Medhanita Dewi Renanti; Aditya Wicaksono; Chaerunnisa Ananda Dein', '2024-07-13', NULL, NULL, 'Published'),
(27, 'Website Fruitaria', 'Fruitaria adalah sebuah sistem berbasis web yang dirancang untuk mengatasi permasalahan yang timbul dari metode penjualan buah konvensional yang belum memanfaatkan teknologi modern. Sistem ini berupaya mengatasi masalah transaksi tunai yang rawan utang tanpa pencatatan akurat, serta pencatatan stok dan keuangan yang hanya didasarkan pada transaksi harian. Fruitaria menawarkan solusi dengan menyediakan fitur-fitur yang memudahkan admin toko buah dalam mencatat segala bentuk transaksi, baik tunai maupun utang, memberikan informasi stok yang tersedia dan yang terjual, melakukan manajemen jenis buah yang akan dijual, pengelolaan terhadap akun pengguna, serta melakukan ekspor data penjualan dan stock opname. Pelanggan juga dapat memanfaatkan sistem keranjang belanja dan melakukan pembayaran baik secara tunai maupun non tunai melalui QRIS, serta mendapatkan informasi lengkap mengenai produk yang dijual. Tujuan utama Fruitaria adalah memfasilitasi proses jual beli buah, sehingga memudahkan admin dalam menghitung laba rugi dalam periode tertentu. Fruitaria dibangun dengan pendekatan desain mobile-first, mengingat kecenderungan pelanggan menggunakan ponsel saat berbelanja.', 'Reksa Prayoga Syahputra; Ababil Pusano; Mochamad Tegar Santoso; Vellisya Afifa Qonita; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Ir. Amir Hamzah, M.T., Ph.D.', '2024-07-18', NULL, NULL, 'Published'),
(28, 'Website Pempek Alya', 'Website Pempek Alya hadir sebagai solusi bagi para pecinta kuliner yang ingin menikmati kelezatan khas Palembang tanpa harus mengunjungi toko fisiknya. Dilengkapi dengan gambar dan deskripsi yang informatif, pelanggan dapat dengan mudah memilih makanan sesuai keinginan dan kebutuhan mereka, lalu bisa dimasukkan ke dalam keranjang serta melakukan check out produk. Website Pempek Alya tidak hanya menawarkan kemudahan akses, tetapi juga memberikan pengalaman berbelanja yang menyenangkan. Pelanggan dapat membaca ulasan dari pembeli lain, mendapatkan informasi terbaru tentang promosi dan penawaran menarik, serta terhubung dengan Pempek Alya melalui media sosial.', 'Adelia Tiara Putri;  Hasan Ismail Abdulmalik; Muhammad Gibran Anggalana; Blessanable Maqdaylene Theophilia Odyardy; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Ir. Amir Hamzah, M.T., Ph.D.', '2024-08-02', NULL, NULL, 'Published'),
(29, 'Website Desa Digital Indonesia', 'Website ini menyediakan informasi potensi dan distribusi produk unggulan desa serta rekomendasi wisata di Indonesia. Terdapat empat aktor pada aplikasi desa digital ini yaitu admin, pemilik produk/pihak desa, Dinas, dan Masyarakat/investor. Admin dapat melakukan create, read, update, delete data pengguna, data wilayah (provinsi, kecamatan, kelurahan), komoditas beserta distribusinya, dan tempat wisata. Aktor pemilik produk/pihak desa dan Dinas dapat melakukan create, read, update, delete data komoditas dan distribusi komoditas/produk. Aktor Dinas juga dapat memasukkan data wisata. Masyarakat dan investor dapat mengakses atau melakukan pencarian informasi terkait produk/komoditas desa beserta distribusinya dan rekomendasi tempat wisata.', 'Medhanita Dewi Renanti; Aditya Wicaksono; Muhammad Nasir; Nur Aziezah; Irma RG Barus; Amata Fami; Sofiyanti Indriasari', '2024-11-13', NULL, NULL, 'Published'),
(30, 'Website Wisata Mulyaharja Bogor', 'Website ini untuk mempromosikan wisata mulyaharja Bogor yang terdiri atas Agro Edu Wisata Organik, Saung Eling, dan Lembah Fatamorgana. Tampilan menu dari website ini (front-end) adalah Beranda, Wisata, Berita, Galeri, Produk, Diskusi, Kontak, dan Tentang Kami. Proses Create, Update, Delete informasi dilakukan pada halaman back-end aplikasi.', 'Medhanita Dewi Renanti; Aditya Wicaksono; Muhammad Nasir; Amata Fami; Nur Aziezah; Irma RG Barus', '2024-11-13', NULL, NULL, 'Published'),
(31, 'Website Ecotainment Godong Ijo', 'Website Ecotainment Godong Ijo merupakan website hasil redesain dari website sebelumnya. Redesain dilakukan setelah melakukan analisis kekurangan baik dari aspek User Interface (UI) maupun User Experience (UX). Website ini dilengkapi juga dengan Content Management System (CMS)  yang berfungsi sebagai manajemen konten pada website utama. Website utama dan CMS Ecotainment Godong Ijo, dapat diakses pada link berikut.', 'Dr. Doni Sahat Tua Manalu, SE., M.Si.; Aditya Wicaksono, S.Komp., M.Kom.; Dr. Veralianta Br Sebayang, SP., M.Si.; Nur Aziezah, S.Si., M.Si.; Hikmah Rahmah, S.Si., M.Si.', '2025-03-05', NULL, NULL, 'Published'),
(32, 'Sistem Informasi Geografis Kesesuaian Lahan', 'Sistem Informasi Geografis Kesesuaian Lahan merupakan sistem berbasis website yang dirancang untuk membantu petani mitra Okiagaru Indonesia Agricoop dalam menentukan tanaman yang sesuai dengan kondisi lahan berdasarkan parameter fisik, kimia, dan iklim. Sistem ini bertujuan meningkatkan proses pengambilan keputusan, produktivitas, dan mendukung praktik pertanian berkelanjutan.', 'Aditya Wicaksono, S.Komp., M.Kom.; Dr. Doni Sahat Tua Manalu, SE., M.Si.; Dr. Veralianta Br Sebayang, SP., M.Si.; Agief Julio Pratama, S.P., M.Si.', '2025-02-17', NULL, NULL, 'Published'),
(33, 'HeyCow (Aplikasi Manajemen dan Monitoring Sapi)', 'HeyCow adalah aplikasi Mobile, Web, dan IoT untuk memantau kesehatan, gejala, dan perilaku sapi secara real-time. Dilengkapi fitur ngangon untuk penitipan sapi dan platform diskusi. HeyCow membantu peternak mengelola ternak dengan efisien dan meningkatkan produktivitas.', 'Reksa Prayoga Syahputra; Fahri Radiansyah; Naufalih Muzakki Sujono; Ardien Ferdinand Putra Setiawan; Aditya Rieyza Munif; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-05-20', NULL, NULL, 'Published'),
(34, 'TOKOPONIK', 'TokoPonik adalah aplikasi e-commerce dan company profile yang fokus pada hidroponik. Aplikasi ini menyediakan berbagai tanaman hidroponik, bibit, dan peralatan berkebun, serta informasi lengkap tentang teknik budidaya hidroponik. Dengan TokoPonik, masyarakat dapat dengan mudah membeli kebutuhan berkebun hidroponik sekaligus belajar tentang metode pertanian modern yang ramah lingkungan.', 'Davino Rizqy Dayan; Irham Maulana Johani; Raden Muhammad Raditya Rahman; Muhammad Islah; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-05-20', NULL, NULL, 'Published'),
(35, 'Kebunify', 'Kebunify adalah platform yang menjadi solusi bagi para pekebun yang memiliki masalah dan tantangan dengan kebun atau tanaman kebunnya, Kebunify tersedia dalam bentuk aplikasi Android yang memungkinkan pengguna untuk berkonsultasi, berdiskusi, mendapatkan informasi, dan membeli peralatan dan bahan perkebunan, Kebunify juga tersedia dalam bentuk website yang memungkinkan pengguna publik untuk mendapatkan informasi melalui artikel dan berdiskusi melalui forum.', 'Refy Rizky Ikman Rizaldi; Hermawan Sentyaki Sarjito; Virza Al Durra Winata; Hilal Bintang Sabili; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-05-23', NULL, NULL, 'Published'),
(36, 'AgriLog', 'AgriLog adalah aplikasi mobile yang mempermudah petani mencatat hasil panen sekaligus mengoptimalkan distribusi produk mereka. Hasil panen yang dicatat dapat ditemukan dan dibeli oleh kolektor, distributor, atau pembeli lain sesuai lokasi, menciptakan peluang pasar yang lebih luas. Fitur utama AgriLog adalah pencatatan hasil panen secara real-time, memungkinkan petani mempublikasikan data panen mereka dengan mudah. Selain itu, AgriLog dilengkapi fitur-fitur lain seperti login dan register untuk akses yang aman, forum diskusi sebagai wadah berbagi informasi antar pengguna, artikel yang menyediakan edukasi dan berita terkini seputar pertanian, komoditas untuk menampilkan hasil panen yang tersedia, dan laporan penjualan yang membantu petani melacak serta menganalisis data transaksi mereka. Dengan antarmuka yang sederhana dan inovatif, AgriLog bertujuan mendukung petani meningkatkan visibilitas hasil panen, memperluas pasar, dan pada akhirnya meningkatkan kesejahteraan mereka.', 'Achmad Fauzal Khobir; Ario Elnino; Muhammad Yermi Rachman; Fachrizal Wisnu Pratama; Arjuna Olanda Putra; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.; Lathifunnisa Fathonah, S.ST., M.T.', '2025-06-16', NULL, NULL, 'Published'),
(37, 'BettaBeal', 'BettaBeal adalah sebuah aplikasi e-commerce yang dirancang khusus untuk memfasilitasi para pecinta ikan cupang dalam melakukan jual beli ikan cupang secara online. Selain sebagai platform transaksi, Bettabeal juga menyediakan artikel informatif seputar ikan cupang, seperti tips perawatan, dan panduan pemilihan ikan berkualitas. Aplikasi ini bertujuan untuk menjadi solusi praktis bagi komunitas pecinta ikan cupang, baik untuk kebutuhan perdagangan maupun sumber informasi terpercaya, sehingga mendukung hobi dan bisnis para pecinta ikan cupang.', 'Aubrey Nedwin Mantiri; Muhammad Faris Fadhil Islam; Ferizwan Malik Wichaksana; Athala Fazli Maula; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.; Lathifunnisa Fathonah, S.ST., M.T.', '2025-05-28', NULL, NULL, 'Published'),
(38, 'PoultryLink', 'PoultryLink merupakan marketplace berbasis web dan mobile yang dikembangkan untuk mengatasi tantangan dalam pemasaran produk unggas di Indonesia. Aplikasi ini dirancang untuk mempermudah akses pasar bagi peternak unggas, dengan menyediakan platform untuk menjual produk seperti bibit unggas, telur, daging, dan pakan ternak secara langsung kepada konsumen. dalam hasil implementasi menunjukkan keberhasilan dalam pengembangan fitur utama, seperti registrasi pengguna, pencarian produk, pengelolaan keranjang, hingga pembayaran. Aplikasi ini mampu meningkatkan efisiensi distribusi dan pemasaran produk unggas, mendukung pertumbuhan industri peternakan unggas di Indonesia.', 'Anargya Rabbani Aslam; Akhfa Bagas Alfarizi; Rival Fitrah Dermawan; Yashin Al Fauzy Sabara; Muhamad Mauladi Fadillah; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.; Dr. Inna Novianty, S.Si., M.', '2025-06-16', NULL, NULL, 'Published'),
(39, 'HydraTiva', 'HydraTiva adalah aplikasi yang dirancang untuk meningkatkan produktivitas dan efisiensi di sektor perkebunan tanaman Stevia. Dengan menggabungkan teknologi IoT (Internet of Things) dan analisis data yang canggih, HydraTiva memungkinkan petani untuk memantau kondisi lahan secara real-time, mengatur penyiraman secara otomatis maupun manual, dan membantu penyaluran hasil perkebunan stevia. Aplikasi ini memberikan informasi kadar tanah kebun stevia, histori penyiraman lahan stevia, dan rekomendasi tindakan berdasarkan data yang terkumpul dari sensor yang tersebar di seluruh lahan stevia.', 'Mochamad Tegar Santoso; Muhammad Thariq Aziz; Riyadh Azhara; Shofwan Imtiyaz; Miftakhul Ushbah Nastaftian; Dhiyaurrahman Hamizan Haikal Putra; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.;  Endang Purnama Giri, S.Kom., M.Kom.', '2025-06-16', NULL, NULL, 'Published'),
(40, 'Vet Link', 'Aplikasi Vet Link adalah platform terintegrasi yang dirancang untuk memudahkan pemilik hewan peliharaan dalam mengelola hewan kesayangan, mengatur kunjungan ke klinik hewan, serta memanfaatkan forum kehilangan hewan. Selain itu, Vet Link juga menyediakan dashboard bagi pengelola klinik hewan untuk mengelola dan mengatur kunjungan ke klinik secara efisien.', 'Kevin Farhan Hernandez; Jonathan; Ghaniyy Fattah Ramadhan; M. Rafka Hadyan S.; Muhammad Raihan Zaldiputra; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-06-16', NULL, NULL, 'Published'),
(41, 'TemanTernak', 'TemanTernak adalah aplikasi telemedicine yang dirancang untuk mengatasi hambatan jarak dan waktu antara peternak dan dokter hewan (veterinarian). Aplikasi ini menyediakan platform yang memungkinkan peternak untuk memperoleh layanan konsultasi jarak jauh, termasuk perawatan dan diagnosis awal terhadap penyakit yang dialami oleh hewan ternak mereka. Dengan adanya aplikasi ini, peternak dapat lebih mudah mengakses informasi medis yang relevan, serta menerima rekomendasi perawatan atau tindakan lebih lanjut secara efektif dan efisien tanpa harus melakukan kunjungan langsung ke fasilitas kesehatan hewan.', 'Hasan Ismail Abdulmalik; Muhammad Gibran Anggalana; Dzaky Fachri Hadafi; Rintan Arufafa Aji; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.; Dr. Inna Novianty, S.Si., M.Si.; Lathifunnisa Fathona', '2025-06-16', NULL, NULL, 'Published'),
(42, 'PERKASA (Peternak Ikan Pasti Bisa)', 'Perkasa adalah web dan aplikasi dimana pengguna dapat belajar bagaimana cara berternak ikan dari awal, bertanya ke komunitas peternak lain, dapatkan informasi di panduan, dan juga bertanya kepada pakar.', 'Cahya Ilham; Anwar Faiz Fauzi; Muhammad Zaki Algifari; Muhammad Daffa Abiyya; Aditya Wicaksono, S.Komp., M.Kom.; Gema Parasti Mindara, S.Si., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-06-16', NULL, NULL, 'Published'),
(43, 'Sistem Informasi Pengarsipan Surat Departemen Manajemen Hutan (SIPMANHUT)', 'SIPMANHUT (Sistem Informasi Pengarsipan Surat Departemen Manajemen Hutan) adalah sebuah sistem informasi berbasis web yang dirancang secara khusus untuk mengatasi permasalahan pengelolaan arsip surat yang masih bersifat semi-digital di Departemen Manajemen Hutan (DMNH) IPB University.', 'Tejo Mulyono; Aditya Wicaksono, S.Komp., M.Kom.; Dr. Soni Trison, S.Hut., M.Si.', '2025-11-05', NULL, NULL, 'Published'),
(44, 'SPROUTIFY', 'Sproutify adalah sistem otomatis berbasis Internet of Things (IoT) yang mengintegrasikan alat dan website melalui database Firebase untuk memantau dan merawat bibit cabai secara real-time. Sistem ini dirancang khusus untuk memudahkan petani dalam mengoptimalkan pertumbuhan tanaman cabai mereka.', 'Sarah Aninditya; Jeremia Andreas.P; Muhammad Omar Wylie; Aditya Wicaksono, S.Komp., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-11-05', NULL, NULL, 'Published'),
(45, 'IO-Lock', 'IO-LOCK merupakan kunci pintu khusus ruang server IoT (Internet of Things) menggunakan sensor RFID (Radio Frequency Identification) dan sensor BME280 diintegrasikan dengan Firebase Realtime Database. Admin dapat mengelola akses user berdasarkan nomor ID masing-masing RFID TAG, RFID Card, dan RFID Devices, serta dapat memantau kondisi suhu dan kelembapan ruangan server secara online dan real time.', 'Muhamad Iqbal Faturrahman; Annisa Raihanah Maimun; Selpi Anjeli; Aditya Wicaksono, S.Komp., M.Kom.; Endang Purnama Giri, S.Kom., M.Kom.', '2025-11-05', NULL, NULL, 'Published'),
(46, 'HUJANGA', 'HUJANGA adalah sebuah sistem berbasis web yang dirancang untuk memantau hujan secara real-time di wilayah Bogor. Produk ini lahir dari kebutuhan akan sistem monitoring yang mudah diakses, cepat, dan relevan dengan kondisi lokal. Dengan menggabungkan teknologi Internet of Things (IoT) dan antarmuka web interaktif, HUJANGA memungkinkan pengguna untuk melihat informasi hujan secara langsung, tanpa perlu mengandalkan prediksi umum. Melalui sistem ini, data hujan dikirim secara otomatis dari perangkat sensor ke Firebase dan ditampilkan pada halaman web. Pengguna tidak hanya dapat melihat kondisi hujan saat ini, tetapi juga berkontribusi dengan menambahkan laporan hujan di lokasi mereka. HUJANGA dikembangkan untuk mendukung kegiatan masyarakat seperti pertanian, pendidikan, maupun mitigasi bencana kecil yang berkaitan dengan cuaca. Dengan tampilan antarmuka yang intuitif dan ramah pengguna, HUJANGA dapat digunakan oleh siapa saja, baik pelajar, guru, maupun masyarakat umum. Sistem ini berfokus untuk menampilkan informasi sederhana mengenai kondisi hujan secara real-time, sehingga pengguna bisa dengan cepat mengetahui apakah sedang terjadi hujan atau tidak di wilayah tertentu.', 'Faiq Subhi Ramadlan; Niefa Efrilia Violenic; Muhammad Reynaldi Ilham; Aditya Wicaksono, S.Komp., M.Kom.; Lathifunnisa Fathonah, S.ST., M.T.', '2025-11-05', NULL, NULL, 'Published'),
(47, 'SQUARI', 'Produk ini adalah Sistem Monitoring Real-Time Akuarium dan Lingkungan Sekitar dengan Fitur Auto-Feeding Berbasis Web. Sistem ini menggunakan NodeMCU ESP8266 yang tersambung ke sensor suhu, kelembapan, dan ketinggian air untuk mengumpulkan data secara berkelanjutan dan mengirimkannya ke Firebase Realtime Database. Antarmuka web dibangun dengan Laravel sehingga pengguna dapat memantau kondisi akuarium melalui browser. Tujuan sistem ini adalah meningkatkan efisiensi dan akurasi dalam memantau parameter lingkungan akuarium serta mengotomasi proses pemberian pakan ikan. Sistem akan mengirimkan notifikasi jika parameter melewati batas aman sehingga pengguna dapat segera mengambil tindakan.', 'Nabil Rifqi Wijaya; Daffa Rif\'at Mahardika; Muhammad Sulthan Alfriansyah; Aditya Wicaksono, S.Komp., M.Kom.; Lathifunnisa Fathonah, S.ST., M.T.', '2025-11-05', NULL, NULL, 'Published');

--
-- Dumping data for table `tbl_project_category`
--

INSERT INTO `tbl_project_category` (`id_project`, `id_kategori`) VALUES
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(44, 4),
(45, 4),
(46, 4),
(47, 4);

--
-- Dumping data for table `tbl_project_files`
--

INSERT INTO `tbl_project_files` (`id_file`, `id_project`, `label`, `nama_file`, `file_path`, `file_size`, `mime_type`, `created_at`) VALUES
(52, 23, 'Sertifikat HKI', 'sertifikat_EC002023130745.pdf', 'uploads/files/file_23_1763563345_0.pdf', 2108930, 'application/pdf', '2025-11-19 14:42:25'),
(53, 23, 'Bukti Ciptaan', 'Bukti Ciptaan.pdf', 'uploads/files/file_23_1763563345_1.pdf', 2630518, 'application/pdf', '2025-11-19 14:42:25'),
(54, 23, 'Snapshot 1', 'Logo Okiagaru.png', 'uploads/snapshots/snapshot_23_1763563528_0.png', 109387, 'image/png', '2025-11-19 14:45:28'),
(55, 24, 'Sertifikat HKI', 'sertifikat_EC00202407677.pdf', 'uploads/files/file_24_1763564463_0.pdf', 2108362, 'application/pdf', '2025-11-19 15:01:03'),
(56, 24, 'Bukti Ciptaan', 'Bukti Ciptaan.pdf', 'uploads/files/file_24_1763564463_1.pdf', 2730536, 'application/pdf', '2025-11-19 15:01:03'),
(57, 25, 'Sertifikat HKI', 'sertifikat_EC00202452600.pdf', 'uploads/files/file_25_1763564737_0.pdf', 2091746, 'application/pdf', '2025-11-19 15:05:37'),
(58, 26, 'Sertifikat HKI', 'sertifikat_EC00202464912.pdf', 'uploads/files/file_26_1763564943_0.pdf', 2095709, 'application/pdf', '2025-11-19 15:09:03'),
(59, 27, 'Sertifikat HKI', 'sertifikat_EC00202468106.pdf', 'uploads/files/file_27_1763565258_0.pdf', 2095282, 'application/pdf', '2025-11-19 15:14:19'),
(60, 28, 'Sertifikat HKI', 'sertifikat_EC00202476220.pdf', 'uploads/files/file_28_1763565473_0.pdf', 2094949, 'application/pdf', '2025-11-19 15:17:53'),
(61, 29, 'Sertifikat HKI', 'sertifikat_EC002024224375.pdf', 'uploads/files/file_29_1763565974_0.pdf', 2095821, 'application/pdf', '2025-11-19 15:26:14'),
(62, 30, 'Sertifikat HKI', 'sertifikat_EC002024224379.pdf', 'uploads/files/file_30_1763566250_0.pdf', 2095829, 'application/pdf', '2025-11-19 15:30:50'),
(63, 31, 'Sertifikat HKI', 'sertifikat_EC002025027715.pdf', 'uploads/files/file_31_1763566747_0.pdf', 3243125, 'application/pdf', '2025-11-19 15:39:07'),
(64, 31, 'Bukti Ciptaan', 'Guidebook Website Ecotainment Godong Ijo.pdf', 'uploads/files/file_31_1763566747_1.pdf', 1761929, 'application/pdf', '2025-11-19 15:39:07'),
(65, 32, 'Sertifikat HKI', 'sertifikat_EC00202522000.pdf', 'uploads/files/file_32_1763567004_0.pdf', 2074581, 'application/pdf', '2025-11-19 15:43:24'),
(66, 33, 'Sertifikat HKI', 'sertifikat_EC002025052366.pdf', 'uploads/files/file_33_1763568318_0.pdf', 3245961, 'application/pdf', '2025-11-19 16:05:19'),
(67, 34, 'Sertifikat HKI', 'sertifikat_EC002025052382.pdf', 'uploads/files/file_34_1763568924_0.pdf', 3244797, 'application/pdf', '2025-11-19 16:15:24'),
(68, 35, 'Sertifikat HKI', 'sertifikat_EC002025053878.pdf', 'uploads/files/file_35_1763570617_0.pdf', 3244783, 'application/pdf', '2025-11-19 16:43:37'),
(69, 36, 'Sertifikat HKI', 'SuratPernyataanKI.2.006464-2025.pdf', 'uploads/files/file_36_1763571021_0.pdf', 3240162, 'application/pdf', '2025-11-19 16:50:21'),
(70, 37, 'Sertifikat HKI', 'sertifikat_EC002025056535.pdf', 'uploads/files/file_37_1763571225_0.pdf', 3246011, 'application/pdf', '2025-11-19 16:53:45'),
(71, 37, 'Bukti Ciptaan', 'Bukti Ciptaan.pdf', 'uploads/files/file_37_1763571225_1.pdf', 3838886, 'application/pdf', '2025-11-19 16:53:46'),
(72, 38, 'Sertifikat HKI', 'sertifikat_EC002025067062.pdf', 'uploads/files/file_38_1763571476_0.pdf', 3247732, 'application/pdf', '2025-11-19 16:57:56'),
(73, 39, 'Sertifikat HKI', 'sertifikat_EC002025067065.pdf', 'uploads/files/file_39_1763571712_0.pdf', 3246244, 'application/pdf', '2025-11-19 17:01:51'),
(74, 39, 'Bukti Ciptaan', 'Bukti Ciptaan.pdf', 'uploads/files/file_39_1763571712_1.pdf', 2516139, 'application/pdf', '2025-11-19 17:01:51'),
(75, 40, 'Sertifikat HKI', 'sertifikat_EC002025067070.pdf', 'uploads/files/file_40_1763571885_0.pdf', 3246564, 'application/pdf', '2025-11-19 17:04:44'),
(76, 41, 'Sertifikat HKI', 'sertifikat_EC002025067072.pdf', 'uploads/files/file_41_1763572064_0.pdf', 3245286, 'application/pdf', '2025-11-19 17:07:43'),
(77, 42, 'Sertifikat HKI', 'sertifikat_EC002025067074.pdf', 'uploads/files/file_42_1763572258_0.pdf', 3244713, 'application/pdf', '2025-11-19 17:10:58'),
(78, 43, 'Sertifikat HKI', 'sertifikat_EC002025171603.pdf', 'uploads/files/file_43_1763572411_0.pdf', 3238185, 'application/pdf', '2025-11-19 17:13:31'),
(79, 44, 'Sertifikat HKI', 'sertifikat_EC002025171599.pdf', 'uploads/files/file_44_1763572585_0.pdf', 3241115, 'application/pdf', '2025-11-19 17:16:25'),
(80, 45, 'Sertifikat HKI', 'sertifikat_EC002025171604.pdf', 'uploads/files/file_45_1763572744_0.pdf', 3240459, 'application/pdf', '2025-11-19 17:19:04'),
(81, 46, 'Sertifikat HKI', 'sertifikat_EC002025171609.pdf', 'uploads/files/file_46_1763572887_0.pdf', 3242807, 'application/pdf', '2025-11-19 17:21:27'),
(82, 46, 'Bukti Ciptaan', 'Bukti Ciptaan.pdf', 'uploads/files/file_46_1763572887_1.pdf', 574143, 'application/pdf', '2025-11-19 17:21:27'),
(83, 47, 'Sertifikat HKI', 'sertifikat_EC002025171592.pdf', 'uploads/files/file_47_1763573029_0.pdf', 3243504, 'application/pdf', '2025-11-19 17:23:49'),
(84, 47, 'Bukti Ciptaan', 'Bukti Ciptaan.pdf', 'uploads/files/file_47_1763573029_1.pdf', 4554312, 'application/pdf', '2025-11-19 17:23:50'),
(85, 47, 'Snapshot 1', 'Screenshot from 2025-11-04 12-28-37.png', 'uploads/snapshots/snapshot_47_1763612539_0.png', 34976, 'image/png', '2025-11-20 04:22:19'),
(86, 47, 'Snapshot 2', 'Screenshot from 2025-11-04 12-29-29.png', 'uploads/snapshots/snapshot_47_1763612539_1.png', 50364, 'image/png', '2025-11-20 04:22:19'),
(87, 47, 'Snapshot 3', 'Screenshot from 2025-11-04 12-32-23.png', 'uploads/snapshots/snapshot_47_1763612539_2.png', 13433, 'image/png', '2025-11-20 04:22:19'),
(88, 47, 'Snapshot 4', 'Screenshot from 2025-11-04 12-43-16.png', 'uploads/snapshots/snapshot_47_1763612539_3.png', 19119, 'image/png', '2025-11-20 04:22:19');

--
-- Dumping data for table `tbl_project_links`
--

INSERT INTO `tbl_project_links` (`id_link`, `id_project`, `label`, `url`, `is_primary`, `created_at`) VALUES
(24, 23, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032333133303734357c636f70797269676874', 1, '2025-11-19 14:42:25'),
(25, 24, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/454330303230323430373637377c636f70797269676874', 1, '2025-11-19 15:01:03'),
(26, 25, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/454330303230323435323630307c636f70797269676874', 1, '2025-11-19 15:05:37'),
(27, 26, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/454330303230323436343931327c636f70797269676874', 1, '2025-11-19 15:09:03'),
(28, 27, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/454330303230323436383130367c636f70797269676874', 1, '2025-11-19 15:14:18'),
(29, 28, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/454330303230323437363232307c636f70797269676874', 1, '2025-11-19 15:17:53'),
(30, 29, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032343232343337357c636f70797269676874', 1, '2025-11-19 15:26:14'),
(31, 30, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032343232343337397c636f70797269676874', 1, '2025-11-19 15:30:50'),
(32, 31, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353032373731357c636f70797269676874', 1, '2025-11-19 15:39:07'),
(33, 32, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/454330303230323532323030307c636f70797269676874', 1, '2025-11-19 15:43:24'),
(34, 33, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353035323336367c636f70797269676874', 1, '2025-11-19 16:05:18'),
(35, 25, 'Jurnal', 'https://doi.org/10.32493/jtsi.v7i4.44077', 0, '2025-11-19 16:08:15'),
(36, 34, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353035323338327c636f70797269676874', 1, '2025-11-19 16:15:24'),
(37, 35, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353035333837387c636f70797269676874', 1, '2025-11-19 16:43:37'),
(38, 36, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353035333839307c636f70797269676874', 1, '2025-11-19 16:50:21'),
(39, 37, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353035363533357c636f70797269676874', 1, '2025-11-19 16:53:45'),
(40, 38, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353036373036327c636f70797269676874', 1, '2025-11-19 16:57:56'),
(41, 39, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353036373036357c636f70797269676874', 1, '2025-11-19 17:01:51'),
(42, 40, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353036373037307c636f70797269676874', 1, '2025-11-19 17:04:44'),
(43, 41, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353036373037327c636f70797269676874', 1, '2025-11-19 17:07:43'),
(44, 42, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353036373037347c636f70797269676874', 1, '2025-11-19 17:10:58'),
(45, 43, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353137313630337c636f70797269676874', 1, '2025-11-19 17:13:31'),
(46, 44, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353137313539397c636f70797269676874', 1, '2025-11-19 17:16:25'),
(47, 45, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353137313630347c636f70797269676874', 1, '2025-11-19 17:19:04'),
(48, 46, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353137313630397c636f70797269676874', 1, '2025-11-19 17:21:27'),
(49, 47, 'Link Project', 'https://pdki-indonesia.dgip.go.id/link/45433030323032353137313539327c636f70797269676874', 1, '2025-11-19 17:23:49');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
