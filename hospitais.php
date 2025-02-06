<?php
    ob_start(); // Inicia o buffer de saída para evitar problemas com o header()
    include 'config/config.php';
    include 'partials/header.php';

    $pageTitle = "Hospitais Parceiros";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => '#', 'active' => true]
    ];

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta sem filtro de busca
    $stmt = $pdo->prepare("SELECT * FROM hospitais");
    $stmt->execute();
    $hospitais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Função para alterar o estado de um hospital
    if (isset($_GET['toggle_id'])) {
        $id = $_GET['toggle_id'];
        $stmt = $pdo->prepare("SELECT estado FROM hospitais WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $hospital = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hospital) {
            $novoEstado = $hospital['estado'] == 1 ? 0 : 1;
            $updateStmt = $pdo->prepare("UPDATE hospitais SET estado = :estado WHERE id = :id");
            $updateStmt->execute(['estado' => $novoEstado, 'id' => $id]);

            // Redireciona de volta para a página de hospitais após a alteração
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=estado_alterado");
            exit;
        }
    }

    ob_end_flush(); // Libera o buffer de saída
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <!-- ✅ Adicionando a mensagem de sucesso -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
                if ($_GET['success'] == 'editado') {
                    echo "<strong>Sucesso!</strong> As alterações do hospital foram salvas.";
                } elseif ($_GET['success'] == 'estado_alterado') {
                    echo "<strong>Sucesso!</strong> O estado do hospital foi atualizado.";
                } elseif ($_GET['success'] == 'excluido') {
                    echo "<strong>Hospital removido!</strong> O hospital foi excluído do sistema.";
                }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mt-4">
    <?php if (count($hospitais) > 0): ?>
        <?php foreach ($hospitais as $hospital): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 d-flex flex-column shadow-lg">
                    <div class="card-body flex-grow-1 d-flex flex-column">
                        <h5 class="card-title"> <?= $hospital['nome'] ?> </h5>
                        <p class="card-text flex-grow-1">
                            <strong>Endereço:</strong> <?= $hospital['endereco'] ?><br>
                            <strong>Telefone:</strong> <?= $hospital['telefone'] ?><br>
                            <strong>Email:</strong> <a href="mailto:<?= $hospital['email'] ?>"><?= $hospital['email'] ?></a><br>
                            <strong>Responsável:</strong> <?= $hospital['nome_responsavel'] ?><br>
                            <strong>Estado:</strong>
                            <a href="?toggle_id=<?= $hospital['id'] ?>" class="btn btn-sm <?= $hospital['estado'] == 1 ? 'btn-success' : 'btn-danger' ?>">
                                <?= $hospital['estado'] == 1 ? 'Ativo' : 'Inativo' ?>
                            </a>
                        </p>
                        <div class="d-flex justify-content-start gap-2">
                            <a href="hospital-editar.php?id=<?= $hospital['id'] ?>" class="btn btn-sm btn-dark">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="excluir_hospital.php?id=<?= $hospital['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este hospital?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-danger">Nenhum hospital cadastrado.</p>
    <?php endif; ?>
</div>



<!-- Footer -->
<?php include 'partials/footer.php'; ?>