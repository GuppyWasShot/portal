-- =====================================================
-- Tambah Admin Baru
-- Username: administrator
-- Password: admin123
-- =====================================================

-- Insert admin baru
INSERT INTO `tbl_admin` (`username`, `email`, `password`) 
VALUES (
    'administrator', 
    'administrator@portaltpl.ac.id', 
    '$2y$10$mmYoFTfvW87YwH1.yFLs/OeX8XY0lOM2CeuwRUjxS9SLIhaRcA8pK'
);

-- Verifikasi data admin
SELECT id_admin, username, email, 'Password: admin123' as password_info 
FROM tbl_admin 
WHERE username = 'administrator';
