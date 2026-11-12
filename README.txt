==================================================
BUMDes SEJAHTERA MANDIRI - PAKET INSTALASI
==================================================

TEKNOLOGI
- PHP Native (PHP 8.1 atau lebih baru)
- MySQL / MariaDB
- PDO MySQL (extension pdo_mysql wajib aktif)
- HTML5, CSS3, JavaScript

CARA INSTALASI PALING MUDAH (XAMPP)
1. Ekstrak folder bumdes-sejahtera-mandiri ke C:\xampp\htdocs\
   Pastikan hasilnya: C:\xampp\htdocs\bumdes-sejahtera-mandiri\install.php
   Jangan sampai menjadi folder bersarang dua kali.
2. Buka XAMPP Control Panel, lalu Start Apache dan MySQL.
3. Buka http://localhost/bumdes-sejahtera-mandiri/install.php
4. Isi host 127.0.0.1, database bumdes_sejahtera, username root,
   dan kosongkan password jika XAMPP Anda masih memakai pengaturan default.
5. Klik Pasang Database dan Sistem.
6. Setelah berhasil, hapus file install.php dari folder proyek.
7. Buka http://localhost/bumdes-sejahtera-mandiri/masyarakat/
8. Login admin di http://localhost/bumdes-sejahtera-mandiri/admin/login.php

LOGIN AWAL
Username : admin
Password : admin12345
Segera ganti password melalui menu Ganti Password.

JIKA MUNCUL ERROR pdo_mysql
1. XAMPP Control Panel > Apache > Config > PHP (php.ini).
2. Cari baris ;extension=pdo_mysql
3. Hapus tanda titik koma di depannya sehingga menjadi extension=pdo_mysql.
4. Simpan php.ini, lalu Stop dan Start Apache kembali.
5. Pastikan MySQL berstatus Running.

JIKA TIDAK BISA DIBUKA
- Pastikan alamat folder benar dan install.php berada tepat di dalam folder proyek.
- Pastikan Apache dan MySQL sama-sama Running.
- Periksa port Apache (umumnya 80) dan MySQL (umumnya 3306).
- Pastikan ekstensi pdo_mysql aktif.
- Pesan error koneksi database akan menampilkan detail teknis untuk membantu diagnosis.

DATABASE LAMA
- Backup database terlebih dahulu sebelum melakukan perubahan.
- Wizard instalasi membuat tabel yang belum ada dan melengkapi kolom updated_at
  yang belum tersedia. Wizard tidak menghapus data yang sudah ada.
- Jika database lama memiliki struktur yang sangat berbeda, buat database baru
  dengan nama lain melalui wizard agar data lama tetap aman.
- Tidak perlu mengimpor SQL secara manual; wizard instalasi menyiapkan tabel dan kolom yang diperlukan.

FITUR
PUBLIK:
- Beranda dan profil BUMDes
- Daftar unit usaha aktif
- Informasi pendapatan dan pengeluaran
- Ringkasan laporan keuangan

ADMIN:
- Login, logout, dan sesi kedaluwarsa
- Dashboard ringkasan keuangan
- CRUD transaksi pendapatan dan pengeluaran
- CRUD unit usaha
- Laporan bulanan dan ekspor CSV
- Ganti password administrator
- Log aktivitas administrator
- Validasi input, prepared statements, CSRF, dan pembatasan percobaan login

CATATAN
- Data contoh hanya untuk demonstrasi dan harus diganti dengan data yang sah.
- Sistem ini belum menggantikan audit/akuntansi resmi.
- Sebelum dipakai secara operasional, lakukan pengujian transaksi, rekonsiliasi,
  backup-pemulihan, hak akses, HTTPS, dan pemeriksaan keamanan.
- Pemeriksaan sintaks PHP bukan bukti bahwa aplikasi telah diuji langsung
  terhadap konfigurasi XAMPP dan MySQL milik pengguna.
==================================================
