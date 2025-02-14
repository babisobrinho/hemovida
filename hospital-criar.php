<?php
    ob_start(); 
    include 'config/config.php';
    include 'partials/header.php';

    $pageTitle = "Adicionar Hospital";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => 'hospitais.php', 'active' => false],
        ['title' => 'Adicionar Hospital', 'url' => '#', 'active' => true]
    ];

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $nome_responsavel = $_POST['nome_responsavel'];
        $estado = isset($_POST['estado']) ? 1 : 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO hospitais (nome, endereco, telefone, email, nome_responsavel, estado) VALUES (:nome, :endereco, :telefone, :email, :nome_responsavel, :estado)");
            $stmt->execute([
                'nome' => $nome,
                'endereco' => $endereco,
                'telefone' => $telefone,
                'email' => $email,
                'nome_responsavel' => $nome_responsavel,
                'estado' => $estado
            ]);
            
            header("Location: hospitais.php?success=adicionado");
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Erro ao adicionar hospital: " . $e->getMessage();
        }
    }

    ob_end_flush();
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title">Adicionar Novo Hospital</h5>
            <form method="POST" action="">
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
                    <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
                    <label class="form-check-label" for="estado">Ativo</label>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="hospitais.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
