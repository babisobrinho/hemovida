<?php
    $pageTitle = $pageTitle ?? "";
    $breadcrumbItems = $breadcrumbItems ?? [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Página Atual', 'url' => '#', 'active' => true]
    ];
?>

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0"><?= $pageTitle ?></h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <?php foreach ($breadcrumbItems as $index => $item): ?>
                    <li class="breadcrumb-item <?= isset($item['active']) && $item['active'] ? 'text-danger fw-semibold' : 'text-dark' ?>">
                        <?php if (isset($item['url']) && !$item['active']): ?>
                            <a href="<?= $item['url'] ?>" class="text-dark text-decoration-none"><?= $item['title'] ?></a>
                        <?php else: ?>
                            <?= $item['title'] ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</div>
