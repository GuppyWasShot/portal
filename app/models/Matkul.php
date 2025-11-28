<?php
/**
 * Class Matkul - Model buat ngatur data mata kuliah
 * Semua operasi CRUD buat matakuliah ada disini
 * 
 * Cara pake:
 * $matkul = new Matkul();
 * $list = $matkul->getAll();
 */
require_once __DIR__ . '/Database.php';

class Matkul {
    private $db;
    
    /**
     * Konstruktor - inisialisasi koneksi database
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Ambil semua data matkul, bisa pake filter juga
     * 
     * @param array $filters Filter opsional (status, semester, order)
     * @return array List data matakuliah
     */
    public function getAll($filters = []) {
        $where_conditions = [];
        $params = [];
        $types = "";
        
        // Filter berdasarkan status
        if (isset($filters['status'])) {
            $where_conditions[] = "status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        // Filter berdasarkan semester
        if (isset($filters['semester'])) {
            $where_conditions[] = "semester = ?";
            $params[] = $filters['semester'];
            $types .= "i";
        }
        
        // Bikin WHERE clause
        $where_clause = !empty($where_conditions) 
            ? "WHERE " . implode(' AND ', $where_conditions)
            : "";
        
        // Urutan data (default: semester asc, urutan asc)
        $order = isset($filters['order']) ? $filters['order'] : 'semester ASC, COALESCE(NULLIF(urutan, 0), 9999) ASC, nama ASC';
        
        $query = "SELECT * FROM tbl_matkul $where_clause ORDER BY $order";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        $matkul_list = [];
        while ($row = $result->fetch_assoc()) {
            $matkul_list[] = $row;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        return $matkul_list;
    }
    
    /**
     * Ambil data matkul by ID
     * 
     * @param int $id ID matakuliah
     * @return array|null Data matkul atau null kalo ga ketemu
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_matkul WHERE id_matkul = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $matkul = $result->fetch_assoc();
        $stmt->close();
        
        return $matkul;
    }
    
    /**
     * Hitung jumlah matkul aktif (buat dashboard)
     * 
     * @return int Jumlah matkul yang statusnya active
     */
    public function getActiveCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM tbl_matkul WHERE status = 'active'");
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }
    
    /**
     * Bikin matkul baru
     * 
     * @param array $data Data matkul (kode, nama, semester wajib; sisanya opsional)
     * @return int|false ID matkul baru atau false kalo gagal
     */
    public function create($data) {
        // Validasi: kode, nama, semester wajib ada
        if (empty($data['kode']) || empty($data['nama']) || empty($data['semester'])) {
            return false;
        }
        
        // Set nilai default kalo ga diisi
        $kode = $data['kode'];
        $nama = $data['nama'];
        $sks = isset($data['sks']) ? intval($data['sks']) : 0;
        $semester = intval($data['semester']);
        $kategori = $data['kategori'] ?? null;
        $deskripsi = $data['deskripsi'] ?? null;
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_matkul (kode, nama, sks, semester, kategori, deskripsi, urutan, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssiissis", $kode, $nama, $sks, $semester, $kategori, $deskripsi, $urutan, $status);
            $success = $stmt->execute();
            
            if ($success) {
                $insert_id = $stmt->insert_id;
                $stmt->close();
                return $insert_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Matkul::create() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update data matkul
     * 
     * @param int $id ID matkul yang mau diupdate
     * @param array $data Data baru
     * @return bool True kalo berhasil
     */
    public function update($id, $data) {
        // Validasi ID
        if ($id <= 0) {
            return false;
        }
        
        // Kode, nama, semester wajib ada
        if (empty($data['kode']) || empty($data['nama']) || empty($data['semester'])) {
            return false;
        }
        
        $kode = $data['kode'];
        $nama = $data['nama'];
        $sks = isset($data['sks']) ? intval($data['sks']) : 0;
        $semester = intval($data['semester']);
        $kategori = $data['kategori'] ?? null;
        $deskripsi = $data['deskripsi'] ?? null;
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE tbl_matkul 
                 SET kode = ?, nama = ?, sks = ?, semester = ?, kategori = ?, deskripsi = ?, urutan = ?, status = ?
                 WHERE id_matkul = ?"
            );
            $stmt->bind_param("ssiissisi", $kode, $nama, $sks, $semester, $kategori, $deskripsi, $urutan, $status, $id);
            
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Matkul::update() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus data matkul
     * 
     * @param int $id ID matkul yang mau dihapus
     * @return bool True kalo berhasil
     */
    public function delete($id) {
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_matkul WHERE id_matkul = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Matkul::delete() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ambil matkul berdasarkan semester tertentu
     * 
     * @param int $semester Semester (1-8)
     * @return array List matkul di semester itu
     */
    public function getBySemester($semester) {
        return $this->getAll(['semester' => $semester, 'status' => 'active']);
    }
}
