<?php

    include 'partials/header.php';
    include 'includes/db_functions.php';

    if (isset($_GET['toggle_id'])) {
        $hospitalId = $_GET['toggle_id'];
        if (toggleEstado($pdo, 'hospitais', $hospitalId)) {
            $_SESSION['alert_message'] = displayAlert('Estado do hospital alterado com sucesso!', 'sucesso', 'success');
            header("Location: hospitais.php");
            exit;
        } else {
            $_SESSION['alert_message'] = displayAlert('Erro ao tentar alterar o estado do hospital.', 'erro', 'danger');
        }
    }

    $stmt = $pdo->query("SELECT * FROM hospitais");
    $hospitais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pageTitle = "Hospitais Parceiros";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => '#', 'active' => true]
    ];

?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <?php
        if (isset($_SESSION['alert_message'])) {
            echo $_SESSION['alert_message'];
            unset($_SESSION['alert_message']);
        }
    ?>

    <div class="d-flex justify-content-start mb-3">
        <a href="hospital-criar.php" class="btn border-0 text-white" style="background-color: #202d3b;"><i class="fa-solid fa-plus"></i> Novo</a>
    </div>

    <div class="row mt-4">
    <?php if (count($hospitais) > 0): ?>
        <?php foreach ($hospitais as $hospital): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 d-flex flex-column shadow-lg">
                    <div class="card-body flex-grow-1 d-flex flex-column">
                        <h5 class="card-title mb-2"><?= $hospital['nome'] ?></h5>
                        <div class="flex-grow-1">
                            <p class="text-dark mb-0"><b>Endereço:</b> <?= $hospital['endereco'] ?></p>
                            <p class="text-dark mb-0"><b>Telefone:</b> <?= $hospital['telefone'] ?></p>
                            <p class="text-dark mb-0">
                                <b>Email:</b>
                                <a href="mailto:<?= $hospital['email'] ?>" class="text-decoration-none text-dark"><?= $hospital['email'] ?>
                                </a>
                            </p>
                            <p class="text-dark mb-0"><b>Responsável:</b> <?= $hospital['nome_responsavel'] ?></p>
                            <p class="text-dark mb-0">
                                <b>Estado:</b>
                                <a href="?toggle_id=<?= $hospital['id'] ?>" class="badge text-decoration-none  <?= $hospital['estado'] == 1 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $hospital['estado'] === 1 ? 'Ativo' : 'Inativo' ?>
                                </a>
                            </p>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="hospital-editar.php?table=hospitais&id=<?= $hospital['id'] ?>" class="btn btn-sm text-white" style="background-color: #202d3b;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModalHospital" data-hospital-id="<?php echo $hospital['id']; ?>">
                                <i class="fa-solid fa-trash"></i>
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

<!-- Modal: Remover Hospital -->
<div class="modal fade" id="deleteModalHospital" tabindex="-1" aria-labelledby="deleteModalLabelHospital" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabelHospital">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem a certeza de que deseja remover este hospital?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonHospital" href="#" class="btn btn-danger">Remover</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-danger[data-bs-toggle="modal"]').forEach(function(deleteButton) {
            deleteButton.addEventListener('click', function () {
                const hospitalId = this.getAttribute('data-hospital-id');
                const deleteConfirmButton = document.getElementById('deleteConfirmButtonHospital');
                deleteConfirmButton.setAttribute('href', 'includes/destroy.php?table=hospitais&id=' + hospitalId);
            });
        });
    });
</script>

<?php include 'partials/footer.php'; ?>