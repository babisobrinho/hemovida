<?php
    include 'config/config.php';
    include 'partials/header.php';

    $pageTitle = "Hospitais Parceiros";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => '#', 'active' => true]
    ];

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $stmt = $pdo->prepare("SELECT * FROM hospitais WHERE nome LIKE :search");
    $stmt->execute(['search' => "%$search%"]);
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
            header("Location: " . $_SERVER['PHP_SELF'] . "?search=" . urlencode($search));
            exit;
        }
    }
?>

<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="fw-bold"><?= $pageTitle ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-dark">Dashboard</a></li>
                <li class="breadcrumb-item active text-danger" aria-current="page">Hospitais</li>
            </ol>
        </nav>
    </div>

    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Pesquisar hospital..." value="<?= htmlspecialchars($search) ?>">
    </div>

    <div class="row mt-4">
    <?php if (count($hospitais) > 0): ?>
        <?php foreach ($hospitais as $hospital): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 d-flex flex-column">
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
                            <a href="editar_hospital.php?id=<?= $hospital['id'] ?>" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>
                            <a href="excluir_hospital.php?id=<?= $hospital['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este hospital?');"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-danger">Nenhum hospital cadastrado.</p>
    <?php endif; ?>
</div>
