<?php
/**
 * Category Class
 * Menangani semua operasi terkait Kategori Project
 * 
 * Usage:
 * $category = new Category();
 * $list = $category->getAll();
 */
require_once __DIR__ . '/Database.php';

class Category {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Mendapatkan semua kategori
     * 
     * @param array $filters Optional filters (order)
     * @return array Array of categories
     */
    public function getAll($filters = []) {
        $order = isset($filters['order']) ? $filters['order'] : 'nama_kategori ASC';
        
        $query = "SELECT * FROM tbl_category ORDER BY $order";
        $result = $this->db->query($query);
        
        $category_list = [];
        while ($row = $result->fetch_assoc()) {
            $category_list[] = $row;
        }
        
        return $category_list;
    }
    
    /**
     * Mendapatkan kategori berdasarkan ID
     * 
     * @param int $id ID kategori
     * @return array|null Data kategori atau null
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_category WHERE id_kategori = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        
        return $category;
    }
    
    /**
     * Cek apakah kategori sedang digunakan oleh project
     * 
     * @param int $id ID kategori
     * @return bool True jika kategori digunakan
     */
    public function isInUse($id) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM tbl_project_category WHERE id_kategori = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int)$row['count'] > 0;
    }
    
    /**
     * Validasi format warna hex
     * 
     * @param string $hex Warna hex
     * @return bool True jika valid
     */
    private function isValidHexColor($hex) {
        return preg_match('/^#[0-9A-F]{6}$/i', $hex);
    }
    
    /**
     * Membuat kategori baru
     * 
     * @param array $data Data kategori (nama_kategori, warna_hex)
     * @return int|false ID kategori baru atau false jika gagal
     */
    public function create($data) {
        // Validasi required fields
        if (empty($data['nama_kategori'])) {
            return false;
        }
        
        $nama_kategori = $data['nama_kategori'];
        $warna_hex = isset($data['warna_hex']) ? strtoupper($data['warna_hex']) : '#6366F1';
        
        // Validasi warna hex
        if (!$this->isValidHexColor($warna_hex)) {
            error_log("Category::create() - Invalid hex color: $warna_hex");
            return false;
        }
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_category (nama_kategori, warna_hex) VALUES (?, ?)"
            );
            $stmt->bind_param("ss", $nama_kategori, $warna_hex);
            $success = $stmt->execute();
            
            if ($success) {
                $insert_id = $stmt->insert_id;
                $stmt->close();
                return $insert_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Category::create() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update kategori
     * 
     * @param int $id ID kategori
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil
     */
    public function update($id, $data) {
        if ($id <= 0) {
            return false;
        }
        
        // Validasi required fields
        if (empty($data['nama_kategori'])) {
            return false;
        }
        
        $nama_kategori = $data['nama_kategori'];
        $warna_hex = isset($data['warna_hex']) ? strtoupper($data['warna_hex']) : '#6366F1';
        
        // Validasi warna hex
        if (!$this->isValidHexColor($warna_hex)) {
            error_log("Category::update() - Invalid hex color: $warna_hex");
            return false;
        }
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE tbl_category SET nama_kategori = ?, warna_hex = ? WHERE id_kategori = ?"
            );
            $stmt->bind_param("ssi", $nama_kategori, $warna_hex, $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Category::update() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus kategori
     * Note: Database constraint akan otomatis hapus relasi di tbl_project_category (CASCADE)
     * 
     * @param int $id ID kategori
     * @return bool True jika berhasil
     */
    public function delete($id) {
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_category WHERE id_kategori = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Category::delete() error: " . $e->getMessage());
            return false;
        }
    }
}
