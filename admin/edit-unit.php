<?php
require_once "../config/config.php";adminLogin();$id=filter_var($_GET['id']??$_POST['id']??0,FILTER_VALIDATE_INT);if(!$id||$id<1){http_response_code(400);exit('ID tidak valid.');}
$stmt=$pdo->prepare('SELECT * FROM unit_usaha WHERE id=?');$stmt->execute([$id]);$unit=$stmt->fetch();if(!$unit){http_response_code(404);exit('Unit usaha tidak ditemukan.');}$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verifyCsrf();$nama=trim($_POST['nama_unit']??'');$kategori=trim($_POST['kategori']??'');$deskripsi=trim($_POST['deskripsi']??'');$alamat=trim($_POST['alamat']??'');$status=$_POST['status']??'';
if($nama===''||textLength($nama)>100||$kategori===''||textLength($kategori)>100||!in_array($status,['Aktif','Tidak Aktif'],true))$error='Periksa kembali nama, kategori, dan status unit.';
else{$up=$pdo->prepare('UPDATE unit_usaha SET nama_unit=?,kategori=?,deskripsi=?,alamat=?,status=? WHERE id=?');$up->execute([$nama,$kategori,$deskripsi,$alamat,$status,$id]);auditLog($pdo,'EDIT_UNIT','Memperbarui unit '.$nama);redirect('unit-usaha.php?updated=1');}}
require '_header.php';?>
<div class="page-heading"><div><h1>Edit Unit Usaha</h1><p>Perbarui informasi unit usaha BUMDes.</p></div></div><div class="content-panel"><?php if($error):?><div class="alert"><?=e($error)?></div><?php endif;?>
<form method="POST" class="form-grid"><?=csrfField()?><input type="hidden" name="id" value="<?= (int)$id ?>">
<div class="form-group"><label>Nama Unit</label><input name="nama_unit" maxlength="100" value="<?=e($unit['nama_unit'])?>" required></div><div class="form-group"><label>Kategori</label><input name="kategori" maxlength="100" value="<?=e($unit['kategori'])?>" required></div>
<div class="form-group full"><label>Deskripsi</label><textarea name="deskripsi"><?=e($unit['deskripsi'])?></textarea></div><div class="form-group full"><label>Alamat</label><textarea name="alamat"><?=e($unit['alamat'])?></textarea></div>
<div class="form-group"><label>Status</label><select name="status"><option <?= $unit['status']==='Aktif'?'selected':'' ?>>Aktif</option><option <?= $unit['status']==='Tidak Aktif'?'selected':'' ?>>Tidak Aktif</option></select></div><div class="form-group full"><button class="btn-submit">Simpan Perubahan</button> <a class="btn-small" href="unit-usaha.php">Batal</a></div></form></div><?php require '_footer.php';?>
