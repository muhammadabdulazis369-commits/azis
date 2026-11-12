<?php

require_once "../config/config.php";

$data = $pdo->query("
    SELECT * FROM unit_usaha
    WHERE status='Aktif'
    ORDER BY id DESC
")->fetchAll();

require "_header.php";

?>

<section class="inner-banner">

    <span>Potensi Ekonomi Desa</span>

    <h1>Unit Usaha Kami</h1>

    <p>Berbagai bidang usaha untuk kemajuan masyarakat.</p>

</section>

<main class="inner-container">

    <div class="public-business-grid">

        <?php foreach($data as $unit): ?>

        <article class="public-business-card">

            <div class="public-business-icon">
                <i class="fa-solid fa-shop"></i>
            </div>

            <span class="category-label">
                <?= e($unit["kategori"]) ?>
            </span>

            <h2><?= e($unit["nama_unit"]) ?></h2>

            <p><?= e($unit["deskripsi"]) ?></p>

            <div class="business-address">
                <i class="fa-solid fa-location-dot"></i>
                <?= e($unit["alamat"]) ?>
            </div>

        </article>

        <?php endforeach; ?>

        <?php if(!$data): ?>

            <div class="information-card">
                Belum ada unit usaha yang tersedia.
            </div>

        <?php endif; ?>

    </div>

</main>

<?php require "_footer.php"; ?>
