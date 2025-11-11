<?php
session_start();
$page_title = "Detail Karya";
include '../config/db_connect.php';

// Generate atau ambil UUID user dari session
if (!isset($_SESSION['user_uuid'])) {
    $_SESSION['user_uuid'] = uniqid('user_', true);
}

$id_project = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_project <= 0) {
    header("Location: galeri.php");
    exit();
}

// Ambil data karya
$stmt = $conn->prepare("SELECT p.*, 
                        GROUP_CONCAT(DISTINCT c.nama_kategori ORDER BY c.nama_kategori SEPARATOR ', ') as kategori,
                        GROUP_CONCAT(DISTINCT c.warna_hex ORDER BY c.nama_kategori SEPARATOR ',') as warna,
                        AVG(r.skor) as avg_rating,
                        COUNT(DISTINCT r.id_rating) as total_rating
                        FROM tbl_project p
                        LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
                        LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
                        LEFT JOIN tbl_rating r ON p.id_project = r.id_project
                        WHERE p.id_project = ? AND p.status = 'Published'
                        GROUP BY p.id_project");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
$karya = $result->fetch_assoc();
$stmt->close();

if (!$karya) {
    header("Location: galeri.php");
    exit();
}

// Update page title
$page_title = $karya['judul'];

// Ambil links
$stmt = $conn->prepare("SELECT * FROM tbl_project_links WHERE id_project = ? ORDER BY is_primary DESC");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result_links = $stmt->get_result();
$links = [];
while ($row = $result_links->fetch_assoc()) {
    $links[] = $row;
}
$stmt->close();

// Ambil files
$stmt = $conn->prepare("SELECT * FROM tbl_project_files WHERE id_project = ? ORDER BY id_file ASC");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result_files = $stmt->get_result();
$files = [];
while ($row = $result_files->fetch_assoc()) {
    $files[] = $row;
}
$stmt->close();

// Cek apakah user sudah pernah rating
$user_ip = $_SERVER['REMOTE_ADDR'];
$user_uuid = $_SESSION['user_uuid'];

$stmt = $conn->prepare("SELECT skor FROM tbl_rating WHERE id_project = ? AND (uuid_user = ? OR ip_address = ?)");
$stmt->bind_param("iss", $id_project, $user_uuid, $user_ip);
$stmt->execute();
$result_rating = $stmt->get_result();
$user_rating = $result_rating->fetch_assoc();
$stmt->close();

// Pisahkan files berdasarkan tipe
$snapshots = array_filter($files, function($f) {
    return strpos($f['file_path'], 'snapshots') !== false;
});
$documents = array_filter($files, function($f) {
    return strpos($f['file_path'], 'files') !== false;
});

include '../includes/header_public.php';
?>

<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="index.php" class="hover:text-indigo-600">Beranda</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="galeri.php" class="hover:text-indigo-600">Galeri</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($karya['judul']); ?></span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Media & Gallery -->
        <div class="lg:col-span-2">
            
            <!-- Main Image -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <?php if (!empty($karya['snapshot_url'])): ?>
                <img src="../<?php echo htmlspecialchars($karya['snapshot_url']); ?>" 
                     alt="<?php echo htmlspecialchars($karya['judul']); ?>"
                     class="w-full h-96 object-cover">
                <?php else: ?>
                <div class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                    <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Additional Snapshots Gallery -->
            <?php if (count($snapshots) > 1): ?>
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Galeri Foto</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($snapshots as $snapshot): ?>
                    <a href="../<?php echo htmlspecialchars($snapshot['file_path']); ?>" target="_blank"
                       class="group relative aspect-video bg-gray-100 rounded-lg overflow-hidden hover:shadow-lg transition">
                        <img src="../<?php echo htmlspecialchars($snapshot['file_path']); ?>" 
                             alt="Snapshot"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Description -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Deskripsi</h2>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($karya['deskripsi'])); ?>
                </div>
            </div>
            
            <!-- Documents -->
            <?php if (!empty($documents)): ?>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    File Pendukung
                </h3>
                <div class="space-y-3">
                    <?php foreach ($documents as $doc): ?>
                    <a href="../<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" download
                       class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($doc['label']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($doc['nama_file']); ?> • <?php echo round($doc['file_size']/1024, 1); ?> KB</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Right Column: Info & Actions -->
        <div class="lg:col-span-1">
            
            <!-- Title & Categories -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($karya['judul']); ?></h1>
                
                <!-- Categories -->
                <?php if ($karya['kategori']): 
                    $kategori_arr = explode(', ', $karya['kategori']);
                    $warna_arr = explode(',', $karya['warna']);
                    $icons_arr = explode(',', $karya['icons']);
                ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach($kategori_arr as $idx => $kat): 
                        $warna = $warna_arr[$idx] ?? '#6B7280';
                    ?>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full" 
                          style="background-color: <?php echo $warna; ?>20; color: <?php echo $warna; ?>">
                        <span class="mr-1"><?php echo $icon; ?></span>
                        <?php echo htmlspecialchars($kat); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Meta Info -->
                <div class="space-y-3 text-sm">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-medium"><?php echo htmlspecialchars($karya['pembuat']); ?></span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span><?php echo date('d F Y', strtotime($karya['tanggal_selesai'])); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Rating Box -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Rating & Penilaian</h3>
                
                <!-- Average Rating Display -->
                <div class="text-center mb-6 pb-6 border-b border-gray-200">
                    <?php if ($karya['avg_rating']): ?>
                    <div class="text-5xl font-bold text-gray-900 mb-2">
                        <?php echo number_format($karya['avg_rating'], 1); ?>
                    </div>
                    <div class="flex justify-center mb-2">
                        <?php 
                        $avg = round($karya['avg_rating']);
                        for ($i = 1; $i <= 5; $i++): 
                        ?>
                        <svg class="w-6 h-6 <?php echo $i <= $avg ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <p class="text-sm text-gray-600"><?php echo $karya['total_rating']; ?> penilaian</p>
                    <?php else: ?>
                    <div class="text-gray-400 mb-2">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <p class="text-sm mt-2">Belum ada penilaian</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- User Rating Form -->
                <?php if ($user_rating): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-800 font-medium">Anda sudah memberikan rating</p>
                    <p class="text-xs text-green-600 mt-1">Rating Anda: <?php echo $user_rating['skor']; ?> bintang</p>
                </div>
                <?php else: ?>
                <div>
                    <p class="text-sm text-gray-600 mb-3 text-center">Berikan penilaian Anda</p>
                    <form id="ratingForm" method="POST" action="proses_rating.php" class="text-center">
                        <input type="hidden" name="id_project" value="<?php echo $id_project; ?>">
                        <div class="flex justify-center mb-4" id="starRating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 transition" data-rating="<?php echo $i; ?>">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="skor" id="skorInput" required>
                        <button type="submit" id="submitBtn" disabled
                                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg transition disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-indigo-700">
                            Kirim Penilaian
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Links -->
            <?php if (!empty($links)): ?>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Tautan</h3>
                <div class="space-y-3">
                    <?php foreach ($links as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between p-3 bg-gray-50 hover:bg-indigo-50 rounded-lg transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">
                                <?php echo htmlspecialchars($link['label']); ?>
                            </span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
    </div>
    
</div>

<script>
// Star rating functionality
const stars = document.querySelectorAll('.star-btn');
const skorInput = document.getElementById('skorInput');
const submitBtn = document.getElementById('submitBtn');

stars.forEach(star => {
    star.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        skorInput.value = rating;
        submitBtn.disabled = false;
        
        // Update star colors
        stars.forEach((s, index) => {
            if (index < rating) {
                s.classList.remove('text-gray-300');
                s.classList.add('text-yellow-400');
            } else {
                s.classList.remove('text-yellow-400');
                s.classList.add('text-gray-300');
            }
        });
    });
    
    // Hover effect
    star.addEventListener('mouseenter', function() {
        const rating = parseInt(this.dataset.rating);
        stars.forEach((s, index) => {
            if (index < rating) {
                s.classList.add('text-yellow-400');
            }
        });
    });
});

document.getElementById('starRating').addEventListener('mouseleave', function() {
    const currentRating = parseInt(skorInput.value) || 0;
    stars.forEach((s, index) => {
        if (index < currentRating) {
            s.classList.remove('text-gray-300');
            s.classList.add('text-yellow-400');
        } else {
            s.classList.remove('text-yellow-400');
            s.classList.add('text-gray-300');
        }
    });
});
</script>

<?php include '../includes/footer_public.php'; ?>