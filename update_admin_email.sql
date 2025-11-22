-- =====================================================
-- Update Email untuk Admin yang Belum Punya Email
-- =====================================================

-- Cek admin yang email-nya kosong atau NULL
SELECT id_admin, username, email 
FROM tbl_admin 
WHERE email IS NULL OR email = '';

-- Update email untuk admin 'administrator' (sesuaikan username jika berbeda)
UPDATE tbl_admin 
SET email = 'administrator@portaltpl.ac.id' 
WHERE username = 'administrator' AND (email IS NULL OR email = '');

-- Verifikasi hasil update
SELECT id_admin, username, email 
FROM tbl_admin;
