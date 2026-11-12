<?php
require_once "../config/config.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BUMDes Sejahtera Mandiri</title>

<link rel="stylesheet" href="../style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

</head>

<body>

<header class="public-header">

    <a href="index.php" class="public-brand">

        <div class="public-logo">
            <i class="fa-solid fa-seedling"></i>
        </div>

        <div>
            <h2>BUMDes</h2>
            <h2>Sejahtera Mandiri</h2>
            <small>Desa Maju, Masyarakat Sejahtera</small>
        </div>

    </a>

    <button class="mobile-menu" id="mobileMenu">
        <i class="fa-solid fa-bars"></i>
    </button>

    <nav class="public-nav" id="publicNav">

        <a href="index.php">
            <i class="fa-solid fa-house"></i> Beranda
        </a>

        <a href="profil.php">Profil</a>

        <a href="unit-usaha.php">Unit Usaha</a>

        <a href="laporan.php">Laporan</a>

        <a href="pendapatan.php">Pendapatan</a>

        <a href="pengeluaran.php">Pengeluaran</a>

    </nav>

    <form action="laporan.php" method="GET" class="public-search">

        <input type="search" name="q"
        placeholder="Cari informasi...">

        <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

    </form>

</header>
