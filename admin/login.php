<?php
require_once "../config/config.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verifyCsrf();
    $username = is_string($_POST['username'] ?? null) ? substr(trim($_POST['username']), 0, 50) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $limit = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE username=? AND ip_address=? AND attempted_at >= (NOW() - INTERVAL 15 MINUTE)');
    $limit->execute([$username, $ip]);
    $byUsername = (int)$limit->fetchColumn() >= 5;
    $ipLimit = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip_address=? AND attempted_at >= (NOW() - INTERVAL 15 MINUTE)');
    $ipLimit->execute([$ip]);
    $byIp = (int)$ipLimit->fetchColumn() >= 20;
    $tooManyAttempts = $byUsername || $byIp;
    $pdo->exec('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 30 DAY)');

    $stmt = $pdo->prepare(
        "SELECT * FROM admin WHERE username = ?"
    );

    $stmt->execute([$username]);

    $admin = $stmt->fetch();

    if (!$tooManyAttempts && $admin && password_verify($password, $admin["password"])) {

        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_nama"] = $admin["nama"];
        $_SESSION['last_activity'] = time();
        unset($_SESSION['csrf_token']);
        $clear = $pdo->prepare('DELETE FROM login_attempts WHERE username=? AND ip_address=?');
        $clear->execute([$username, $ip]);
        auditLog($pdo, 'LOGIN', 'Administrator berhasil masuk.');

        header("Location: dashboard.php");
        exit;

    } else {
        if (!$tooManyAttempts) {
            $attempt = $pdo->prepare('INSERT INTO login_attempts (username, ip_address) VALUES (?, ?)');
            $attempt->execute([$username, $ip]);
        }
        auditLog($pdo, 'LOGIN_GAGAL', 'Percobaan login gagal untuk username: ' . $username);
        $error = $tooManyAttempts ? 'Terlalu banyak percobaan. Coba lagi setelah 15 menit.' : 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin BUMDes</title>
<link rel="stylesheet" href="../style.css">
</head>

<body class="login-page">

<div class="login-container">

    <div class="login-logo">
        <h1>BUMDes</h1>
        <p>Sejahtera Mandiri</p>
    </div>

    <h2>Login Administrator</h2>
    <?php if (isset($_GET['expired'])): ?><div class="alert">Sesi berakhir karena tidak ada aktivitas. Silakan login kembali.</div><?php endif; ?>
    <p>Silakan masuk untuk mengelola BUMDes.</p>

    <?php if ($error): ?>
        <div class="alert">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?= csrfField() ?>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn-primary">
            Masuk Dashboard
        </button>

    </form>

    <a href="../masyarakat/index.php" class="back-link">
        Kembali ke Beranda
    </a>

</div>

</body>
</html>
