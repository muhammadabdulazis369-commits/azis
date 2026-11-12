<?php
require_once "../config/config.php";
adminLogin();

$pageTitle = "Dashboard";

$totalPendapatan = $pdo->query("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pendapatan
")->fetchColumn();

$totalPengeluaran = $pdo->query("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pengeluaran
")->fetchColumn();

$totalUnit = $pdo->query("
    SELECT COUNT(*) FROM unit_usaha
    WHERE status='Aktif'
")->fetchColumn();

$saldo = $totalPendapatan - $totalPengeluaran;

$totalTransaksi = $pdo->query("
    SELECT
    (SELECT COUNT(*) FROM pendapatan) +
    (SELECT COUNT(*) FROM pengeluaran)
")->fetchColumn();

$pendapatanTerbaru = $pdo->query("
    SELECT * FROM pendapatan
    ORDER BY tanggal DESC
    LIMIT 5
")->fetchAll();

$pengeluaranTerbaru = $pdo->query("
    SELECT * FROM pengeluaran
    ORDER BY tanggal DESC
    LIMIT 5
")->fetchAll();

require "_header.php";
?>

<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Selamat datang di sistem informasi BUMDes Sejahtera Mandiri.</p>
    </div>

    <div class="date-now">
        <?= date("d F Y") ?>
    </div>
</div>

<div class="dashboard-cards">

    <div class="dashboard-card">
        <div class="card-icon brown">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <p>Total Pendapatan</p>
            <h2><?= rupiah($totalPendapatan) ?></h2>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon red">
            <i class="fa-solid fa-money-bill-transfer"></i>
        </div>
        <div>
            <p>Total Pengeluaran</p>
            <h2><?= rupiah($totalPengeluaran) ?></h2>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon gold">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <p>Saldo Bersih</p>
            <h2><?= rupiah($saldo) ?></h2>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-icon dark">
            <i class="fa-solid fa-shop"></i>
        </div>
        <div>
            <p>Unit Usaha Aktif</p>
            <h2><?= $totalUnit ?> Unit</h2>
        </div>
    </div>

</div>

<div class="dashboard-cards secondary-cards">

    <div class="dashboard-card">
        <div class="card-icon gold">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
            <p>Total Transaksi</p>
            <h2><?= $totalTransaksi ?></h2>
        </div>
    </div>

</div>

<div class="dashboard-grid">

    <div class="content-panel">

        <div class="panel-heading">
            <h3>Pendapatan Terbaru</h3>
            <a href="pendapatan.php">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="data-table">

                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($pendapatanTerbaru as $data): ?>

                    <tr>
                        <td><?= e($data["kode_transaksi"]) ?></td>
                        <td><?= tanggal($data["tanggal"]) ?></td>
                        <td><?= e($data["kategori"]) ?></td>
                        <td class="text-income">
                            <?= rupiah($data["jumlah"]) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (!$pendapatanTerbaru): ?>
                    <tr>
                        <td colspan="4">Belum ada pendapatan.</td>
                    </tr>
                <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>

    <div class="content-panel">

        <div class="panel-heading">
            <h3>Pengeluaran Terbaru</h3>
            <a href="pengeluaran.php">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="data-table">

                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($pengeluaranTerbaru as $data): ?>

                    <tr>
                        <td><?= e($data["kode_transaksi"]) ?></td>
                        <td><?= tanggal($data["tanggal"]) ?></td>
                        <td class="text-expense">
                            <?= rupiah($data["jumlah"]) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (!$pengeluaranTerbaru): ?>
                    <tr>
                        <td colspan="3">Belum ada pengeluaran.</td>
                    </tr>
                <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>

</div>

<?php require "_footer.php"; ?>
