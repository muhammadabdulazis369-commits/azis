<?php

require_once "../config/config.php";

$pendapatan = $pdo->query("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pendapatan
    WHERE tanggal >= MAKEDATE(YEAR(CURDATE()), 1) AND tanggal < MAKEDATE(YEAR(CURDATE()) + 1, 1)
")->fetchColumn();

$pengeluaran = $pdo->query("
    SELECT COALESCE(SUM(jumlah),0)
    FROM pengeluaran
    WHERE tanggal >= MAKEDATE(YEAR(CURDATE()), 1) AND tanggal < MAKEDATE(YEAR(CURDATE()) + 1, 1)
")->fetchColumn();

$unitAktif = $pdo->query("
    SELECT COUNT(*) FROM unit_usaha
    WHERE status='Aktif'
")->fetchColumn();

$units = $pdo->query("
    SELECT * FROM unit_usaha
    WHERE status='Aktif'
    ORDER BY id DESC
    LIMIT 4
")->fetchAll();

$jumlahTransaksi = (int)$pdo->query("SELECT (SELECT COUNT(*) FROM pendapatan) + (SELECT COUNT(*) FROM pengeluaran)")->fetchColumn();


require "_header.php";

?>

<section class="hero-section">

    <div class="hero-background"></div>

    <div class="hero-content">

        <span class="hero-label">
            BUMDes Sejahtera Mandiri
        </span>

        <h1>
            Bersama Membangun<br>
            Desa yang <em>Lebih Maju</em>
        </h1>

        <p>
            BUMDes Sejahtera Mandiri hadir untuk menggerakkan
            ekonomi desa, meningkatkan kesejahteraan masyarakat,
            dan mewujudkan desa mandiri.
        </p>

        <div class="hero-buttons">

            <a href="profil.php" class="hero-button primary">
                <i class="fa-solid fa-arrow-right"></i>
                Pelajari Lebih Lanjut
            </a>

            <a href="laporan.php" class="hero-button secondary">
                <i class="fa-solid fa-chart-column"></i>
                Lihat Laporan
            </a>

        </div>

    </div>

    <div class="hero-decoration">
        Desa Sejahtera<br>
        Bersama BUMDes
    </div>

</section>

<section class="statistics-section">

    <div class="stat-box">

        <div class="stat-icon">
            <i class="fa-solid fa-coins"></i>
        </div>

        <div>
            <h2><?= rupiah($pendapatan) ?></h2>
            <p>Total Pendapatan Tahun Ini</p>
        </div>

    </div>

    <div class="stat-box">

        <div class="stat-icon">
            <i class="fa-solid fa-shop"></i>
        </div>

        <div>
            <h2><?= $unitAktif ?></h2>
            <p>Unit Usaha Aktif</p>
        </div>

    </div>

    <div class="stat-box">

        <div class="stat-icon">
            <i class="fa-solid fa-receipt"></i>
        </div>

        <div>
            <h2><?= (int)$jumlahTransaksi ?></h2>
            <p>Transaksi Tercatat</p>
        </div>

    </div>

    <div class="stat-box">

        <div class="stat-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div>
            <h2><?= rupiah($pengeluaran) ?></h2>
            <p>Total Pengeluaran Tahun Ini</p>
        </div>

    </div>

</section>

<main class="homepage-container">

    <section class="homepage-top">

        <div class="about-card">

            <div class="about-content">

                <h2>Tentang BUMDes</h2>

                <p>
                    BUMDes Sejahtera Mandiri adalah badan usaha
                    milik desa yang dikelola secara profesional
                    untuk mengoptimalkan potensi desa, memberikan
                    manfaat ekonomi bagi masyarakat, dan mendorong
                    pembangunan desa yang berkelanjutan.
                </p>

                <a href="profil.php" class="small-button">
                    Selengkapnya
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

            <div class="about-photo"></div>

        </div>

        <div class="business-card">

            <div class="section-heading">

                <h2>Unit Usaha Kami</h2>

                <a href="unit-usaha.php">
                    Lihat Semua
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

            <div class="business-grid">

                <?php foreach($units as $unit): ?>

                <a href="unit-usaha.php" class="business-item">

                    <div class="business-icon">

                        <?php
                        $icon = "fa-shop";

                        if ($unit["kategori"] == "Pertanian") {
                            $icon = "fa-leaf";
                        } elseif ($unit["kategori"] == "Pariwisata") {
                            $icon = "fa-mountain";
                        } elseif ($unit["kategori"] == "Keuangan") {
                            $icon = "fa-cart-shopping";
                        }
                        ?>

                        <i class="fa-solid <?= $icon ?>"></i>

                    </div>

                    <h3><?= e($unit["nama_unit"]) ?></h3>

                </a>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

    <section class="homepage-bottom">

        <div class="latest-news">

            <div class="section-heading">
                <h2>Berita Terbaru</h2>
                <a href="profil.php">Lihat Semua →</a>
            </div>

            <div class="information-card">
                <h3>Informasi dan Kegiatan BUMDes</h3>
                <p>Informasi kegiatan resmi akan ditampilkan setelah pengurus mengelola dan mempublikasikannya melalui sistem.</p>
                <a href="unit-usaha.php" class="small-button">Lihat Unit Usaha <i class="fa-solid fa-arrow-right"></i></a>
            </div>

        </div>

        <div class="homepage-cta">

            <i class="fa-solid fa-chart-line"></i>

            <div>
                <h2>
                    Mari Bersama Majukan Desa
                    Melalui BUMDes
                </h2>

                <p>
                    Bersinergi, Berkarya, dan Sejahtera
                </p>
            </div>

            <a href="#kontak">
                Hubungi Kami
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </section>

</main>

<section class="gallery-section">

    <h2>Potensi Desa Kami</h2>

    <div class="gallery-grid">

        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=900" alt="Pertanian desa">

        <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?w=900" alt="Lingkungan desa">

        <img src="https://images.unsplash.com/photo-1470770841072-f978cf4d019e?w=900" alt="Wisata alam">

    </div>

</section>

<div id="kontak"></div>

<?php require "_footer.php"; ?>
