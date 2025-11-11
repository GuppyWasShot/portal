<?php
$page_title = "Beranda";
include '../config/db_connect.php';
include '../includes/header_public.php';

// Ambil karya terbaru (Featured)
$query_featured = "SELECT p.*, 
                   GROUP_CONCAT(DISTINCT c.nama_kategori ORDER BY c.nama_kategori SEPARATOR ', ') as kategori,
                   GROUP_CONCAT(DISTINCT c.warna_hex ORDER BY c.nama_kategori SEPARATOR ',') as warna,
                   AVG(r.skor) as avg_rating,
                   COUNT(DISTINCT r.id_rating) as total_rating
                   FROM tbl_project p
                   LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
                   LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
                   LEFT JOIN tbl_rating r ON p.id_project = r.id_project
                   WHERE p.status = 'Published'
                   GROUP BY p.id_project
                   ORDER BY p.id_project DESC
                   LIMIT 6";
$result_featured = mysqli_query($conn, $query_featured);

// Ambil kategori
$query_kategori = "SELECT * FROM tbl_category ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Statistik
$total_karya = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_project WHERE status = 'Published'"))['total'];
?>

<!-- Hero Section -->
<div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Portal Karya Mahasiswa TPL
            </h1>
            <p class="text-xl md:text-2xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                Jelajahi inovasi dan kreativitas mahasiswa Teknologi Rekayasa Perangkat Lunak, 
                Sekolah Vokasi IPB University
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="galeri.php" 
                   class="px-8 py-4 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition text-lg shadow-lg">
                    Jelajahi Karya
                </a>
                <a href="tentang.php" 
                   class="px-8 py-4 bg-indigo-500/30 backdrop-blur-sm border-2 border-white text-white rounded-lg font-semibold hover:bg-indigo-500/50 transition text-lg">
                    Tentang TPL
                </a>
            </div>
            
            <!-- Stats -->
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6">
                    <div class="text-4xl font-bold mb-2"><?php echo $total_karya; ?>+</div>
                    <div class="text-indigo-100">Karya Dipublikasikan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6">
                    <div class="text-4xl font-bold mb-2"><?php echo mysqli_num_rows($result_kategori); ?></div>
                    <div class="text-indigo-100">Kategori</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6">
                    <div class="text-4xl font-bold mb-2">100+</div>
                    <div class="text-indigo-100">Mahasiswa Berkontribusi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Kategori Karya</h2>
        <p class="text-lg text-gray-600">Temukan karya berdasarkan kategori yang Anda minati</p>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
        <a href="galeri.php?kategori[]=<?php echo $kategori['id_kategori']; ?>" 
           class="group bg-white rounded-xl shadow-md p-8 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 text-center"
           style="border-top: 4px solid <?php echo $kategori['warna_hex']; ?>">
            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition">
                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
            </h3>
            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($kategori['deskripsi']); ?></p>
        </a>
        <?php endwhile; ?>
    </div>
</div>

<!-- Featured Projects -->
<?php if (mysqli_num_rows($result_featured) > 0): ?>
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Karya Terbaru</h2>
            <p class="text-lg text-gray-600">Inovasi terbaru dari mahasiswa TPL</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($karya = mysqli_fetch_assoc($result_featured)): ?>
            <a href="detail_karya.php?id=<?php echo $karya['id_project']; ?>" 
               class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                
                <!-- Thumbnail -->
                <div class="relative h-48 bg-gradient-to-br from-gray-200 to-gray-300 overflow-hidden">
                    <?php if (!empty($karya['snapshot_url'])): ?>
                    <img src="../<?php echo htmlspecialchars($karya['snapshot_url']); ?>" 
                         alt="<?php echo htmlspecialchars($karya['judul']); ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($karya['avg_rating']): ?>
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full flex items-center">
                        <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-900"><?php echo number_format($karya['avg_rating'], 1); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Content -->
                <div class="p-5">
                    <?php if ($karya['kategori']): 
                        $kategori_arr = explode(', ', $karya['kategori']);
                        $warna_arr = explode(',', $karya['warna']);
                        $icons_arr = explode(',', $karya['icons']);
                    ?>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <?php foreach($kategori_arr as $idx => $kat): 
                            $warna = $warna_arr[$idx] ?? '#6B7280';
                        ?>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full" 
                              style="background-color: <?php echo $warna; ?>20; color: <?php echo $warna; ?>">
                            <span class="mr-1"><?php echo $icon; ?></span>
                            <?php echo htmlspecialchars($kat); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition">
                        <?php echo htmlspecialchars($karya['judul']); ?>
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <?php echo htmlspecialchars($karya['pembuat']); ?>
                    </p>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-500">
                            <?php echo date('Y', strtotime($karya['tanggal_selesai'])); ?>
                        </span>
                        <span class="text-sm text-indigo-600 font-medium group-hover:underline flex items-center">
                            Lihat Detail
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="galeri.php" class="inline-block px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition">
                Lihat Semua Karya
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- CTA Section -->
<div class="bg-indigo-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Mahasiswa TPL?</h2>
        <p class="text-xl text-indigo-100 mb-8">Hubungi admin untuk mempublikasikan karya Anda di portal ini</p>
        <a href="mailto:tpl@apps.ipb.ac.id" class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition">
            Hubungi Admin
        </a>
    </div>
</div>

<?php include '../includes/footer_public.php'; ?>