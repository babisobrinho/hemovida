<?php
    include 'partials/header.php';

    $pageTitle = "Transfusões de Sangues";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Transfusões', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
</div>

<?php
    include 'partials/footer.php';
?>