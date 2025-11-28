<?php
/**
 * Kelas Dosen
 * Buat ngatur semua data dosen - CRUD lengkap
 * 
 * Cara pake:
 * $dosen = new Dosen();
 * $list = $dosen->getAll();
 */
require_once __DIR__ . '/Database.php';

class Dosen {
    private $db;
    
    /**
     * Constructor - bikin object Dosen
     * Menggunakan dependency injection untuk koneksi database
     */
    public function __construct($database = null) {
        if ($database === null) {
            $this->db = Database::getInstance()->getConnection();
        } else {
            $this->db = $database;
        }
    }
    
    /**
     * Ambil semua data dosen (bisa pake filter)
     * 
     * @param array $filters Filter opsional (status, order/urutan)
     * @return array List dosen
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
        
        // Bikin klausa WHERE
        $where_clause = !empty($where_conditions) 
            ? "WHERE " . implode(' AND ', $where_conditions)
            : "";
        
        // Klausa urutan
        $order = isset($filters['order']) ? $filters['order'] : 'status DESC, urutan ASC, nama ASC';
        
        $query = "SELECT * FROM tbl_dosen $where_clause ORDER BY $order";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        $dosen_list = [];
        while ($row = $result->fetch_assoc()) {
            $dosen_list[] = $row;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        return $dosen_list;
    }
    
    /**
     * Ambil data dosen berdasarkan ID
     * 
     * @param int $id ID dosen yang mau diambil
     * @return array|null Data dosen atau null kalo ga ketemu
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_dosen WHERE id_dosen = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $dosen = $result->fetch_assoc();
        $stmt->close();
        
        return $dosen;
    }
    
    /**
     * Ambil jumlah dosen yang aktif (buat di dashboard)
     * 
     * @return int Jumlah dosen aktif
     */
    public function getActiveCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM tbl_dosen WHERE status = 'active'");
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }
    
    /**
     * Bikin data dosen baru
     * 
     * @param array $data Data dosen (nama, gelar, jabatan, email, foto_url, deskripsi, urutan, status) yang mau ditambahin
     * @return int|false ID dosen baru kalo berhasil, false kalo gagal
     */
    public function create($data) {
        // Validasi field yang wajib diisi
        if (empty($data['nama'])) {
            return false;
        }
        
        // Set nilai default
        $nama = $data['nama'];
        $gelar = $data['gelar'] ?? null;
        $jabatan = $data['jabatan'] ?? null;
        $email = $data['email'] ?? null;
        $foto_url = $data['foto_url'] ?? null;
        $deskripsi = $data['deskripsi'] ?? null;
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tbl_dosen (nama, gelar, jabatan, email, foto_url, deskripsi, urutan, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssssis", $nama, $gelar, $jabatan, $email, $foto_url, $deskripsi, $urutan, $status);
            $success = $stmt->execute();
            
            if ($success) {
                $insert_id = $stmt->insert_id;
                $stmt->close();
                return $insert_id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Dosen::create() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update data dosen
     * 
     * @param int $id ID dosen yang mau diupdate
     * @param array $data Data baru yang mau diupdate
     * @return bool True kalo berhasil
     */
    public function update($id, $data) {
        // Validasi ID
        if ($id <= 0) {
            return false;
        }
        
        // Validasi field yang wajib diisi
        if (empty($data['nama'])) {
            return false;
        }
        
        $nama = $data['nama'];
        $gelar = $data['gelar'] ?? null;
        $jabatan = $data['jabatan'] ?? null;
        $email = $data['email'] ?? null;
        $deskripsi = $data['deskripsi'] ?? null;
        $urutan = isset($data['urutan']) ? intval($data['urutan']) : 0;
        $status = isset($data['status']) && $data['status'] === 'inactive' ? 'inactive' : 'active';
        
        try {
            // Cek apakah foto_url di-update
            if (isset($data['foto_url'])) {
                $foto_url = $data['foto_url'];
                $stmt = $this->db->prepare(
                    "UPDATE tbl_dosen 
                     SET nama = ?, gelar = ?, jabatan = ?, email = ?, foto_url = ?, deskripsi = ?, urutan = ?, status = ?
                     WHERE id_dosen = ?"
                );
                $stmt->bind_param("ssssssisi", $nama, $gelar, $jabatan, $email, $foto_url, $deskripsi, $urutan, $status, $id);
            } else {
                // Update tanpa mengubah foto_url
                $stmt = $this->db->prepare(
                    "UPDATE tbl_dosen 
                     SET nama = ?, gelar = ?, jabatan = ?, email = ?, deskripsi = ?, urutan = ?, status = ?
                     WHERE id_dosen = ?"
                );
                $stmt->bind_param("sssssisi", $nama, $gelar, $jabatan, $email, $deskripsi, $urutan, $status, $id);
            }
            
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Dosen::update() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hapus data dosen
     * 
     * @param int $id ID dosen yang mau dihapus
     * @return bool True kalo berhasil
     */
    public function delete($id) {
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_dosen WHERE id_dosen = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $success && $affected_rows > 0;
        } catch (Exception $e) {
            error_log("Dosen::delete() error: " . $e->getMessage());
            return false;
        }
    }
}
