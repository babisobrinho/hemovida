<?php
    ob_start(); // Inicia o buffer de saída para evitar problemas com o header()
    include 'config/config.php';
    include 'partials/header.php';

    $pageTitle = "Editar Hospital";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => 'hospitais.php', 'active' => false],
        ['title' => 'Editar Hospital', 'url' => '#', 'active' => true]
    ];

    // Verifica se o ID foi passado na URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die("<p class='text-danger text-center'>Erro: Nenhum hospital selecionado para edição.</p>");
    }

    $id = $_GET['id'];
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta os dados do hospital pelo ID
    $stmt = $pdo->prepare("SELECT * FROM hospitais WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $hospital = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hospital) {
        die("<p class='text-danger text-center'>Erro: Hospital não encontrado.</p>");
    }

    // Atualiza os dados do hospital se o formulário for enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = trim($_POST['nome']);
        $endereco = trim($_POST['endereco']);
        $telefone = trim($_POST['telefone']);
        $email = trim($_POST['email']);
        $nome_responsavel = trim($_POST['nome_responsavel']);
        $estado = isset($_POST['estado']) ? 1 : 0;

        // Verifica se todos os campos estão preenchidos
        if (empty($nome) || empty($endereco) || empty($telefone) || empty($email) || empty($nome_responsavel)) {
            die("<p class='text-danger text-center'>Erro: Todos os campos devem ser preenchidos.</p>");
        }

        try {
            $updateStmt = $pdo->prepare("UPDATE hospitais SET 
                nome = :nome, 
                endereco = :endereco, 
                telefone = :telefone, 
                email = :email, 
                nome_responsavel = :nome_responsavel, 
                estado = :estado 
                WHERE id = :id");

            $updateStmt->execute([
                'nome' => $nome,
                'endereco' => $endereco,
                'telefone' => $telefone,
                'email' => $email,
                'nome_responsavel' => $nome_responsavel,
                'estado' => $estado,
                'id' => $id
            ]);

            // **Redirecionamento correto**
            header("Location: hospitais.php?success=editado");
            exit();

        } catch (PDOException $e) {
            die("<p class='text-danger text-center'>Erro ao salvar alterações: " . $e->getMessage() . "</p>");
        }
    }

    ob_end_flush(); // Libera o buffer de saída
?>
<div class="container p-4"> 
<?php include 'partials/page-header.php'; ?>
<div class="container d-flex justify-content-center align-items-start vh-100">
    
    <div class="card shadow-lg p-4" style="width: 40rem;">
        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Hospital</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($hospital['nome']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco" value="<?= htmlspecialchars($hospital['endereco']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($hospital['telefone']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($hospital['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="nome_responsavel" class="form-label">Responsável</label>
                <input type="text" class="form-control" id="nome_responsavel" name="nome_responsavel" value="<?= htmlspecialchars($hospital['nome_responsavel']) ?>" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="estado" name="estado" <?= $hospital['estado'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="estado">Ativo</label>
            </div>

            <div class="d-flex justify-content-between">
                <a href="hospitais.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-danger">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
</div>
<!-- Footer -->
<?php include 'partials/footer.php'; ?>
