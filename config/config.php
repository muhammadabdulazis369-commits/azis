<?php
// Konfigurasi inti aplikasi. Pastikan HTTPS aktif sebelum secure cookie di server produksi.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/database.php';
define('NAMA_BUMDES', 'BUMDes Sejahtera Mandiri');

function e($data): string {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function rupiah($angka): string {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
function tanggal($value): string {
    if (!$value || $value === '0000-00-00') return '-';
    $time = strtotime($value);
    return $time ? date('d-m-Y', $time) : '-';
}
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}
function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(419);
        exit('Permintaan tidak valid atau sesi telah berakhir. Muat ulang halaman lalu coba lagi.');
    }
}
function adminLogin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php'); exit;
    }
    $now = time();
    if (!empty($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity']) > 1800) {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: login.php?expired=1'); exit;
    }
    $_SESSION['last_activity'] = $now;
}
function textLength(string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}
function textCut(string $value, int $length): string {
    return function_exists('mb_substr') ? mb_substr($value, 0, $length, 'UTF-8') : substr($value, 0, $length);
}
function auditLog(PDO $pdo, string $aksi, string $detail = ''): void {
    try {
        $stmt = $pdo->prepare('INSERT INTO audit_log (admin_id, aksi, detail, ip_address) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_SESSION['admin_id'] ?? null, $aksi, textCut($detail, 1000), $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) {
        error_log('Audit log gagal: ' . $e->getMessage());
    }
}
function validDate(string $date): bool {
    $d = DateTime::createFromFormat('!Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
function validMoney($value): bool {
    return is_scalar($value) && preg_match('/^\d{1,13}(?:\.\d{1,2})?$/', (string)$value) && (float)$value > 0;
}
function redirect(string $url): never {
    header('Location: ' . $url); exit;
}
