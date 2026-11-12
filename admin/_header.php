<?php
require_once "../config/config.php";
adminLogin();

$halaman = basename($_SERVER["PHP_SELF"]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin BUMDes Sejahtera Mandiri</title>

<link rel="stylesheet" href="../style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="admin-body">

<div class="admin-layout">

<aside class="sidebar">

    <div class="sidebar-brand">
        <i class="fa-solid fa-seedling"></i>

        <div>
            <h2>BUMDes</h2>
            <small>Sejahtera Mandiri</small>
        </div>
    </div>

    <div class="sidebar-menu">

        <p class="menu-label">MENU UTAMA</p>

        <a href="dashboard.php"
        class="<?= $halaman == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <p class="menu-label">KEUANGAN</p>

        <a href="pendapatan.php"
        class="<?= $halaman == 'pendapatan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-arrow-trend-up"></i>
            Pendapatan
        </a>

        <a href="pengeluaran.php"
        class="<?= $halaman == 'pengeluaran.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-arrow-trend-down"></i>
            Pengeluaran
        </a>

        <a href="laporan.php"
        class="<?= $halaman == 'laporan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            Laporan
        </a>

        <p class="menu-label">PENGELOLAAN</p>

        <a href="unit-usaha.php"
        class="<?= $halaman == 'unit-usaha.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-shop"></i>
            Unit Usaha
        </a>

        <a href="audit-log.php" class="<?= $halaman == 'audit-log.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i>
            Aktivitas Admin
        </a>

        <a href="pengaturan.php" class="<?= $halaman == 'pengaturan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-key"></i>
            Ganti Password
        </a>

        <a href="../masyarakat/index.php" target="_blank" rel="noopener">
            <i class="fa-solid fa-globe"></i>
            Lihat Website
        </a>

        <form method="POST" action="logout.php" class="sidebar-logout" onsubmit="return confirm('Yakin ingin keluar?')">
            <?= csrfField() ?>
            <button type="submit">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </button>
        </form>

    </div>

</aside>

<main class="admin-main">

<header class="admin-topbar">

    <div>
        <h3>Panel Administrator</h3>
        <small>Pengelolaan BUMDes Sejahtera Mandiri</small>
    </div>

    <div class="admin-user">
        <i class="fa-solid fa-circle-user"></i>
        <?= e($_SESSION["admin_nama"]) ?>
    </div>

</header>

<div class="admin-content">
