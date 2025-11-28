<?php
/**
 * Kelas Matkul (Mata Kuliah)
 * Buat ngatur data mata kuliah TPL
 * 
 * Cara pake:
 * $matkul = new Matkul();
 * $list = $matkul->getAll();
 */
require_once __DIR__ . '/Database.php';

class Matkul {
    private $db;
    
    /**
     * Konstruktor - bikin objek Matkul
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Ambil semua mata kuliah (bisa pake filter)
     * 
     * @param array $filters Filter opsional (status, semester, order)
     * @return array List matkul
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
            $params[] = intval($filters['semester']);
            $types .= "i";
        }
        
        // Buat klausa WHERE
        $where_clause = !empty($where_conditions) 
            ? "WHERE " . implode(' AND ', $where_conditions)
            : "";
        
        // Klausa ORDER BY
        $order = isset($filters['order']) ? $filters['order'] : 'semester ASC, urutan ASC, nama ASC';
        
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
     * Ambil mata kuliah yang dikelompokkan berdasarkan semester
     * 
     * @return array Array dengan key semester, value list matkul
     */
    public function getAllGroupedBySemester() {
        $all_matkul = $this->getAll();
        
        $grouped = [];
        foreach ($all_matkul as $matkul) {
            $semester = $matkul['semester'];
            if (!isset($grouped[$semester])) {
                $grouped[$semester] = [];
            }
            $grouped[$semester][] = $matkul;
        }
        
        // Urutkan berdasarkan semester
        ksort($grouped);
        
        return $grouped;
    }
    
    /**
     * Ambil matkul berdasarkan ID
     * 
     * @param int $id ID matkul
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
     * Ambil semua mata kuliah dalam semester tertentu
     * 
     * @param int $semester Semester (1-8)
     * @return array List matkul
     */
    public function getBySemester($semester) {
        return $this->getAll(['semester' => $semester]);
    }
    
    /**
     * Hitung total jumlah mata kuliah
     * 
     * @return int Total mata kuliah
     */
    public function getTotalCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM tbl_matkul");
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }
    
    /**
     * Bikin matkul baru
     * 
     * @param array $data Data matkul (kode, nama, semester, sks, kategori, deskripsi, urutan, status)
     * @return int|false ID matkul baru kalo berhasil
     */
    public function create($data) {
        // Validasi field wajib
        if (empty($data['kode']) || empty($data['nama']) || !isset($data['semester'])) {
            return false;
        }
        
        $kode = $data['kode'];
        $nama = $data['nama'];
        $semester = intval($data['semester']);
        $sks = isset($data['sks']) ? intval($data['sks']) : 0;
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
     * Update mata kuliah
     * 
     * @param int $id ID matkul
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil
     */
    public function update($id, $data) {
        if ($id <= 0) {
            return false;
        }
        
        // Validasi field wajib
        if (empty($data['kode']) || empty($data['nama']) || !isset($data['semester'])) {
            return false;
        }
        
        $kode = $data['kode'];
        $nama = $data['nama'];
        $semester = intval($data['semester']);
        $sks = isset($data['sks']) ? intval($data['sks']) : 0;
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
     * Hapus mata kuliah
     * 
     * @param int $id ID matkul
     * @return bool True jika berhasil
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
}
