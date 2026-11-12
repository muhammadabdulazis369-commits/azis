<?php
/** Wizard instalasi awal BUMDes Sejahtera Mandiri. Hapus file ini setelah instalasi berhasil. */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}
$lockFile = __DIR__ . '/config/installed.lock';
$localConfig = __DIR__ . '/config/database.local.php';
if (is_file($lockFile)) {
    http_response_code(403);
    exit('Instalasi sudah selesai. Hapus install.php dari folder proyek untuk keamanan.');
}
if (empty($_SESSION['install_token'])) $_SESSION['install_token'] = bin2hex(random_bytes(32));
$error = '';
$success = false;
$values = [
    'host' => trim($_POST['host'] ?? '127.0.0.1'),
    'database' => trim($_POST['database'] ?? 'bumdes_sejahtera'),
    'username' => trim($_POST['username'] ?? 'root'),
    'password' => (string)($_POST['password'] ?? ''),
];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $token = $_POST['install_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['install_token'], $token)) {
        $error = 'Sesi instalasi tidak valid. Muat ulang halaman.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $values['database'])) {
        $error = 'Nama database hanya boleh berisi huruf, angka, dan garis bawah.';
    } elseif ($values['host'] === '' || $values['username'] === '') {
        $error = 'Host dan username database wajib diisi.';
    } elseif (!extension_loaded('pdo_mysql')) {
        $error = 'Ekstensi pdo_mysql belum aktif. Buka php.ini, aktifkan extension=pdo_mysql, lalu restart Apache.';
    } else {
        try {
            $server = new PDO('mysql:host=' . $values['host'] . ';charset=utf8mb4', $values['username'], $values['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $server->exec('CREATE DATABASE IF NOT EXISTS `' . $values['database'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $db = new PDO('mysql:host=' . $values['host'] . ';dbname=' . $values['database'] . ';charset=utf8mb4', $values['username'], $values['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
            $ddl = [];
            $ddl[] = "CREATE TABLE IF NOT EXISTS admin (id INT AUTO_INCREMENT PRIMARY KEY,nama VARCHAR(100) NOT NULL,username VARCHAR(50) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $ddl[] = "CREATE TABLE IF NOT EXISTS unit_usaha (id INT AUTO_INCREMENT PRIMARY KEY,nama_unit VARCHAR(100) NOT NULL,kategori VARCHAR(100) NOT NULL,deskripsi TEXT NULL,alamat TEXT NULL,status ENUM('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $ddl[] = "CREATE TABLE IF NOT EXISTS pendapatan (id INT AUTO_INCREMENT PRIMARY KEY,kode_transaksi VARCHAR(50) NOT NULL UNIQUE,tanggal DATE NOT NULL,kategori VARCHAR(100) NOT NULL,unit_usaha_id INT NULL,keterangan TEXT NULL,jumlah DECIMAL(15,2) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_pendapatan_tanggal(tanggal),INDEX idx_pendapatan_unit(unit_usaha_id),CONSTRAINT fk_pendapatan_unit FOREIGN KEY(unit_usaha_id) REFERENCES unit_usaha(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $ddl[] = "CREATE TABLE IF NOT EXISTS pengeluaran (id INT AUTO_INCREMENT PRIMARY KEY,kode_transaksi VARCHAR(50) NOT NULL UNIQUE,tanggal DATE NOT NULL,kategori VARCHAR(100) NOT NULL,unit_usaha_id INT NULL,keterangan TEXT NULL,jumlah DECIMAL(15,2) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_pengeluaran_tanggal(tanggal),INDEX idx_pengeluaran_unit(unit_usaha_id),CONSTRAINT fk_pengeluaran_unit FOREIGN KEY(unit_usaha_id) REFERENCES unit_usaha(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $ddl[] = "CREATE TABLE IF NOT EXISTS login_attempts (id BIGINT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(100) NOT NULL,ip_address VARCHAR(45) NOT NULL,attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX idx_login_attempts(username,ip_address,attempted_at),INDEX idx_login_ip(ip_address,attempted_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $ddl[] = "CREATE TABLE IF NOT EXISTS audit_log (id BIGINT AUTO_INCREMENT PRIMARY KEY,admin_id INT NULL,aksi VARCHAR(100) NOT NULL,detail TEXT NULL,ip_address VARCHAR(45) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX idx_audit_created(created_at),CONSTRAINT fk_audit_admin FOREIGN KEY(admin_id) REFERENCES admin(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            foreach ($ddl as $sql) $db->exec($sql);

            // Pastikan kolom timestamp tersedia pada database versi lama.
            $requiredColumns = [
                'admin' => ['updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
                'unit_usaha' => ['updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
                'pendapatan' => ['updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
                'pengeluaran' => ['updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
            ];
            foreach ($requiredColumns as $table => $columns) {
                foreach ($columns as $column => $definition) {
                    $checkColumn = $db->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
                    $checkColumn->execute([$table, $column]);
                    if (!(int)$checkColumn->fetchColumn()) $db->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
                }
            }

            $checkAdmin = $db->prepare('SELECT id FROM admin WHERE username = ? LIMIT 1');
            $checkAdmin->execute(['admin']);
            if (!$checkAdmin->fetchColumn()) {
                $addAdmin = $db->prepare('INSERT INTO admin(nama,username,password) VALUES(?,?,?)');
                $addAdmin->execute(['Administrator', 'admin', password_hash('admin12345', PASSWORD_DEFAULT)]);
            }
            $unitCount = (int)$db->query('SELECT COUNT(*) FROM unit_usaha')->fetchColumn();
            if ($unitCount === 0) {
                $seed = $db->prepare('INSERT INTO unit_usaha(nama_unit,kategori,deskripsi,status) VALUES(?,?,?,?)');
                foreach ([['Simpan Desa','Keuangan','Layanan simpan pinjam masyarakat desa.','Aktif'],['Pertanian Mandiri','Pertanian','Pengembangan pertanian desa.','Aktif'],['Perdagangan Desa','Perdagangan','Pengelolaan perdagangan dan produk desa.','Aktif'],['Wisata Desa','Pariwisata','Pengembangan wisata desa.','Aktif']] as $row) $seed->execute($row);
            }
            $configContent = "<?php\nreturn " . var_export(['host'=>$values['host'],'database'=>$values['database'],'username'=>$values['username'],'password'=>$values['password'],'charset'=>'utf8mb4'], true) . ";\n";
            if (file_put_contents($localConfig, $configContent, LOCK_EX) === false) throw new RuntimeException('Tidak dapat menulis config/database.local.php. Pastikan folder config dapat ditulis.');
            file_put_contents($lockFile, 'installed_at=' . date(DATE_ATOM) . PHP_EOL, LOCK_EX);
            $success = true;
            unset($_SESSION['install_token']);
        } catch (Throwable $e) {
            error_log('Instalasi BUMDes gagal: ' . $e->getMessage());
            $error = 'Instalasi gagal: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalasi BUMDes Sejahtera Mandiri</title><style>
:root{--ink:#29271f;--muted:#77756d;--line:#e8e5dc;--paper:#f5f4ef;--gold:#9b8153}*{box-sizing:border-box}body{margin:0;background:var(--paper);font-family:Inter,Arial,sans-serif;color:var(--ink);min-height:100vh;display:grid;place-items:center;padding:24px}.wrap{width:min(100%,620px);background:#fff;border:1px solid var(--line);border-radius:20px;padding:clamp(24px,5vw,42px);box-shadow:0 20px 60px #29271f12}.eyebrow{color:var(--gold);font-size:12px;font-weight:700;letter-spacing:.15em;text-transform:uppercase}h1{font-size:clamp(25px,5vw,34px);margin:10px 0}p{color:var(--muted);line-height:1.65}.grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.field{display:grid;gap:7px}.field.full{grid-column:1/-1}label{font-size:13px;font-weight:650}input{width:100%;padding:13px 14px;border:1px solid var(--line);border-radius:10px;font:inherit}input:focus{outline:2px solid #9b815344;border-color:var(--gold)}button,.link{display:inline-flex;justify-content:center;align-items:center;background:var(--ink);color:white;border:0;border-radius:10px;padding:14px 20px;font-weight:700;text-decoration:none;cursor:pointer;width:100%;margin-top:18px}.notice{padding:13px 15px;border-radius:10px;background:#f8eeee;color:#8c3636;margin:16px 0}.ok{background:#edf7ef;color:#28633a}.small{font-size:12px}.creds{background:#f7f6f1;border:1px solid var(--line);padding:14px;border-radius:12px;margin:16px 0}.creds code{font-weight:700}@media(max-width:520px){.grid{grid-template-columns:1fr}.field.full{grid-column:auto}}
</style></head><body><main class="wrap"><div class="eyebrow">BUMDes Sejahtera Mandiri</div><h1><?= $success ? 'Instalasi berhasil' : 'Instalasi sistem' ?></h1>
<?php if ($success): ?><div class="notice ok">Database dan tabel berhasil disiapkan. Konfigurasi koneksi telah disimpan.</div><div class="creds"><strong>Login awal administrator</strong><br>Username: <code>admin</code><br>Password: <code>admin12345</code></div><p>Untuk keamanan, hapus file <strong>install.php</strong> dari folder proyek setelah ini. Segera ganti password administrator.</p><a class="link" href="admin/login.php">Masuk ke halaman admin</a><a class="link" style="background:#777" href="masyarakat/">Lihat website publik</a>
<?php else: ?><p>Isi koneksi MySQL XAMPP. Pengaturan umum XAMPP: host <strong>127.0.0.1</strong>, username <strong>root</strong>, password dikosongkan.</p><?php if ($error): ?><div class="notice"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><form method="post"><input type="hidden" name="install_token" value="<?= htmlspecialchars($_SESSION['install_token'], ENT_QUOTES, 'UTF-8') ?>"><div class="grid"><div class="field"><label>Host MySQL</label><input name="host" value="<?= htmlspecialchars($values['host'], ENT_QUOTES, 'UTF-8') ?>" required></div><div class="field"><label>Nama Database</label><input name="database" value="<?= htmlspecialchars($values['database'], ENT_QUOTES, 'UTF-8') ?>" required></div><div class="field"><label>Username MySQL</label><input name="username" value="<?= htmlspecialchars($values['username'], ENT_QUOTES, 'UTF-8') ?>" required></div><div class="field"><label>Password MySQL</label><input type="password" name="password" value="" autocomplete="new-password"></div></div><button type="submit">Pasang Database dan Sistem</button></form><p class="small">Jika muncul pesan pdo_mysql belum aktif: buka XAMPP Control Panel → Apache → Config → PHP (php.ini), cari <code>;extension=pdo_mysql</code>, hapus tanda titik koma, simpan, lalu restart Apache.</p><?php endif; ?></main></body></html>
