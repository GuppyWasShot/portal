<?php
$page_title = "Galeri Karya";
include '../config/db_connect.php';
include '../includes/header_public.php';

// Ambil parameter pencarian dan filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : [];

// Ambil semua kategori untuk filter
$query_kategori = "SELECT * FROM tbl_category ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Build query untuk karya
$where_conditions = ["p.status = 'Published'"];
$params = [];
$types = "";

// Filter pencarian
if (!empty($search)) {
    $where_conditions[] = "(p.judul LIKE ? OR p.pembuat LIKE ? OR p.deskripsi LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Filter kategori
if (!empty($kategori_filter) && is_array($kategori_filter)) {
    $placeholders = implode(',', array_fill(0, count($kategori_filter), '?'));
    $where_conditions[] = "pc.id_kategori IN ($placeholders)";
    foreach ($kategori_filter as $kat_id) {
        $params[] = intval($kat_id);
        $types .= "i";
    }
}

// Order by
$order_by = "p.id_project DESC"; // default: terbaru
switch ($sort) {
    case 'judul_asc':
        $order_by = "p.judul ASC";
        break;
    case 'judul_desc':
        $order_by = "p.judul DESC";
        break;
    case 'terlama':
        $order_by = "p.tanggal_selesai ASC";
        break;
    case 'rating':
        $order_by = "avg_rating DESC";
        break;
}

$where_clause = implode(' AND ', $where_conditions);

$query_karya = "SELECT p.*, 
                GROUP_CONCAT(DISTINCT c.nama_kategori ORDER BY c.nama_kategori SEPARATOR ', ') as kategori,
                GROUP_CONCAT(DISTINCT c.warna_hex ORDER BY c.nama_kategori SEPARATOR ',') as warna,
                AVG(r.skor) as avg_rating,
                COUNT(DISTINCT r.id_rating) as total_rating
                FROM tbl_project p
                LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
                LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
                LEFT JOIN tbl_rating r ON p.id_project = r.id_project
                WHERE $where_clause
                GROUP BY p.id_project
                ORDER BY $order_by";

if (!empty($params)) {
    $stmt = $conn->prepare($query_karya);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result_karya = $stmt->get_result();
} else {
    $result_karya = mysqli_query($conn, $query_karya);
}

$total_hasil = mysqli_num_rows($result_karya);
?>

<!-- Hero Section -->
<div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Galeri Karya Mahasiswa</h1>
        <p class="text-xl text-indigo-100">Jelajahi inovasi dan kreativitas mahasiswa Teknologi Rekayasa Perangkat Lunak</p>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <form method="GET" action="galeri.php" class="bg-white rounded-xl shadow-md p-6 mb-8">
        
        <!-- Search Bar -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Karya</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari berdasarkan judul, pembuat, atau deskripsi..."
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </div>
        </div>
        
        <!-- Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Sort Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select name="sort" onchange="this.form.submit()" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="terbaru" <?php echo $sort == 'terbaru' ? 'selected' : ''; ?>>Terbaru</option>
                    <option value="terlama" <?php echo $sort == 'terlama' ? 'selected' : ''; ?>>Terlama</option>
                    <option value="judul_asc" <?php echo $sort == 'judul_asc' ? 'selected' : ''; ?>>Judul (A-Z)</option>
                    <option value="judul_desc" <?php echo $sort == 'judul_desc' ? 'selected' : ''; ?>>Judul (Z-A)</option>
                    <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Rating Tertinggi</option>
                </select>
            </div>
            
            <!-- Category Filter (kept for form preservation) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                <button type="button" onclick="toggleKategoriFilter()" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-left hover:bg-gray-50 transition flex items-center justify-between">
                    <span><?php echo !empty($kategori_filter) ? count($kategori_filter) . ' kategori dipilih' : 'Semua Kategori'; ?></span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
            
        </div>
        
        <!-- Category Filter Dropdown -->
        <div id="kategoriFilterBox" class="hidden mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php 
                mysqli_data_seek($result_kategori, 0);
                while($kat = mysqli_fetch_assoc($result_kategori)): 
                    $checked = in_array($kat['id_kategori'], $kategori_filter) ? 'checked' : '';
                ?>
                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-gray-100">
                    <input type="checkbox" name="kategori[]" value="<?php echo $kat['id_kategori']; ?>" 
                           <?php echo $checked; ?>
                           onchange="this.form.submit()"
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="text-sm"><?php echo htmlspecialchars($kat['nama_kategori']); ?></span>
                </label>
                <?php endwhile; ?>
            </div>
            <?php if (!empty($kategori_filter)): ?>
            <button type="button" onclick="clearKategori()" 
                    class="mt-3 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 underline">
                Hapus semua filter kategori
            </button>
            <?php endif; ?>
        </div>
        
        <!-- Active Filters Display -->
        <?php if (!empty($search) || !empty($kategori_filter)): ?>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-gray-600">Filter aktif:</span>
                
                <?php if (!empty($search)): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    "<?php echo htmlspecialchars($search); ?>"
                </span>
                <?php endif; ?>
                
                <?php if (!empty($kategori_filter)): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                    <?php echo count($kategori_filter); ?> kategori
                </span>
                <?php endif; ?>
                
                <a href="galeri.php" class="text-sm text-red-600 hover:text-red-800 underline">
                    Hapus semua filter
                </a>
            </div>
        </div>
        <?php endif; ?>
        
    </form>
    
    <!-- Results Info -->
    <div class="mb-6">
        <p class="text-gray-600">
            Menampilkan <span class="font-semibold text-gray-900"><?php echo $total_hasil; ?></span> karya
            <?php if (!empty($search)): ?>
            untuk pencarian "<span class="font-semibold"><?php echo htmlspecialchars($search); ?></span>"
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Karya Grid -->
    <?php if ($total_hasil > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <?php while($karya = mysqli_fetch_assoc($result_karya)): ?>
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
                
                <!-- Rating Badge -->
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
                
                <!-- Categories -->
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
                
                <!-- Title -->
                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition">
                    <?php echo htmlspecialchars($karya['judul']); ?>
                </h3>
                
                <!-- Creator -->
                <p class="text-sm text-gray-600 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <?php echo htmlspecialchars($karya['pembuat']); ?>
                </p>
                
                <!-- Description -->
                <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                    <?php echo htmlspecialchars(substr($karya['deskripsi'], 0, 100)); ?>...
                </p>
                
                <!-- Footer -->
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
    
    <?php else: ?>
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada karya ditemukan</h3>
        <p class="text-gray-600 mb-6">
            <?php if (!empty($search) || !empty($kategori_filter)): ?>
            Coba ubah filter atau kata kunci pencarian Anda
            <?php else: ?>
            Belum ada karya yang dipublikasikan
            <?php endif; ?>
        </p>
        <?php if (!empty($search) || !empty($kategori_filter)): ?>
        <a href="galeri.php" class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
            Lihat Semua Karya
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
</div>

<script>
function toggleKategoriFilter() {
    document.getElementById('kategoriFilterBox').classList.toggle('hidden');
}

function clearKategori() {
    // Uncheck all category checkboxes
    document.querySelectorAll('input[name="kategori[]"]').forEach(cb => cb.checked = false);
    // Submit form
    document.querySelector('form').submit();
}
</script>

<?php include '../includes/footer_public.php'; ?>