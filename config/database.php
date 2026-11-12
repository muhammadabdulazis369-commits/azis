<?php
/** Koneksi database. Kredensial dapat diatur melalui database.local.php. */
$localConfig = __DIR__ . '/database.local.php';
$config = [
    'host' => '127.0.0.1',
    'database' => 'bumdes_sejahtera',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
if (is_file($localConfig)) {
    $saved = require $localConfig;
    if (is_array($saved)) $config = array_merge($config, $saved);
}
$host = $config['host'];
$dbname = $config['database'];
$username = $config['username'];
$password = $config['password'];
$charset = $config['charset'] ?? 'utf8mb4';

try {
    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('Ekstensi pdo_mysql belum aktif pada PHP. Aktifkan extension=pdo_mysql di php.ini lalu restart Apache.');
    }
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset={$charset}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (Throwable $e) {
    error_log('Koneksi database BUMDes gagal: ' . $e->getMessage());
    http_response_code(500);
    $message = 'Koneksi database gagal. Buka /install.php untuk memasang atau periksa pengaturan database.';
    if (PHP_SAPI !== 'cli') {
        die('<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Koneksi Database</title><body style="font-family:Arial,sans-serif;background:#f5f4f0;padding:40px;color:#28251f"><main style="max-width:620px;margin:auto;background:white;padding:28px;border-radius:16px"><h2>Database belum siap</h2><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><p>Detail teknis: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p><a href="../install.php">Buka halaman instalasi</a></main></body></html>');
    }
    die($message);
}
