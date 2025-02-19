<?php

    include 'partials/header.php';
    include 'includes/obter_registo.php';

    $hospital = $record;

    $pageTitle = "Editar Hospital";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => 'hospitais.php', 'active' => false],
        ['title' => 'Editar Hospital', 'url' => '#', 'active' => true]
    ];

?>

    <div class="container p-4"> 
        <?php include 'partials/page-header.php'; ?>
        <div class="container d-flex justify-content-center align-items-start">
            <div class="card shadow p-4" style="width: 40rem;">
                <form method="POST" action="includes/update.php">
                    <div class="card-body">
                        <input type="hidden" name="table" value="hospitais">
                        <input type="hidden" name="id" value="<?php echo $hospital['id']; ?>">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Hospital</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $hospital['nome']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo $hospital['endereco']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $hospital['telefone']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $hospital['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nome_responsavel" class="form-label">Responsável</label>
                            <input type="text" class="form-control" id="nome_responsavel" name="nome_responsavel" value="<?php echo $hospital['nome_responsavel']; ?>" required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1" <?php echo $hospital['estado'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="estado">Ativo</label>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="hospitais.php" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
