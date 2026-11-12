<?php

require_once "../config/config.php";

$tahun = (int)($_GET["tahun"] ?? date("Y"));
if ($tahun < 2000 || $tahun > 2100) { http_response_code(400); exit('Tahun laporan tidak valid.'); }
$awal = sprintf('%04d-01-01', $tahun);
$akhir = sprintf('%04d-01-01', $tahun + 1);

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pendapatan
    WHERE tanggal>=? AND tanggal<?
");

$stmt->execute([$awal, $akhir]);

$pendapatan = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pengeluaran
    WHERE tanggal>=? AND tanggal<?
");

$stmt->execute([$awal, $akhir]);

$pengeluaran = $stmt->fetchColumn();

$saldo = $pendapatan - $pengeluaran;

require "_header.php";

?>

<section class="inner-banner">

    <span>Informasi Publik</span>

    <h1>Laporan Keuangan BUMDes</h1>

    <p>Rekapitulasi keuangan BUMDes Sejahtera Mandiri.</p>

</section>

<main class="inner-container">

    <div class="information-card">

        <form method="GET" class="filter-form">

            <div class="form-group">
                <label>Tahun Laporan</label>

                <input type="number" name="tahun"
                value="<?= $tahun ?>" min="2000" max="2100">
            </div>

            <button class="btn-submit">
                Tampilkan Laporan
            </button>

        </form>

    </div>

    <div class="report-summary">

        <div class="report-box">
            <span>Total Pendapatan</span>
            <h2><?= rupiah($pendapatan) ?></h2>
        </div>

        <div class="report-box">
            <span>Total Pengeluaran</span>
            <h2><?= rupiah($pengeluaran) ?></h2>
        </div>

        <div class="report-box">
            <span>Saldo Bersih</span>
            <h2><?= rupiah($saldo) ?></h2>
        </div>

    </div>

    <div class="information-card">

        <h2>Ringkasan Laporan Tahun <?= $tahun ?></h2>

        <table class="data-table">

            <tbody>

                <tr>
                    <th>Total Pendapatan</th>
                    <td><?= rupiah($pendapatan) ?></td>
                </tr>

                <tr>
                    <th>Total Pengeluaran</th>
                    <td><?= rupiah($pengeluaran) ?></td>
                </tr>

                <tr>
                    <th>Saldo Bersih</th>
                    <td><strong><?= rupiah($saldo) ?></strong></td>
                </tr>

            </tbody>

        </table>

        <p class="report-note">
            Laporan ini dihitung berdasarkan seluruh data transaksi
            yang tercatat pada sistem BUMDes.
        </p>

    </div>

</main>

<?php require "_footer.php"; ?>

