<?php
/**
 * Kelas Karya (Project Mahasiswa)
 * Buat ngatur semua karya/project - ini model paling kompleks!
 * 
 * Cara pake:
 * $karya = new Karya();
 * $detail = $karya->getKaryaById(1);
 */
require_once __DIR__ . '/Database.php';

class Karya {
    private $db;
    
    /**
     * Constructor - bikin object Karya
     * Pake dependency injection buat koneksi database
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Ambil detail karya berdasarkan ID (buat halaman detail)
     * 
     * @param int $id ID project
     * @return array|null Data karya atau null kalo ga ada
     */
    public function getKaryaById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
            GROUP_CONCAT(DISTINCT c.nama_kategori ORDER BY c.nama_kategori SEPARATOR ', ') as kategori,
            GROUP_CONCAT(DISTINCT c.warna_hex ORDER BY c.nama_kategori SEPARATOR ',') as warna,
            AVG(r.skor) as avg_rating,
            COUNT(DISTINCT r.id_rating) as total_rating
            FROM tbl_project p
            LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
            LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
            LEFT JOIN tbl_rating r ON p.id_project = r.id_project
            WHERE p.id_project = ? AND p.status = 'Published'
            GROUP BY p.id_project
        ");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $karya = $result->fetch_assoc();
        $stmt->close();
        
        return $karya;
    }
    
    /**
     * Ambil semua link dari karya tertentu
     * 
     * @param int $project_id ID project
     * @return array List link (GitHub, demo, dll)
     */
    public function getLinks($project_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM tbl_project_links 
            WHERE id_project = ? 
            ORDER BY is_primary DESC
        ");
        
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $links = [];
        while ($row = $result->fetch_assoc()) {
            $links[] = $row;
        }
        
        $stmt->close();
        return $links;
    }
    
    /**
     * Ambil semua file dari karya (snapshot + dokumen)
     * 
     * @param int $project_id ID project
     * @return array List file
     */
    public function getFiles($project_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM tbl_project_files 
            WHERE id_project = ? 
            ORDER BY id_file ASC
        ");
        
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $files = [];
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }
        
        $stmt->close();
        return $files;
    }
    
    /**
     * Pisahin file jadi 2 kategori: snapshot sama dokumen
     * 
     * @param array $files List file mentah
     * @return array Array dengan key 'snapshots' dan 'documents'
     */
    public function separateFiles($files) {
        $snapshots = array_filter($files, function($f) {
            return strpos($f['file_path'], 'snapshots') !== false;
        });
        
        $documents = array_filter($files, function($f) {
            return strpos($f['file_path'], 'files') !== false;
        });
        
        return [
            'snapshots' => array_values($snapshots),
            'documents' => array_values($documents)
        ];
    }
    
    /**
     * Ambil semua karya dengan filter (buat halaman galeri publik)
     * 
     * @param array $filters Filter: search, sort, kategori
     * @return array List karya yang dipublish
     */
    public function getAllKarya($filters = []) {
        $search = isset($filters['search']) ? trim($filters['search']) : '';
        $sort = isset($filters['sort']) ? $filters['sort'] : 'terbaru';
        $kategori_filter = isset($filters['kategori']) ? $filters['kategori'] : [];
        
        // Bikin kondisi WHERE buat query
        $where_conditions = ["p.status = 'Published'"];
        $params = [];
        $types = "";
        
        // Filter buat search/pencarian
        if (!empty($search)) {
            $where_conditions[] = "(p.judul LIKE ? OR p.pembuat LIKE ? OR p.deskripsi LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "sss";
        }
        
        // Filter berdasarkan kategori
        if (!empty($kategori_filter) && is_array($kategori_filter)) {
            $placeholders = implode(',', array_fill(0, count($kategori_filter), '?'));
            $where_conditions[] = "pc.id_kategori IN ($placeholders)";
            foreach ($kategori_filter as $kat_id) {
                $params[] = intval($kat_id);
                $types .= "i";
            }
        }
        
        // Urutan sorting
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
        
        $query = "SELECT p.*, 
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
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        $karya_list = [];
        while ($row = $result->fetch_assoc()) {
            $karya_list[] = $row;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        return $karya_list;
    }
    
    /**
     * Ambil karya dengan filter + pagination (buat  galeri yang banyak)
     * 
     * @param array $filters Filter: search, sort, kategori
     * @param int $page Halaman sekarang (mulai dari 1)
     * @param int $per_page Jumlah item per halaman
     * @return array Array: data, total, total_pages, current_page
     */
    public function getAllKaryaPaginated($filters = [], $page = 1, $per_page = 12) {
        $search = isset($filters['search']) ? trim($filters['search']) : '';
        $sort = isset($filters['sort']) ? $filters['sort'] : 'terbaru';
        $kategori_filter = isset($filters['kategori']) ? $filters['kategori'] : [];
        
        // Bikin kondisi WHERE
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
        
        // Filter kategori - project harus punya SEMUA kategori yang dipilih (logika AND)
        if (!empty($kategori_filter) && is_array($kategori_filter)) {
            $count_needed = count($kategori_filter);
            $placeholders = implode(',', array_fill(0, $count_needed, '?'));
            // Cek project punya semua kategori dengan hitung jumlah match
            $where_conditions[] = "(
                SELECT COUNT(DISTINCT pc_filter.id_kategori) 
                FROM tbl_project_category pc_filter 
                WHERE pc_filter.id_project = p.id_project 
                AND pc_filter.id_kategori IN ($placeholders)
            ) = $count_needed";
            foreach ($kategori_filter as $kat_id) {
                $params[] = intval($kat_id);
                $types .= "i";
            }
        }
        
        // Urutan sorting
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
        
        // Query buat hitung total data
        $count_query = "SELECT COUNT(DISTINCT p.id_project) as total
                FROM tbl_project p
                WHERE $where_clause";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($count_query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $count_result = $stmt->get_result();
            $total = $count_result->fetch_assoc()['total'];
            $stmt->close();
        } else {
            $count_result = $this->db->query($count_query);
            $total = $count_result->fetch_assoc()['total'];
        }
        
        // Query buat ambil data dengan pagination
        $offset = ($page - 1) * $per_page;
        $query = "SELECT p.*, 
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
                ORDER BY $order_by
                LIMIT ? OFFSET ?";
        
        $params[] = $per_page;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $karya_list = [];
        while ($row = $result->fetch_assoc()) {
            $karya_list[] = $row;
        }
        $stmt->close();
        
        $total_pages = ceil($total / $per_page);
        
        return [
            'data' => $karya_list,
            'total' => $total,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'per_page' => $per_page
        ];
    }
    
    /**
     * Tambah karya baru (khusus admin)
     * 
     * @param array $data Data lengkap: judul, deskripsi, pembuat, tanggal_selesai, dll
     * @return int|false ID karya baru kalo berhasil
     */
    public function tambahKarya($data) {
        // Validasi field wajib
        if (empty($data['judul'])) {
            return false;
        }
        
        $judul = $data['judul'];
        $deskripsi = $data['deskripsi'] ?? null;
        $pembuat = $data['pembuat'] ?? null;
        $tanggal_selesai = $data['tanggal_selesai'] ?? null;
        $link_external = $data['link_external'] ?? null;
        $snapshot_url = $data['snapshot_url'] ?? null;
        $status = isset($data['status']) ? $data['status'] : 'Draft';
        
        // Validasi status
        if (!in_array($status, ['Draft', 'Published', 'Hidden'])) {
            $status = 'Draft';
        }
        
        try {
            // Insert data project
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_project (judul, deskripsi, pembuat, tanggal_selesai, link_external, snapshot_url, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssssss", $judul, $deskripsi, $pembuat, $tanggal_selesai, $link_external, $snapshot_url, $status);
            $success = $stmt->execute();
            
            if (!$success) {
                $stmt->close();
                return false;
            }
            
            $project_id = $stmt->insert_id;
            $stmt->close();
            
            // Insert kategori kalo ada
            if (!empty($data['categories']) && is_array($data['categories'])) {
                $this->updateCategories($project_id, $data['categories']);
            }
            
            return $project_id;
            
        } catch (Exception $e) {
            error_log("Karya::tambahKarya() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update karya (khusus admin)
     * 
     * @param int $id ID karya yang mau diupdate
     * @param array $data Data baru
     * @return bool True kalo berhasil
     */
    public function updateKarya($id, $data) {
        if ($id <= 0) {
            return false;
        }
        
        // Validasi field wajib
        if (empty($data['judul'])) {
            return false;
        }
        
        $judul = $data['judul'];
        $deskripsi = $data['deskripsi'] ?? null;
        $pembuat = $data['pembuat'] ?? null;
        $tanggal_selesai = $data['tanggal_selesai'] ?? null;
        $link_external = $data['link_external'] ?? null;
        $status = isset($data['status']) ? $data['status'] : 'Draft';
        
        // Validasi status
        if (!in_array($status, ['Draft', 'Published', 'Hidden'])) {
            $status = 'Draft';
        }
        
        try {
            // Cek apakah snapshot_url mau diupdate juga
            if (isset($data['snapshot_url'])) {
                $snapshot_url = $data['snapshot_url'];
                $stmt = $this->db->prepare(
                    "UPDATE tbl_project 
                     SET judul = ?, deskripsi = ?, pembuat = ?, tanggal_selesai = ?, link_external = ?, snapshot_url = ?, status = ?
                     WHERE id_project = ?"
                );
                $stmt->bind_param("sssssssi", $judul, $deskripsi, $pembuat, $tanggal_selesai, $link_external, $snapshot_url, $status, $id);
            } else {
                // Update tanpa ubah snapshot_url
                $stmt = $this->db->prepare(
                    "UPDATE tbl_project 
                     SET judul = ?, deskripsi = ?, pembuat = ?, tanggal_selesai = ?, link_external = ?, status = ?
                     WHERE id_project = ?"
                );
                $stmt->bind_param("ssssssi", $judul, $deskripsi, $pembuat, $tanggal_selesai, $link_external, $status, $id);
            }
            
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            // Update kategori kalo ada
            if (isset($data['categories']) && is_array($data['categories'])) {
                $this->updateCategories($id, $data['categories']);
            }
            
            return $success && $affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Karya::updateKarya() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus karya (khusus admin)
     * Note: Semua data relasi (kategori, file, link, rating) otomatis kehapus
     * karena pake ON DELETE CASCADE di database
     * 
     * @param int $id ID karya
     * @return bool True kalo berhasil
     */
    public function hapusKarya($id) {
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_project WHERE id_project = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Karya::hapusKarya() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ambil semua project buat admin dengan filter, sort, dan pagination
     * 
     * @param array $filters Filter opsional:
     *   - 'search': Cari di judul, pembuat, deskripsi
     *   - 'status': Filter berdasarkan status
     *   - 'sort': Urutan (terbaru, terlama, judul_asc, judul_desc, rating)
     *   - 'page': Halaman sekarang (default: 1)
     *   - 'per_page': Item per halaman (default: 15)
     * @return array Data: 'data', 'total', 'page', 'per_page', 'total_pages'
     */
    public function getAllForAdmin($filters = []) {
        $search = isset($filters['search']) ? trim($filters['search']) : '';
        $status = isset($filters['status']) ? $filters['status'] : null;
        $sort = isset($filters['sort']) ? $filters['sort'] : 'terbaru';
        $page = isset($filters['page']) ? max(1, intval($filters['page'])) : 1;
        $per_page = isset($filters['per_page']) ? max(1, intval($filters['per_page'])) : 15;
        
        // Bikin kondisi WHERE
        $where_conditions = [];
        $params = [];
        $types = "";
        
        if ($status !== null && $status !== '') {
            $where_conditions[] = "p.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        if (!empty($search)) {
            $where_conditions[] = "(p.judul LIKE ? OR p.pembuat LIKE ? OR p.deskripsi LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "sss";
        }
        
        $where_clause = !empty($where_conditions) ? "WHERE " . implode(' AND ', $where_conditions) : "";
        
        // Tentukan urutan sort
        $order_by = match($sort) {
            'judul_asc' => "p.judul ASC",
            'judul_desc' => "p.judul DESC",
            'terlama' => "p.tanggal_selesai ASC, p.id_project ASC",
            'rating' => "avg_rating DESC, p.id_project DESC",
            default => "p.id_project DESC" // terbaru
        };
        
        // Hitung total record
        $count_query = "SELECT COUNT(DISTINCT p.id_project) as total
                       FROM tbl_project p
                       LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
                       LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
                       LEFT JOIN tbl_rating r ON p.id_project = r.id_project
                       $where_clause";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($count_query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $total = $result->fetch_assoc()['total'];
            $stmt->close();
        } else {
            $result = $this->db->query($count_query);
            $total = $result->fetch_assoc()['total'];
        }
        
        // Hitung pagination
        $total_pages = ceil($total / $per_page);
        $offset = ($page - 1) * $per_page;
        
        // Ambil data dengan pagination
        $query = "SELECT p.*, 
                 GROUP_CONCAT(DISTINCT c.nama_kategori ORDER BY c.nama_kategori SEPARATOR ', ') as kategori,
                 GROUP_CONCAT(DISTINCT c.warna_hex ORDER BY c.nama_kategori SEPARATOR ',') as warna,
                 AVG(r.skor) as avg_rating,
                 COUNT(DISTINCT r.id_rating) as total_rating
                 FROM tbl_project p
                 LEFT JOIN tbl_project_category pc ON p.id_project = pc.id_project
                 LEFT JOIN tbl_category c ON pc.id_kategori = c.id_kategori
                 LEFT JOIN tbl_rating r ON p.id_project = r.id_project
                 $where_clause
                 GROUP BY p.id_project
                 ORDER BY $order_by
                 LIMIT ? OFFSET ?";
        
        $limit_params = $params;
        $limit_types = $types . "ii";
        $limit_params[] = $per_page;
        $limit_params[] = $offset;
        
        if (!empty($limit_params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($limit_types, ...$limit_params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages
        ];
    }
    
    /**
     * Update status karya aja (Draft/Published/Hidden)
     * 
     * @param int $id ID project
     * @param string $status Status baru (Draft, Published, Hidden)
     * @return bool True kalo berhasil
     */
    public function updateStatus($id, $status) {
        if ($id <= 0) {
            return false;
        }
        
        // Validasi status harus salah satu dari 3 pilihan
        if (!in_array($status, ['Draft', 'Published', 'Hidden'])) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE tbl_project SET status = ? WHERE id_project = ?");
            $stmt->bind_param("si", $status, $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Karya::updateStatus() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus file dari project (snapshot/dokumen)
     * 
     * @param int $file_id ID file yang mau dihapus
     * @return bool True kalo berhasil
     */
    public function deleteFile($file_id) {
        if ($file_id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_project_files WHERE id_file = ?");
            $stmt->bind_param("i", $file_id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Karya::deleteFile() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus link dari project (GitHub, demo, dll)
     * 
     * @param int $link_id ID link yang mau dihapus
     * @return bool True kalo berhasil
     */
    public function deleteLink($link_id) {
        if ($link_id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_project_links WHERE id_link = ?");
            $stmt->bind_param("i", $link_id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Karya::deleteLink() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tambah file baru ke project
     * 
     * @param int $project_id ID project tujuan
     * @param array $file_data Data file: label, nama_file, file_path, file_size, mime_type
     * @return int|false ID file baru kalo berhasil
     */
    public function addFile($project_id, $file_data) {
        if ($project_id <= 0 || empty($file_data['file_path'])) {
            return false;
        }
        
        $label = $file_data['label'] ?? 'File';
        $nama_file = $file_data['nama_file'] ?? '';
        $file_path = $file_data['file_path'];
        $file_size = $file_data['file_size'] ?? null;
        $mime_type = $file_data['mime_type'] ?? null;
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_project_files (id_project, label, nama_file, file_path, file_size, mime_type) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("isssis", $project_id, $label, $nama_file, $file_path, $file_size, $mime_type);
            $success = $stmt->execute();
            
            if ($success) {
                $file_id = $stmt->insert_id;
                $stmt->close();
                return $file_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Karya::addFile() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tambah link baru ke project
     * 
     * @param int $project_id ID project tujuan
     * @param array $link_data Data link: label, url, is_primary
     * @return int|false ID link baru kalo berhasil
     */
    public function addLink($project_id, $link_data) {
        if ($project_id <= 0 || empty($link_data['url'])) {
            return false;
        }
        
        $label = $link_data['label'] ?? 'Link';
        $url = $link_data['url'];
        $is_primary = isset($link_data['is_primary']) ? intval($link_data['is_primary']) : 0;
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_project_links (id_project, label, url, is_primary) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("issi", $project_id, $label, $url, $is_primary);
            $success = $stmt->execute();
            
            if ($success) {
                $link_id = $stmt->insert_id;
                $stmt->close();
                return $link_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Karya::addLink() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update kategori project (hapus semua lama, insert baru)
     * 
     * @param int $project_id ID project
     * @param array $category_ids List ID kategori
     * @return bool True kalo berhasil
     */
    public function updateCategories($project_id, $category_ids) {
        if ($project_id <= 0) {
            return false;
        }
        
        try {
            // Hapus semua kategori yang lama dulu
            $stmt = $this->db->prepare("DELETE FROM tbl_project_category WHERE id_project = ?");
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $stmt->close();
            
            // Insert kategori yang baru
            if (!empty($category_ids) && is_array($category_ids)) {
                $stmt = $this->db->prepare(
                    "INSERT INTO tbl_project_category (id_project, id_kategori) VALUES (?, ?)"
                );
                
                foreach ($category_ids as $category_id) {
                    $cat_id = intval($category_id);
                    if ($cat_id > 0) {
                        $stmt->bind_param("ii", $project_id, $cat_id);
                        $stmt->execute();
                    }
                }
                
                $stmt->close();
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Karya::updateCategories() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mendapatkan file tertentu berdasarkan ID
     * 
     * @param int $file_id ID file
     * @return array|null Data file atau null
     */
    public function getFileById($file_id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_project_files WHERE id_file = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();
        $stmt->close();
        
        return $file;
    }
    
    /**
     * Mendapatkan link tertentu berdasarkan ID
     * 
     * @param int $link_id ID link
     * @return array|null Data link atau null
     */
    public function getLinkById($link_id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_project_links WHERE id_link = ?");
        $stmt->bind_param("i", $link_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $link = $result->fetch_assoc();
        $stmt->close();
        
        return $link;
    }
}

