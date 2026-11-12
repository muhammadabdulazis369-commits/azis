<?php
require_once "../config/config.php";
adminLogin();
$jenis = $_GET['jenis'] ?? $_POST['jenis'] ?? '';
$allowed = ['pendapatan', 'pengeluaran'];
if (!in_array($jenis, $allowed, true)) { http_response_code(400); exit('Jenis transaksi tidak valid.'); }
$id = filter_var($_GET['id'] ?? $_POST['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('ID transaksi tidak valid.'); }
$stmt = $pdo->prepare("SELECT * FROM {$jenis} WHERE id=?"); $stmt->execute([$id]); $data = $stmt->fetch();
if (!$data) { http_response_code(404); exit('Transaksi tidak ditemukan.'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $tanggalInput = trim($_POST['tanggal'] ?? ''); $kategori = trim($_POST['kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? ''); $jumlah = trim($_POST['jumlah'] ?? '');
    $unitRaw = $_POST['unit_usaha_id'] ?? ''; $unit = $unitRaw === '' ? null : filter_var($unitRaw, FILTER_VALIDATE_INT);
    if (!validDate($tanggalInput) || $tanggalInput > date('Y-m-d')) $error = 'Tanggal tidak valid.';
    elseif ($kategori === '' || textLength($kategori) > 100 || $keterangan === '' || textLength($keterangan) > 2000) $error = 'Kategori dan keterangan wajib diisi.';
    elseif (!validMoney($jumlah)) $error = 'Nominal tidak valid.';
    elseif ($unitRaw !== '' && (!$unit || $unit < 1)) $error = 'Unit usaha tidak valid.';
    else {
        if ($unit !== null) { $check=$pdo->prepare('SELECT id FROM unit_usaha WHERE id=?'); $check->execute([$unit]); if (!$check->fetchColumn()) $error='Unit usaha tidak ditemukan.'; }
        if (!$error) {
            $update=$pdo->prepare("UPDATE {$jenis} SET tanggal=?,kategori=?,unit_usaha_id=?,keterangan=?,jumlah=? WHERE id=?");
            $update->execute([$tanggalInput,$kategori,$unit,$keterangan,$jumlah,$id]);
            auditLog($pdo, 'EDIT_' . strtoupper($jenis), 'ID transaksi ' . $id . ', nominal baru ' . $jumlah);
            redirect($jenis . '.php?updated=1');
        }
    }
    $data=array_merge($data,['tanggal'=>$tanggalInput,'kategori'=>$kategori,'keterangan'=>$keterangan,'jumlah'=>$jumlah,'unit_usaha_id'=>$unit]);
}
$units=$pdo->query('SELECT id,nama_unit FROM unit_usaha ORDER BY nama_unit')->fetchAll();
require '_header.php';
?>
<div class="page-heading"><div><h1>Edit <?= e(ucfirst($jenis)) ?></h1><p>Kode transaksi: <?= e($data['kode_transaksi']) ?></p></div></div>
<div class="content-panel"><?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="POST" class="form-grid"><?= csrfField() ?><input type="hidden" name="jenis" value="<?= e($jenis) ?>"><input type="hidden" name="id" value="<?= (int)$id ?>">
<div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" max="<?= date('Y-m-d') ?>" value="<?= e($data['tanggal']) ?>" required></div>
<div class="form-group"><label>Kategori</label><input type="text" name="kategori" maxlength="100" value="<?= e($data['kategori']) ?>" required></div>
<div class="form-group"><label>Unit Usaha</label><select name="unit_usaha_id"><option value="">Tidak terkait unit</option><?php foreach($units as $unit): ?><option value="<?= (int)$unit['id'] ?>" <?= (int)$data['unit_usaha_id']===(int)$unit['id']?'selected':'' ?>><?= e($unit['nama_unit']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Jumlah (Rp)</label><input type="number" name="jumlah" min="0.01" step="0.01" value="<?= e($data['jumlah']) ?>" required></div>
<div class="form-group full"><label>Keterangan</label><textarea name="keterangan" maxlength="2000" required><?= e($data['keterangan']) ?></textarea></div>
<div class="form-group full"><button type="submit" class="btn-submit">Simpan Perubahan</button> <a class="btn-small" href="<?= e($jenis) ?>.php">Batal</a></div></form></div>
<?php require '_footer.php'; ?>
