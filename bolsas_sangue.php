<?php
    include 'partials/header.php';

    $pageTitle = "Bolsas de Sangues";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Inventário', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
</div>

<?php
    include 'partials/footer.php';
?>