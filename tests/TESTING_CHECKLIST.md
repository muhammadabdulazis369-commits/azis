# Checklist Pengujian BUMDes Sejahtera Mandiri

Jalankan checklist ini setelah instalasi database dan sebelum aplikasi dipakai.

## Autentikasi dan keamanan
- [ ] Login dengan akun awal berhasil.
- [ ] Password salah menampilkan pesan umum dan tercatat pada audit log.
- [ ] Percobaan login dibatasi setelah 5 kegagalan per username atau 20 kegagalan per IP dalam 15 menit.
- [ ] Login berhasil membuat session ID baru.
- [ ] Halaman admin tidak dapat dibuka tanpa login.
- [ ] Sesi berakhir setelah 30 menit tanpa aktivitas.
- [ ] Logout hanya berjalan melalui POST dan token CSRF valid.
- [ ] Form tambah/edit/hapus/status menolak token CSRF yang salah.
- [ ] Password baru ditolak jika kurang dari 10 karakter atau tidak memiliki huruf/angka.

## Transaksi
- [ ] Pendapatan dapat ditambah, diedit, dan dihapus.
- [ ] Pengeluaran dapat ditambah, diedit, dan dihapus.
- [ ] Tanggal tidak valid atau tanggal masa depan ditolak.
- [ ] Nominal nol, negatif, format teks, dan nominal di luar batas ditolak.
- [ ] Unit usaha tidak aktif tidak tersedia pada form transaksi baru.
- [ ] Penghapusan unit usaha tidak menghapus transaksi lama.
- [ ] Kode transaksi unik meskipun beberapa transaksi dibuat berdekatan.

## Unit usaha dan laporan
- [ ] Unit usaha dapat ditambah, diedit, diaktifkan/nonaktifkan, dan dihapus.
- [ ] Dashboard menghitung total pendapatan, pengeluaran, saldo, dan unit aktif.
- [ ] Filter laporan bulanan menampilkan periode yang benar.
- [ ] Rekap kategori cocok dengan rincian transaksi.
- [ ] Ekspor CSV dapat dibuka di spreadsheet dan nilai teks tidak dieksekusi sebagai formula.
- [ ] Data publik hanya menampilkan unit usaha aktif.
- [ ] Angka ringkasan publik sesuai dengan database.

## Operasional
- [ ] Tampilan diuji di desktop dan ponsel.
- [ ] Tidak ada error PHP pada log Apache/PHP.
- [ ] Backup database dibuat dan proses restore pernah diuji.
- [ ] Password database produksi tidak menggunakan kredensial default XAMPP.
- [ ] Server produksi menggunakan HTTPS.

Catat hasil, tanggal, versi PHP, versi MySQL/MariaDB, browser, dan bukti untuk setiap pengujian.
