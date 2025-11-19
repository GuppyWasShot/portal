<?php
/**
 * Debug Fungsi: CRUD Kategori
 * Akses: /debug/functions/kategori_debug.php
 * Membantu menelusuri kegagalan operasi kategori dengan menampilkan pesan error SQL secara langsung.
 *
 * ⚠️ Jangan gunakan di environment produksi karena akan menampilkan pesan error mentah.
 */

require_once __DIR__ . '/../../app/autoload.php';

$db = Database::getInstance()->getConnection();
$feedback = '';
$last_query = '';

function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
if ($action === 'create') {
            $nama = trim($_POST['nama_kategori'] ?? '');
            $warna = strtoupper(trim($_POST['warna_hex'] ?? '#6366F1'));
            $last_query = "INSERT INTO tbl_category (nama_kategori, warna_hex) VALUES ('$nama', '$warna')";

            if (empty($nama)) {
                throw new Exception('Nama kategori wajib diisi.');
            }

            if (!preg_match('/^#[0-9A-F]{6}$/', $warna)) {
                throw new Exception('Format warna tidak valid.');
            }

            $stmt = $db->prepare("INSERT INTO tbl_category (nama_kategori, warna_hex) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama, $warna);
            $stmt->execute();
            $stmt->close();

            $feedback = '✅ Insert kategori berhasil.';
        } elseif ($action === 'update') {
            $id = intval($_POST['id_kategori'] ?? 0);
            $nama = trim($_POST['nama_kategori'] ?? '');
            $warna = strtoupper(trim($_POST['warna_hex'] ?? '#6366F1'));
            $last_query = "UPDATE tbl_category SET nama_kategori='$nama', warna_hex='$warna' WHERE id_kategori=$id";

            if ($id <= 0) {
                throw new Exception('ID kategori tidak valid.');
            }
            if (empty($nama)) {
                throw new Exception('Nama kategori wajib diisi.');
            }
            if (!preg_match('/^#[0-9A-F]{6}$/', $warna)) {
                throw new Exception('Format warna tidak valid.');
            }

            $stmt = $db->prepare("UPDATE tbl_category SET nama_kategori = ?, warna_hex = ? WHERE id_kategori = ?");
            $stmt->bind_param("ssi", $nama, $warna, $id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Tidak ada baris yang diperbarui. Pastikan ID benar.');
            }

            $stmt->close();
            $feedback = '✅ Update kategori berhasil.';
        } elseif ($action === 'delete') {
            $id = intval($_POST['id_kategori'] ?? 0);
            $last_query = "DELETE FROM tbl_category WHERE id_kategori=$id";

            if ($id <= 0) {
                throw new Exception('ID kategori tidak valid.');
            }

            $stmt = $db->prepare("DELETE FROM tbl_project_category WHERE id_kategori = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $db->prepare("DELETE FROM tbl_category WHERE id_kategori = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Kategori tidak ditemukan atau sudah dihapus.');
            }

            $stmt->close();
            $feedback = '✅ Delete kategori berhasil.';
        } else {
            $feedback = '❌ Action tidak dikenali.';
        }
    } catch (Throwable $e) {
        $feedback = '❌ Error: ' . sanitize($e->getMessage());

        if ($db->error) {
            $feedback .= '<br>❌ SQL Error: ' . sanitize($db->error);
        }

        if (!empty($last_query)) {
            $feedback .= '<br><small>Query Terakhir: ' . sanitize($last_query) . '</small>';
        }
    }
}

$kategori_result = $db->query("SELECT c.*, COUNT(pc.id_project) as total_karya 
                               FROM tbl_category c
                               LEFT JOIN tbl_project_category pc ON c.id_kategori = pc.id_kategori
                               GROUP BY c.id_kategori
                               ORDER BY c.nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Debug CRUD Kategori</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body { background: #f3f4f6; }
        code { background: #e5e7eb; padding: 2px 4px; border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen py-10">
    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-2xl p-8 space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Debug CRUD Kategori</h1>
            <p class="text-gray-500 mt-1 text-sm">Gunakan halaman ini untuk melihat pesan error detail jika operasi kategori gagal.</p>
            <p class="text-red-500 text-xs mt-1">⚠️ Jangan gunakan di produksi — halaman ini menampilkan pesan error SQL mentah.</p>
        </div>

        <?php if (!empty($feedback)): ?>
            <div class="px-4 py-3 rounded-lg <?php echo strpos($feedback, '✅') !== false ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
                <?php echo $feedback; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <form method="POST" class="bg-gray-50 rounded-xl p-6 space-y-4 border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Tambah Kategori (Create)</h2>
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna HEX</label>
                    <input type="text" name="warna_hex" value="#6366F1" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition">Tes Insert</button>
            </form>

            <form method="POST" class="bg-gray-50 rounded-xl p-6 space-y-4 border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Update / Delete (Debug)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <input type="hidden" name="action" value="update" id="updateAction">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Kategori</label>
                        <input type="number" name="id_kategori" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Baru</label>
                        <input type="text" name="nama_kategori" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="Opsional">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna Baru</label>
                        <input type="text" name="warna_hex" value="#6366F1" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" onclick="document.getElementById('updateAction').value='update';" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded-lg transition">Tes Update</button>
                    <button type="submit" onclick="document.getElementById('updateAction').value='delete';" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition" name="action" value="delete">Tes Delete</button>
                </div>
            </form>
        </div>

        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 overflow-x-auto">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Snapshot Data `tbl_category`</h2>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-white">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Warna</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Total Karya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($kategori_result && $kategori_result->num_rows > 0): ?>
                        <?php while ($kat = $kategori_result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-4 py-2 font-mono text-gray-700"><?php echo sanitize($kat['id_kategori']); ?></td>
                                <td class="px-4 py-2 text-gray-800"><?php echo sanitize($kat['nama_kategori']); ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full border" style="background-color: <?php echo sanitize($kat['warna_hex']); ?>"></span>
                                        <code><?php echo sanitize($kat['warna_hex']); ?></code>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-700"><?php echo sanitize($kat['total_karya']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada data kategori.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

