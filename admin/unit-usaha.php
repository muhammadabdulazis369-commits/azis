<?php
require_once "../config/config.php";
adminLogin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    if (isset($_POST['hapus_id'])) {
        $id=(int)$_POST['hapus_id'];
        $del=$pdo->prepare('DELETE FROM unit_usaha WHERE id=?'); $del->execute([$id]);
        if($del->rowCount()) auditLog($pdo,'HAPUS_UNIT','ID unit usaha: '.$id);
        header('Location: unit-usaha.php?deleted=1'); exit;
    }

    if (isset($_POST["ubah_status"])) {
        $id = (int) $_POST["ubah_status"];
        $stmt = $pdo->prepare("UPDATE unit_usaha SET status = CASE WHEN status='Aktif' THEN 'Tidak Aktif' ELSE 'Aktif' END WHERE id=?");
        $stmt->execute([$id]);
        auditLog($pdo, 'UBAH_STATUS_UNIT', 'ID unit usaha: ' . $id);
        header("Location: unit-usaha.php?status=1");
        exit;
    }

    if (isset($_POST["tambah"])) {

    $nama = trim($_POST["nama_unit"]);
    $kategori = $_POST["kategori"];
    $deskripsi = trim($_POST["deskripsi"]);
    $alamat = trim($_POST["alamat"]);

    if ($nama === '' || textLength($nama) > 100 || trim($kategori) === '') {
        header('Location: unit-usaha.php?invalid=1'); exit;
    }
    $stmt = $pdo->prepare("
        INSERT INTO unit_usaha
        (nama_unit,kategori,deskripsi,alamat)
        VALUES (?,?,?,?)
    ");

    $stmt->execute([
        $nama,
        $kategori,
        $deskripsi,
        $alamat
    ]);

    auditLog($pdo, 'TAMBAH_UNIT', 'Nama unit: ' . $nama);
    header("Location: unit-usaha.php?success=1");
    exit;
}

}


$data = $pdo->query("
    SELECT * FROM unit_usaha
    ORDER BY id DESC
")->fetchAll();

require "_header.php";
?>

<div class="page-heading">
    <div>
        <h1>Unit Usaha</h1>
        <p>Kelola seluruh unit usaha BUMDes.</p>
    </div>
</div>

<?php if(isset($_GET['invalid'])): ?><div class="alert">Nama dan kategori unit usaha wajib diisi dengan benar.</div><?php endif; ?>
<?php if(isset($_GET['updated'])): ?><div class="alert-success">Unit usaha berhasil diperbarui.</div><?php endif; ?>
<?php if(isset($_GET['status'])): ?><div class="alert-success">Status unit usaha berhasil diperbarui.</div><?php endif; ?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">Unit usaha berhasil dihapus.</div><?php endif; ?>
<?php if(isset($_GET["success"])): ?>
<div class="alert-success">Unit usaha berhasil ditambahkan.</div>
<?php endif; ?>

<div class="content-panel">

    <div class="panel-heading">
        <h3>Tambah Unit Usaha</h3>
    </div>

    <form method="POST" class="form-grid">
        <?= csrfField() ?>

        <div class="form-group">
            <label>Nama Unit Usaha</label>
            <input type="text" name="nama_unit" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori" required>
                <option value="">Pilih kategori</option>
                <option>Keuangan</option>
                <option>Pertanian</option>
                <option>Perdagangan</option>
                <option>Pariwisata</option>
                <option>Jasa</option>
            </select>
        </div>

        <div class="form-group full">
            <label>Deskripsi</label>
            <textarea name="deskripsi"></textarea>
        </div>

        <div class="form-group full">
            <label>Alamat Unit Usaha</label>
            <textarea name="alamat"></textarea>
        </div>

        <div class="form-group full">
            <button type="submit" name="tambah" class="btn-submit">
                Tambahkan Unit Usaha
            </button>
        </div>

    </form>

</div>

<div class="content-panel">

    <div class="panel-heading">
        <h3>Daftar Unit Usaha</h3>
        <input type="search" id="searchTable"
        placeholder="Cari unit usaha...">
    </div>

    <div class="table-responsive">

        <table class="data-table" id="tableData">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Unit</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; foreach($data as $row): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($row["nama_unit"]) ?></td>
                    <td><?= e($row["kategori"]) ?></td>
                    <td><?= e($row["deskripsi"]) ?></td>
                    <td><?= e($row["alamat"]) ?></td>

                    <td>
                        <span class="status-badge">
                            <?= e($row["status"]) ?>
                        </span>
                    </td>

                    <td>
                        <a class="btn-small" href="edit-unit.php?id=<?= (int)$row['id'] ?>">Edit</a>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Ubah status unit usaha?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="ubah_status" value="<?= (int)$row['id'] ?>">
                            <button type="submit" class="btn-small">Ubah Status</button>
                        </form>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Hapus unit usaha? Transaksi terkait tetap disimpan tanpa relasi unit.')">
                            <?= csrfField() ?><input type="hidden" name="hapus_id" value="<?= (int)$row['id'] ?>">
                            <button type="submit" class="btn-small">Hapus</button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require "_footer.php"; ?>
