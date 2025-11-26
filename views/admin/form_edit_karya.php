<?php
session_start();
$page_title = "Kelola Karya";
require_once __DIR__ . '/../../app/autoload.php';
$db = Database::getInstance()->getConnection();
$conn = $db;
include __DIR__ . '/../layouts/header_admin.php';

$id_project = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_project <= 0) {
    header("Location: kelola_karya.php?error=invalid_id");
    exit();
}

// Ambil data karya
$stmt = $conn->prepare("SELECT * FROM tbl_project WHERE id_project = ?");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
$karya = $result->fetch_assoc();
$stmt->close();

if (!$karya) {
    header("Location: kelola_karya.php?error=not_found");
    exit();
}

// Ambil kategori yang sudah dipilih
$stmt = $conn->prepare("SELECT id_kategori FROM tbl_project_category WHERE id_project = ?");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result = $stmt->get_result();
$selected_categories = [];
while ($row = $result->fetch_assoc()) {
    $selected_categories[] = $row['id_kategori'];
}
$stmt->close();

// Ambil semua kategori
$query_kategori = "SELECT * FROM tbl_category ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Ambil link yang sudah ada
$stmt = $conn->prepare("SELECT * FROM tbl_project_links WHERE id_project = ? ORDER BY is_primary DESC");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result_links = $stmt->get_result();
$links = [];
while ($row = $result_links->fetch_assoc()) {
    $links[] = $row;
}
$stmt->close();

// Ambil file yang sudah ada
$stmt = $conn->prepare("SELECT * FROM tbl_project_files WHERE id_project = ?");
$stmt->bind_param("i", $id_project);
$stmt->execute();
$result_files = $stmt->get_result();
$files = [];
while ($row = $result_files->fetch_assoc()) {
    $files[] = $row;
}
$stmt->close();
?>

<style>
.category-card {
    background-color: #f3f4f6;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    transition: all .2s ease;
    color: #4b5563;
    text-align: center;
}
.category-card .category-name {
    color: #4b5563;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color .2s ease;
}
.peer:checked + .category-card {
    background-color: var(--cat-bg, #eef2ff);
    border-color: var(--cat-border, #6366f1);
    color: var(--cat-text, #4338ca);
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
}
.peer:checked + .category-card .category-name {
    color: var(--cat-text, #4338ca);
}
</style>

<header class="bg-white shadow-sm">
    <div class="px-8 py-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="kelola_karya.php" class="hover:text-indigo-600">Kelola Karya</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">Edit Karya</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Karya</h2>
        <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($karya['judul']); ?></p>
    </div>
</header>

<div class="p-8">
    
    <div class="bg-white rounded-xl shadow-md p-6">
        
        <form action="../../controllers/admin/proses_edit_karya.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <input type="hidden" name="id_project" value="<?php echo $id_project; ?>">
            
            <!-- Judul Karya -->
            <div>
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Karya <span class="text-red-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" required
                       value="<?php echo htmlspecialchars($karya['judul']); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <!-- Pembuat -->
            <div>
                <label for="pembuat" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Pembuat <span class="text-red-500">*</span>
                </label>
                <input type="text" id="pembuat" name="pembuat" required
                       value="<?php echo htmlspecialchars($karya['pembuat']); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder='Nama atau tim pembuat (pisahkan dengan ";")'>
                <p class="text-xs text-gray-500 mt-1">Jika lebih dari satu nama, pisahkan menggunakan tanda titik koma (;). Contoh: "Ani; Budi; Charlie".</p>
            </div>
            
            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Karya <span class="text-red-500">*</span>
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="5" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($karya['deskripsi']); ?></textarea>
            </div>
            
            <!-- Kategori Toggle -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <?php while($kategori = mysqli_fetch_assoc($result_kategori)): 
                        $color = $kategori['warna_hex'];
                        $bgColor = $color . '1a';
                        $isChecked = in_array($kategori['id_kategori'], $selected_categories);
                    ?>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" name="kategori[]" value="<?php echo $kategori['id_kategori']; ?>"
                               <?php echo $isChecked ? 'checked' : ''; ?>
                               class="peer sr-only">
                        <div class="category-card"
                             style="--cat-bg: <?php echo $bgColor; ?>;
                                    --cat-border: <?php echo $color; ?>;
                                    --cat-text: <?php echo $color; ?>;">
                            <span class="category-name">
                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                </span>
                        </div>
                    </label>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Tanggal Selesai -->
            <div>
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal/Tahun Karya Selesai <span class="text-red-500">*</span>
                </label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" required
                       value="<?php echo $karya['tanggal_selesai']; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <!-- Snapshot Existing -->
            <?php
            $snapshots = array_filter($files, function($f) {
                return strpos($f['file_path'], 'snapshots') !== false;
            });

            if (!empty($snapshots)):
                
            ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Snapshot Yang Ada
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($snapshots as $snapshot): ?>
                    <div class="relative group" data-file-id="<?php echo $snapshot['id_file']; ?>">
                        <img src="../../<?php echo htmlspecialchars($snapshot['file_path']); ?>" 
                             class="w-full h-32 object-cover rounded-lg border-2 border-gray-300">
                        <button type="button" onclick="deleteFile(<?php echo $snapshot['id_file']; ?>)"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 transition opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Upload Snapshot Baru -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Snapshot Baru (Opsional)
                </label>
                
                <input type="file" id="snapshotInput" accept="image/*" multiple class="hidden">
                
                <button type="button" onclick="document.getElementById('snapshotInput').click()" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Snapshot Baru
                </button>
                <p class="text-xs text-gray-500 mt-1">Snapshot baru akan ditambahkan, tidak menghapus yang lama</p>
                
                <div id="previewContainer" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
            </div>
            
            <!-- Link Existing & Baru -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Link Karya</label>
                <div class="space-y-3 mb-3">
                    <?php foreach ($links as $link): ?>
                    <div class="flex gap-2 items-start bg-gray-50 p-3 rounded-lg" data-link-id="<?php echo $link['id_link']; ?>">
                        <input type="text" value="<?php echo htmlspecialchars($link['label']); ?>" readonly
                               class="w-1/3 px-3 py-2 border border-gray-200 rounded-lg bg-white">
                        <input type="url" value="<?php echo htmlspecialchars($link['url']); ?>" readonly
                               class="flex-1 px-3 py-2 border border-gray-200 rounded-lg bg-white">
                        <?php if (!$link['is_primary']): ?>
                        <button type="button" onclick="deleteLink(<?php echo $link['id_link']; ?>)"
                                class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <?php else: ?>
                        <span class="px-3 py-2 text-xs bg-indigo-100 text-indigo-700 rounded-lg">Utama</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div id="linksContainer" class="space-y-3"></div>
                <button type="button" onclick="addLinkField()" 
                        class="mt-3 px-4 py-2 text-sm text-indigo-600 border border-indigo-300 rounded-lg hover:bg-indigo-50 transition">
                    + Tambah Link Baru
                </button>
            </div>
            
            <!-- File Pendukung -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">File Pendukung</label>
                
                <?php
                $docs = array_filter($files, function($f) {
                    return strpos($f['file_path'], 'files') !== false;
                });
                if (!empty($docs)):
                ?>
                <div class="space-y-2 mb-3">
                    <?php foreach ($docs as $doc): ?>
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg" data-file-id="<?php echo $doc['id_file']; ?>">
                        <div>
                            <p class="text-sm font-medium"><?php echo htmlspecialchars($doc['label']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($doc['nama_file']); ?></p>
                        </div>
                        <button type="button" onclick="deleteFile(<?php echo $doc['id_file']; ?>)"
                                class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div id="filesContainer" class="space-y-3"></div>
                <button type="button" onclick="addFileField()" 
                        class="mt-3 px-4 py-2 text-sm text-indigo-600 border border-indigo-300 rounded-lg hover:bg-indigo-50 transition">
                    + Tambah File Baru
                </button>
            </div>
            
            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="kelola_karya.php" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft"
                        class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="publish"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    Update & Publish
                </button>
            </div>
            
        </form>
        
    </div>
</div>

<script>
let selectedFiles = [];
let filesToDelete = []; // Track files marked for deletion
let linksToDelete = []; // Track links marked for deletion

document.getElementById('snapshotInput').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    
    files.forEach(file => {
        if (!file.type.startsWith('image/')) {
            alert(file.name + ' bukan file gambar!');
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            alert(file.name + ' terlalu besar (max 2MB)!');
            return;
        }
        
        selectedFiles.push(file);
    });
    
    renderSnapshotPreviews();
    e.target.value = '';
});

function renderSnapshotPreviews() {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border-2 border-green-300">
                <div class="absolute top-2 right-2 flex gap-1">
                    <span class="bg-green-600 text-white text-xs px-2 py-1 rounded">New ${index + 1}</span>
                    <button type="button" onclick="removeSnapshot(${index})" 
                            class="bg-red-600 text-white p-1 rounded hover:bg-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
    
    updateFormData();
}

function removeSnapshot(index) {
    selectedFiles.splice(index, 1);
    renderSnapshotPreviews();
}

function updateFormData() {
    const oldInputs = document.querySelectorAll('input[name="snapshots[]"]');
    oldInputs.forEach(input => input.remove());
    
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    
    const newInput = document.createElement('input');
    newInput.type = 'file';
    newInput.name = 'snapshots[]';
    newInput.multiple = true;
    newInput.className = 'hidden';
    newInput.files = dt.files;
    
    document.querySelector('form').appendChild(newInput);
}

function addLinkField() {
    const container = document.getElementById('linksContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-start';
    div.innerHTML = `
        <input type="text" name="link_label[]" placeholder="Label" 
               class="w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        <input type="url" name="link_url[]" placeholder="https://..." 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        <button type="button" onclick="this.parentElement.remove()" 
                class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function addFileField() {
    const container = document.getElementById('filesContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-start';
    div.innerHTML = `
        <input type="text" name="file_label[]" placeholder="Label" 
               class="w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        <input type="file" name="file_upload[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        <button type="button" onclick="this.parentElement.remove()" 
                class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(div);
}

// NEW: Staged deletion for files (snapshots & documents)
function deleteFile(id) {
    if (confirm('Tandai file untuk dihapus?\n\nFile akan dihapus dari database saat Anda klik "Update".')) {
        // Hide from UI
        const fileElement = document.querySelector(`[data-file-id="${id}"]`);
        if (fileElement) {
            // Add "marked for deletion" overlay
            fileElement.style.position = 'relative';
            fileElement.style.opacity = '0.4';
            
            const overlay = document.createElement('div');
            overlay.className = 'absolute inset-0 bg-red-500 bg-opacity-20 flex items-center justify-center rounded-lg';
            overlay.innerHTML = `
                <div class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-2">
                    <span>Akan Dihapus</span>
                    <button type="button" onclick="undoDeleteFile(${id})" class="bg-white text-red-600 px-2 py-0.5 rounded hover:bg-gray-100">
                        Undo
                    </button>
                </div>
            `;
            fileElement.appendChild(overlay);
            
            // Add to deletion queue
            if (!filesToDelete.includes(id)) {
                filesToDelete.push(id);
                updateDeleteInputs();
            }
        }
    }
}

function undoDeleteFile(id) {
    // Remove from deletion queue
    const index = filesToDelete.indexOf(id);
    if (index > -1) {
        filesToDelete.splice(index, 1);
        updateDeleteInputs();
    }
    
    // Restore UI
    const fileElement = document.querySelector(`[data-file-id="${id}"]`);
    if (fileElement) {
        fileElement.style.opacity = '1';
        const overlay = fileElement.querySelector('.absolute');
        if (overlay) overlay.remove();
    }
}

// NEW: Staged deletion for links
function deleteLink(id) {
    if (confirm('Tandai link untuk dihapus?\n\nLink akan dihapus dari database saat Anda klik "Update".')) {
        const linkElement = document.querySelector(`[data-link-id="${id}"]`);
        if (linkElement) {
            linkElement.style.position = 'relative';
            linkElement.style.opacity = '0.4';
            linkElement.classList.add('bg-red-50');
            
            const overlay = document.createElement('div');
            overlay.className = 'absolute inset-0 flex items-center justify-center';
            overlay.innerHTML = `
                <div class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-2">
                    <span>Akan Dihapus</span>
                    <button type="button" onclick="undoDeleteLink(${id})" class="bg-white text-red-600 px-2 py-0.5 rounded hover:bg-gray-100">
                        Undo
                    </button>
                </div>
            `;
            linkElement.appendChild(overlay);
            
            if (!linksToDelete.includes(id)) {
                linksToDelete.push(id);
                updateDeleteInputs();
            }
        }
    }
}

function undoDeleteLink(id) {
    const index = linksToDelete.indexOf(id);
    if (index > -1) {
        linksToDelete.splice(index, 1);
        updateDeleteInputs();
    }
    
    const linkElement = document.querySelector(`[data-link-id="${id}"]`);
    if (linkElement) {
        linkElement.style.opacity = '1';
        linkElement.classList.remove('bg-red-50');
        const overlay = linkElement.querySelector('.absolute');
        if (overlay) overlay.remove();
    }
}

// Update hidden inputs for deletions
function updateDeleteInputs() {
    // Remove old deletion inputs
    document.querySelectorAll('input[name="delete_files[]"]').forEach(el => el.remove());
    document.querySelectorAll('input[name="delete_links[]"]').forEach(el => el.remove());
    
    // Add new deletion inputs
    const form = document.querySelector('form');
    
    filesToDelete.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_files[]';
        input.value = id;
        form.appendChild(input);
    });
    
    linksToDelete.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_links[]';
        input.value = id;
        form.appendChild(input);
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer_admin.php'; ?>