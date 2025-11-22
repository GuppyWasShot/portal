# Debug Guide - Ubah Password

## 🔍 File Debug yang Dibuat

### 1. `debug_ubah_password.php` (Root folder)
**URL**: `http://localhost/portal_tpl/debug_ubah_password.php`

**Fungsi:**
- Cek session admin
- Cek koneksi database
- Cek tabel tbl_admin
- Cek file-file yang diperlukan
- Test password hash & verify
- Form test ubah password
- Cek PHP environment
- Lihat error logs

**Cara Pakai:**
1. Login sebagai admin terlebih dahulu
2. Buka `http://localhost/portal_tpl/debug_ubah_password.php`
3. Lihat semua informasi debug
4. Test form ubah password di bagian bawah

---

### 2. `proses_ubah_password_debug.php` (controllers/admin/)
**Lokasi**: `controllers/admin/proses_ubah_password_debug.php`

**Fungsi:**
- Versi debug dari proses ubah password
- Menulis log detail ke file `debug_password_log.txt`
- Menampilkan semua error

**Cara Pakai:**
1. Edit file `views/admin/ubah_password.php`
2. Ubah action form dari:
   ```html
   <form action="../../controllers/admin/proses_ubah_password.php" method="POST">
   ```
   Menjadi:
   ```html
   <form action="../../controllers/admin/proses_ubah_password_debug.php" method="POST">
   ```
3. Submit form ubah password
4. Cek file `debug_password_log.txt` di root folder

---

## 🐛 Langkah Debugging

### Step 1: Cek Environment
```
1. Buka: http://localhost/portal_tpl/debug_ubah_password.php
2. Pastikan semua checklist hijau (✓)
3. Catat jika ada yang merah (✗)
```

### Step 2: Test Password Hash
Di halaman debug, lihat section "Password Hash Test":
- Pastikan hash ter-generate
- Pastikan verify test PASS

### Step 3: Gunakan Debug Controller
```
1. Edit views/admin/ubah_password.php
2. Ganti action form ke proses_ubah_password_debug.php
3. Coba ubah password
4. Buka file debug_password_log.txt
5. Lihat log detail prosesnya
```

### Step 4: Cek Log File
```
File: /opt/lampp/htdocs/portal_tpl/debug_password_log.txt

Log akan berisi:
- Session data
- POST data
- Database queries
- Password verification result
- Update result
- Error messages (jika ada)
```

---

## 🔧 Common Issues & Solutions

### Issue 1: "Password lama tidak sesuai"
**Kemungkinan:**
- Password yang diinput salah
- Hash di database corrupt
- Password verify function error

**Debug:**
```php
// Di debug file, cek:
- Hash dari database (section 3)
- Test verify dengan password yang benar (section 5)
```

### Issue 2: "Session tidak valid"
**Kemungkinan:**
- Belum login
- Session expired
- Session path error

**Debug:**
```php
// Di debug file, cek:
- Section 1: Session Check
- Pastikan admin_logged_in = true
- Pastikan id_admin ada
```

### Issue 3: Form tidak submit
**Kemungkinan:**
- JavaScript error
- Form action salah
- CSRF protection

**Debug:**
```
1. Buka browser DevTools (F12)
2. Tab Console - cek error JavaScript
3. Tab Network - cek request POST
4. Lihat response dari server
```

### Issue 4: Database error
**Kemungkinan:**
- Connection error
- Table tidak ada
- Permission error

**Debug:**
```php
// Di debug file, cek:
- Section 2: Database Connection
- Section 3: Tabel tbl_admin
```

---

## 📝 Checklist Troubleshooting

- [ ] Login sebagai admin berhasil
- [ ] Akses debug_ubah_password.php berhasil
- [ ] Semua file check hijau (✓)
- [ ] Database connection berhasil
- [ ] Tabel tbl_admin ada dan terisi
- [ ] Password hash test PASS
- [ ] Form test bisa disubmit
- [ ] Log file terbuat (debug_password_log.txt)
- [ ] Tidak ada error di PHP error log
- [ ] Browser console tidak ada error

---

## 🚨 Jika Masih Error

1. **Share log file**: Kirim isi `debug_password_log.txt`
2. **Share screenshot**: Screenshot halaman debug
3. **Share error**: Copy error message yang muncul
4. **Browser console**: Screenshot console errors
5. **Network tab**: Screenshot network request/response

---

## 🔄 Kembali ke Normal

Setelah debugging selesai:

1. **Restore form action** di `ubah_password.php`:
   ```html
   <form action="../../controllers/admin/proses_ubah_password.php" method="POST">
   ```

2. **Hapus file debug** (opsional):
   - `debug_ubah_password.php`
   - `debug_password_log.txt`
   - `controllers/admin/proses_ubah_password_debug.php`

3. **Disable error display** di production:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```
