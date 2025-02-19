<?php

    include 'partials/header.php';

    $pageTitle = "Adicionar Hospital";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => 'hospitais.php', 'active' => false],
        ['title' => 'Adicionar Hospital', 'url' => '#', 'active' => true]
    ];

?>

    <div class="container p-4">
        <?php include 'partials/page-header.php'; ?>

        <div class="container d-flex justify-content-center align-items-start">
            <div class="card shadow p-4" style="width: 40rem;">
                <form method="POST" action="includes/store.php">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Hospital</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="nome_responsavel" class="form-label">Nome do Responsável</label>
                            <input type="text" class="form-control" id="nome_responsavel" name="nome_responsavel" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="estado" name="estado" value="1" checked>
                            <label class="form-check-label" for="estado">Ativo</label>
                        </div>
                        <input type="hidden" name="table" value="hospitais">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <a href="hospitais.php" class="btn btn-light border">Voltar</a>
                            <button type="submit" class="btn btn-success">Adicionar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
