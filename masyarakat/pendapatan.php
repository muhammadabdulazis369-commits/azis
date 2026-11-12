<?php

require_once "../config/config.php";

$tahun = (int)($_GET["tahun"] ?? date("Y"));
if ($tahun < 2000 || $tahun > 2100) { http_response_code(400); exit('Tahun laporan tidak valid.'); }
$awal = sprintf('%04d-01-01', $tahun);
$akhir = sprintf('%04d-01-01', $tahun + 1);

$stmt = $pdo->prepare("
    SELECT p.*,u.nama_unit
    FROM pendapatan p
    LEFT JOIN unit_usaha u
    ON p.unit_usaha_id=u.id
    WHERE p.tanggal>=? AND p.tanggal<?
    ORDER BY p.tanggal DESC
");

$stmt->execute([$awal, $akhir]);

$data = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pendapatan
    WHERE tanggal>=? AND tanggal<?
");

$stmt->execute([$awal, $akhir]);

$total = $stmt->fetchColumn();

require "_header.php";

?>

<section class="inner-banner">

    <span>Transparansi Keuangan</span>

    <h1>Informasi Pendapatan</h1>

    <p>Informasi pemasukan BUMDes Sejahtera Mandiri.</p>

</section>

<main class="inner-container">

    <div class="information-card">

        <h2>Total Pendapatan <?= $tahun ?></h2>

        <h1 class="money-display">
            <?= rupiah($total) ?>
        </h1>

        <form method="GET" class="filter-form">

            <div class="form-group">
                <label>Pilih Tahun</label>

                <input type="number" name="tahun"
                value="<?= $tahun ?>" min="2000" max="2100">
            </div>

            <button class="btn-submit">
                Tampilkan
            </button>

        </form>

    </div>

    <div class="information-card">

        <h2>Rincian Pendapatan</h2>

        <div class="table-responsive">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Unit Usaha</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>

                <tbody>

                <?php $no=1; foreach($data as $row): ?>

                    <tr>
                        <td><?= $no++ ?></td>

                        <td>
                            <?= e($row["kode_transaksi"]) ?>
                        </td>

                        <td>
                            <?= tanggal($row["tanggal"]) ?>
                        </td>

                        <td>
                            <?= e($row["kategori"]) ?>
                        </td>

                        <td>
                            <?= e($row["nama_unit"] ?? "-") ?>
                        </td>

                        <td>
                            <?= e($row["keterangan"]) ?>
                        </td>

                        <td>
                            <?= rupiah($row["jumlah"]) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if(!$data): ?>

                    <tr>
                        <td colspan="7">
                            Belum ada data pendapatan.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php require "_footer.php"; ?>
