<?php
require_once "../config/config.php";adminLogin();$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){verifyCsrf();$old=$_POST['password_lama']??'';$new=$_POST['password_baru']??'';$confirm=$_POST['konfirmasi_password']??'';
$stmt=$pdo->prepare('SELECT password FROM admin WHERE id=?');$stmt->execute([$_SESSION['admin_id']]);$hash=$stmt->fetchColumn();
if(!$hash||!password_verify($old,$hash))$error='Password lama tidak sesuai.';
elseif(strlen($new)<10||!preg_match('/[A-Za-z]/',$new)||!preg_match('/[0-9]/',$new))$error='Password baru minimal 10 karakter dan harus mengandung huruf serta angka.';
elseif($new!==$confirm)$error='Konfirmasi password tidak sama.';
else{$upd=$pdo->prepare('UPDATE admin SET password=? WHERE id=?');$upd->execute([password_hash($new,PASSWORD_DEFAULT),$_SESSION['admin_id']]);auditLog($pdo,'GANTI_PASSWORD','Administrator mengganti password.');$success='Password berhasil diperbarui.';}}
require '_header.php';?>
<div class="page-heading"><div><h1>Pengaturan Akun</h1><p>Perbarui password administrator secara berkala.</p></div></div><div class="content-panel">
<?php if($error):?><div class="alert"><?=e($error)?></div><?php endif;?><?php if($success):?><div class="alert-success"><?=e($success)?></div><?php endif;?>
<form method="POST" class="form-grid"><?=csrfField()?>
<div class="form-group full"><label>Password Lama</label><input type="password" name="password_lama" autocomplete="current-password" required></div>
<div class="form-group"><label>Password Baru</label><input type="password" name="password_baru" minlength="10" autocomplete="new-password" required><small>Minimal 10 karakter, mengandung huruf dan angka.</small></div>
<div class="form-group"><label>Konfirmasi Password Baru</label><input type="password" name="konfirmasi_password" minlength="10" autocomplete="new-password" required></div>
<div class="form-group full"><button class="btn-submit" type="submit">Simpan Password Baru</button></div></form></div><?php require '_footer.php';?>
