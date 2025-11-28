<?php
/**
 * About Class
 * Menangani semua operasi terkait About Sections (Halaman Tentang)
 * 
 * Usage:
 * $about = new About();
 * $sections = $about->getAll();
 */
require_once __DIR__ . '/Database.php';

class About {
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
     * Mendapatkan semua about sections dengan optional filtering
     * 
     * @param array $filters Optional filters (status, order)
     * @return array Array of about sections
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
        
        // Build WHERE clause
        $where_clause = !empty($where_conditions) 
            ? "WHERE " . implode(' AND ', $where_conditions)
            : "";
        
        // Order clause
        $order = isset($filters['order']) ? $filters['order'] : 'urutan ASC, created_at DESC';
        
        $query = "SELECT * FROM tbl_about_sections $where_clause ORDER BY $order";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        $about_list = [];
        while ($row = $result->fetch_assoc()) {
            $about_list[] = $row;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        return $about_list;
    }
    
    /**
     * Mendapatkan about sections aktif (untuk tampilan public)
     * 
     * @return array Array of active sections
     */
    public function getActive() {
        return $this->getAll(['status' => 'active']);
    }
    
    /**
     * Mendapatkan about section berdasarkan ID
     * 
     * @param int $id ID section
     * @return array|null Data section atau null
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_about_sections WHERE id_section = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $section = $result->fetch_assoc();
        $stmt->close();
        
        return $section;
    }
    
    /**
     * Mendapatkan about section berdasarkan slug
     * 
     * @param string $slug Slug section
     * @return array|null Data section atau null
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_about_sections WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $section = $result->fetch_assoc();
        $stmt->close();
        
        return $section;
    }
    
    /**
     * Generate slug dari judul
     * Menggunakan helper slugify_text jika tersedia
     * 
     * @param string $judul Judul section
     * @return string Slug
     */
    private function generateSlug($judul) {
        // Cek apakah helper tersedia
        if (function_exists('slugify_text')) {
            return slugify_text($judul);
        }
        
        // Fallback: simple slug generation
        $slug = strtolower(trim($judul));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
    
    /**
     * Cek apakah slug sudah digunakan
     * 
     * @param string $slug Slug to check
     * @param int|null $exclude_id ID to exclude from check (untuk update)
     * @return bool True jika slug sudah digunakan
     */
    private function isSlugExists($slug, $exclude_id = null) {
        if ($exclude_id) {
            $stmt = $this->db->prepare(
                "SELECT id_section FROM tbl_about_sections WHERE slug = ? AND id_section != ?"
            );
            $stmt->bind_param("si", $slug, $exclude_id);
        } else {
            $stmt = $this->db->prepare(
                "SELECT id_section FROM tbl_about_sections WHERE slug = ?"
            );
            $stmt->bind_param("s", $slug);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    /**
     * Buat slug unik dengan menambahkan angka jika perlu
     * 
     * @param string $base_slug Base slug
     * @param int|null $exclude_id ID to exclude
     * @return string Unique slug
     */
    private function makeUniqueSlug($base_slug, $exclude_id = null) {
        $slug = $base_slug;
        $counter = 1;
        
        while ($this->isSlugExists($slug, $exclude_id)) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Membuat about section baru
     * 
     * @param array $data Data section (judul, konten, slug, urutan, status)
     * @return int|false ID section baru atau false jika gagal
     */
    public function create($data) {
        // Validasi required fields
        if (empty($data['judul']) || empty($data['konten'])) {
            return false;
        }
        
        $judul = $data['judul'];
        $konten = $data['konten'];
        
        // Generate slug jika tidak disediakan
        if (!empty($data['slug'])) {
            $slug = $data['slug'];
        } else {
            $slug = $this->generateSlug($judul);
        }
        
        // Ensure unique slug
        $slug = $this->makeUniqueSlug($slug);
        
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_about_sections (judul, slug, konten, urutan, status) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssis", $judul, $slug, $konten, $urutan, $status);
            $success = $stmt->execute();
            
            if ($success) {
                $insert_id = $stmt->insert_id;
                $stmt->close();
                return $insert_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("About::create() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update about section
     * 
     * @param int $id ID section
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil
     */
    public function update($id, $data) {
        if ($id <= 0) {
            return false;
        }
        
        // Validasi required fields
        if (empty($data['judul']) || empty($data['konten'])) {
            return false;
        }
        
        $judul = $data['judul'];
        $konten = $data['konten'];
        
        // Update slug jika disediakan
        if (!empty($data['slug'])) {
            $slug = $data['slug'];
            $slug = $this->makeUniqueSlug($slug, $id);
        } else {
            // Keep existing slug
            $existing = $this->getById($id);
            $slug = $existing['slug'] ?? $this->generateSlug($judul);
        }
        
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE tbl_about_sections 
                 SET judul = ?, slug = ?, konten = ?, urutan = ?, status = ?
                 WHERE id_section = ?"
            );
            $stmt->bind_param("sssisi", $judul, $slug, $konten, $urutan, $status, $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("About::update() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus about section
     * 
     * @param int $id ID section
     * @return bool True jika berhasil
     */
    public function delete($id) {
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_about_sections WHERE id_section = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("About::delete() error: " . $e->getMessage());
            return false;
        }
    }
}
