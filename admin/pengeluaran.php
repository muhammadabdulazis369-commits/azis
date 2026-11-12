<?php
require_once "../config/config.php";
adminLogin();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if (isset($_POST['hapus_id'])) {
        $id = filter_var($_POST['hapus_id'], FILTER_VALIDATE_INT);
        if ($id && $id > 0) {
            $stmt = $pdo->prepare('DELETE FROM pengeluaran WHERE id = ?');
            $stmt->execute([$id]);
            if ($stmt->rowCount()) auditLog($pdo, 'HAPUS_PG', 'ID transaksi: ' . $id);
        }
        redirect('pengeluaran.php?deleted=1');
    }
    if (isset($_POST['simpan'])) {
        $tanggalInput = trim($_POST['tanggal'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $unitRaw = $_POST['unit_usaha_id'] ?? '';
        $unit = $unitRaw === '' ? null : filter_var($unitRaw, FILTER_VALIDATE_INT);
        $keterangan = trim($_POST['keterangan'] ?? '');
        $jumlah = trim($_POST['jumlah'] ?? '');
        if (!validDate($tanggalInput) || $tanggalInput > date('Y-m-d')) {
            $error = 'Tanggal tidak valid atau tidak boleh melebihi hari ini.';
        } elseif ($kategori === '' || textLength($kategori) > 100 || $keterangan === '' || textLength($keterangan) > 2000) {
            $error = 'Kategori dan keterangan wajib diisi (maksimal 2.000 karakter untuk keterangan).';
        } elseif (!validMoney($jumlah)) {
            $error = 'Nominal harus lebih dari nol dan maksimal 2 angka desimal.';
        } elseif ($unitRaw !== '' && (!$unit || $unit < 1)) {
            $error = 'Unit usaha tidak valid.';
        } else {
            if ($unit !== null) {
                $check = $pdo->prepare("SELECT id FROM unit_usaha WHERE id=? AND status='Aktif'");
                $check->execute([$unit]);
                if (!$check->fetchColumn()) $error = 'Unit usaha tidak ditemukan atau tidak aktif.';
            }
            if ($error === '') {
                $kode = 'PG-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
                $stmt = $pdo->prepare('INSERT INTO pengeluaran (kode_transaksi,tanggal,kategori,unit_usaha_id,keterangan,jumlah) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$kode, $tanggalInput, $kategori, $unit, $keterangan, $jumlah]);
                auditLog($pdo, 'TAMBAH_PG', 'Kode ' . $kode . ', nominal ' . $jumlah);
                redirect('pengeluaran.php?success=1');
            }
        }
    }
}
$units = $pdo->query("SELECT id,nama_unit FROM unit_usaha WHERE status='Aktif' ORDER BY nama_unit")->fetchAll();
$totalRecords = (int)$pdo->query('SELECT COUNT(*) FROM pengeluaran')->fetchColumn();
$perPage = 50;
$page = max(1, (int)($_GET['page'] ?? 1));
$totalPages = max(1, (int)ceil($totalRecords / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$list = $pdo->prepare("SELECT t.*,u.nama_unit FROM pengeluaran t LEFT JOIN unit_usaha u ON t.unit_usaha_id=u.id ORDER BY t.tanggal DESC,t.id DESC LIMIT :limit OFFSET :offset");
$list->bindValue(':limit', $perPage, PDO::PARAM_INT);
$list->bindValue(':offset', $offset, PDO::PARAM_INT);
$list->execute();
$data = $list->fetchAll();
$total = $pdo->query('SELECT COALESCE(SUM(jumlah),0) FROM pengeluaran')->fetchColumn();
require '_header.php';
?>
<div class="page-heading"><div><h1>Data Pengeluaran</h1><p>Kelola dan pantau seluruh pengeluaran BUMDes. Menampilkan 50 transaksi per halaman.</p></div></div>
<?php if(isset($_GET['success'])): ?><div class="alert-success">Pengeluaran berhasil ditambahkan.</div><?php endif; ?>
<?php if(isset($_GET['updated'])): ?><div class="alert-success">Transaksi berhasil diperbarui.</div><?php endif; ?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">Permintaan penghapusan transaksi diproses.</div><?php endif; ?>
<?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<div class="summary-box"><div><span>Total Pengeluaran</span><h2><?= rupiah($total) ?></h2></div><i class="fa-solid fa-money-bill-transfer"></i></div>
<div class="content-panel"><div class="panel-heading"><h3>Tambah Pengeluaran</h3></div>
<form method="POST" class="form-grid"><?= csrfField() ?>
<div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required></div>
<div class="form-group"><label>Kategori</label><select name="kategori" required><option value="">Pilih kategori</option><option value="Operasional">Operasional</option><option value="Pemeliharaan">Pemeliharaan</option><option value="Gaji Pegawai">Gaji Pegawai</option><option value="Pembelian Barang">Pembelian Barang</option><option value="Pembangunan">Pembangunan</option><option value="Lainnya">Lainnya</option></select></div>
<div class="form-group"><label>Unit Usaha</label><select name="unit_usaha_id"><option value="">Tidak terkait unit</option><?php foreach($units as $unit): ?><option value="<?= (int)$unit['id'] ?>"><?= e($unit['nama_unit']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Nominal (Rp)</label><input type="number" name="jumlah" min="0.01" step="0.01" max="9999999999999.99" placeholder="Contoh: 1500000" required></div>
<div class="form-group full"><label>Keterangan</label><textarea name="keterangan" maxlength="2000" placeholder="Jelaskan sumber/tujuan transaksi..." required></textarea></div>
<div class="form-group full"><button type="submit" name="simpan" class="btn-submit"><i class="fa-solid fa-plus"></i> Simpan Pengeluaran</button></div>
</form></div>
<div class="content-panel"><div class="panel-heading"><h3>Riwayat Pengeluaran</h3><input type="search" id="searchTable" placeholder="Cari transaksi..."></div>
<div class="table-responsive"><table class="data-table" id="tableData"><thead><tr><th>No</th><th>Kode</th><th>Tanggal</th><th>Kategori</th><th>Unit Usaha</th><th>Keterangan</th><th>Jumlah</th><th>Aksi</th></tr></thead><tbody>
<?php $no=1; foreach($data as $row): ?><tr><td><?= $no++ ?></td><td><?= e($row['kode_transaksi']) ?></td><td><?= tanggal($row['tanggal']) ?></td><td><?= e($row['kategori']) ?></td><td><?= e($row['nama_unit'] ?? '-') ?></td><td><?= e($row['keterangan']) ?></td><td><?= rupiah($row['jumlah']) ?></td><td><a class="btn-small" href="edit-transaksi.php?jenis=pengeluaran&id=<?= (int)$row['id'] ?>">Edit</a> <form method="POST" style="display:inline" onsubmit="return confirm('Hapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')"><?= csrfField() ?><input type="hidden" name="hapus_id" value="<?= (int)$row['id'] ?>"><button type="submit" class="btn-small">Hapus</button></form></td></tr><?php endforeach; ?>
<?php if(!$data): ?><tr><td colspan="8">Belum ada data pengeluaran.</td></tr><?php endif; ?>
</tbody></table></div></div>
<div class="pagination" style="display:flex;gap:8px;align-items:center;justify-content:flex-end;margin:18px 0;flex-wrap:wrap;">
    <span>Halaman <?= $page ?> dari <?= $totalPages ?> (<?= $totalRecords ?> data)</span>
    <?php if($page > 1): ?><a class="btn-small" href="?page=<?= $page-1 ?>">Sebelumnya</a><?php endif; ?>
    <?php if($page < $totalPages): ?><a class="btn-small" href="?page=<?= $page+1 ?>">Berikutnya</a><?php endif; ?>
</div>
<?php require '_footer.php'; ?>
