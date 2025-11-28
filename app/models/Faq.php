<?php
/**
 * Kelas FAQ (Pertanyaan yang Sering Ditanya)
 * Buat ngatur FAQ di halaman publik
 * 
 * Cara pake:
 * $faq = new Faq();
 * $list = $faq->getAll();
 */
require_once __DIR__ . '/Database.php';

class Faq {
    private $db;
    
    /**
     * Constructor - bikin object FAQ
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Ambil semua FAQ yang aktif
     * 
     * @param array $filters Filter opsional (status, order)
     * @return array List FAQ
     */
    public function getAll($filters = []) {
        $where_conditions = [];
        $params = [];
        $types = "";
        
        // Filter by status
        if (isset($filters['status'])) {
            $where_conditions[] = "status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        // Filter by kategori
        if (isset($filters['kategori'])) {
            $where_conditions[] = "kategori = ?";
            $params[] = $filters['kategori'];
            $types .= "s";
        }
        
        // Build WHERE clause
        $where_clause = !empty($where_conditions) 
            ? "WHERE " . implode(' AND ', $where_conditions)
            : "";
        
        // Order clause
        $order = isset($filters['order']) ? $filters['order'] : 'status DESC, urutan ASC, created_at DESC';
        
        $query = "SELECT * FROM tbl_faq $where_clause ORDER BY $order";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        $faq_list = [];
        while ($row = $result->fetch_assoc()) {
            $faq_list[] = $row;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        return $faq_list;
    }
    
    /**
     * Mendapatkan FAQ aktif (untuk tampilan public)
     * 
     * @return array Array of active FAQ
     */
    public function getActive() {
        return $this->getAll(['status' => 'active']);
    }
    
    /**
     * Ambil FAQ berdasarkan ID
     * 
     * @param int $id ID FAQ
     * @return array|null Data FAQ atau null kalo ga ada
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_faq WHERE id_faq = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $faq = $result->fetch_assoc();
        $stmt->close();
        
        return $faq;
    }
    
    /**
     * Bikin FAQ baru
     * 
     * @param array $data Data FAQ (question, answer, urutan, status)
     * @return int|false ID FAQ baru kalo berhasil
     */
    public function create($data) {
        // Validasi required fields
        if (empty($data['pertanyaan']) || empty($data['jawaban'])) {
            return false;
        }
        
        $pertanyaan = $data['pertanyaan'];
        $jawaban = $data['jawaban'];
        $kategori = $data['kategori'] ?? null;
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_faq (pertanyaan, jawaban, kategori, urutan, status) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssis", $pertanyaan, $jawaban, $kategori, $urutan, $status);
            $success = $stmt->execute();
            
            if ($success) {
                $insert_id = $stmt->insert_id;
                $stmt->close();
                return $insert_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Faq::create() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update FAQ
     * 
     * @param int $id ID FAQ yang mau diupdate
     * @param array $data Data baru
     * @return bool True kalo berhasil
     */
    public function update($id, $data) {
        if ($id <= 0) {
            return false;
        }
        
        // Validasi required fields
        if (empty($data['pertanyaan']) || empty($data['jawaban'])) {
            return false;
        }
        
        $pertanyaan = $data['pertanyaan'];
        $jawaban = $data['jawaban'];
        $kategori = $data['kategori'] ?? null;
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE tbl_faq 
                 SET pertanyaan = ?, jawaban = ?, kategori = ?, urutan = ?, status = ?
                 WHERE id_faq = ?"
            );
            $stmt->bind_param("sssisi", $pertanyaan, $jawaban, $kategori, $urutan, $status, $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Faq::update() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus FAQ
     * 
     * @param int $id ID FAQ
     * @return bool True jika berhasil
     */
    public function delete($id) {
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_faq WHERE id_faq = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Faq::delete() error: " . $e->getMessage());
            return false;
        }
    }
}
