<?php
session_start();
$page_title = "Dashboard";
include '../config/db_connect.php';
include '../includes/header_admin.php';

// Ambil statistik
$total_karya = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_project"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_category"))['total'];
$total_rating = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_rating"))['total'];

// Ambil karya terbaru
$query_karya = "SELECT p.*, 
                GROUP_CONCAT(c.nama_kategori SEPARATOR ', ') as kategori,
                AVG(r.skor) as avg_rating,
                COUNT(DISTINCT r.id_rating) as total_rating
                FROM tbl_project p
                LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
                LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
                LEFT JOIN tbl_rating r ON p.id_project = r.id_project
                GROUP BY p.id_project
                ORDER BY p.id_project DESC
                LIMIT 10";
$result_karya = mysqli_query($conn, $query_karya);

// Ambil aktivitas terbaru
$query_activity = "SELECT * FROM tbl_activity_logs ORDER BY log_time DESC LIMIT 10";
$result_activity = mysqli_query($conn, $query_activity);
?>

<!-- Header -->
<header class="bg-white shadow-sm">
    <div class="px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
        <p class="text-gray-600 mt-1">Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
    </div>
</header>

<!-- Content -->
<div class="p-8">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Karya</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_karya; ?></p>
                    <p class="text-xs text-gray-400 mt-1">Proyek terdaftar</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Kategori</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_kategori; ?></p>
                    <p class="text-xs text-gray-400 mt-1">Kategori aktif</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Rating</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_rating; ?></p>
                    <p class="text-xs text-gray-400 mt-1">Penilaian diterima</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Projects (2/3 width) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Karya Terbaru</h3>
                    <a href="kelola_karya.php" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        Lihat Semua →
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while($row = mysqli_fetch_assoc($result_karya)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['judul']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($row['pembuat']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($row['kategori'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <span class="text-yellow-500 mr-1">⭐</span>
                                        <?php echo $row['avg_rating'] ? number_format($row['avg_rating'], 1) : '-'; ?>
                                        <span class="text-gray-400 ml-1">(<?php echo $row['total_rating']; ?>)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php 
                                    $status_class = $row['status'] == 'Published' ? 'bg-green-100 text-green-800' : 
                                                   ($row['status'] == 'Draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800');
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Activity Log (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                </div>
                
                <div class="p-4 max-h-96 overflow-y-auto">
                    <div class="space-y-4">
                        <?php while($activity = mysqli_fetch_assoc($result_activity)): ?>
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium"><?php echo htmlspecialchars($activity['username']); ?></span>
                                </p>
                                <p class="text-sm text-gray-600">
                                    <?php echo htmlspecialchars($activity['action']); ?>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?php 
                                    $time = strtotime($activity['log_time']);
                                    echo date('d M Y, H:i', $time);
                                    ?>
                                </p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</div>

<?php include '../includes/footer_admin.php'; ?>