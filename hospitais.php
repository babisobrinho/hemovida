<?php

    include 'partials/header.php';
    include 'includes/db_functions.php';

    if (isset($_GET['toggle_id'])) {
        $hospitalId = $_GET['toggle_id'];
        if (toggleEstado($pdo, 'hospitais', $hospitalId)) {
            $_SESSION['alert_message'] = displayAlert('Estado do hospital alterado com sucesso.', 'sucesso', 'success');
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

    <div class="d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #ffffff; border: 1px solid #dee2e6;">
        <div>
            <p class="mb-0 text-muted">Gerir hospitais parceiros do sistema</p>
        </div>
        <a href="hospital-criar.php" class="btn border-0 text-white px-4 py-2 rounded-3" style="background-color: #202d3b; transition: all 0.3s ease;">
            <i class="fa-solid fa-plus me-2"></i>Novo Hospital
        </a>
    </div>

    <div class="row g-3">
    <?php if (count($hospitais) > 0): ?>
        <?php foreach ($hospitais as $hospital): ?>
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100" style="background-color: #ffffff; border-radius: 16px; transition: all 0.3s ease;">
                    <div class="card-body p-3">
                        <!-- Header do Card -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="input-group" style="flex: 1; margin-right: 10px;">
                                <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                    <i class="fa-regular fa-hospital text-white"></i>
                                </span>
                                <div class="form-control py-2 px-3 border-0 rounded-end-3 d-flex align-items-center" style="background-color: #f8f9fa;">
                                    <span class="text-dark fw-medium"><?= $hospital['nome'] ?></span>
                                </div>
                            </div>
                            <a href="?toggle_id=<?= $hospital['id'] ?>" class="badge text-decoration-none px-2 py-1 rounded-pill <?= $hospital['estado'] == 1 ? 'bg-success' : 'bg-danger' ?>" style="transition: all 0.3s ease; font-size: 0.7rem;">
                                <?= $hospital['estado'] === 1 ? 'Ativo' : 'Inativo' ?>
                            </a>
                        </div>
                        
                        <!-- Informações Compactas -->
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                        <i class="fa-solid fa-location-dot text-white"></i>
                                    </span>
                                    <div class="form-control py-2 px-3 border-0 rounded-end-3 d-flex align-items-center" style="background-color: #f8f9fa;">
                                        <span class="text-dark fw-medium"><?= $hospital['endereco'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                        <i class="fa-solid fa-phone text-white"></i>
                                    </span>
                                    <div class="form-control py-2 px-3 border-0 rounded-end-3 d-flex align-items-center" style="background-color: #f8f9fa;">
                                        <span class="text-dark fw-medium"><?= $hospital['telefone'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                        <i class="fa-solid fa-envelope text-white"></i>
                                    </span>
                                    <div class="form-control py-2 px-3 border-0 rounded-end-3 d-flex align-items-center" style="background-color: #f8f9fa;">
                                        <a href="mailto:<?= $hospital['email'] ?>" class="text-decoration-none text-dark fw-medium"><?= $hospital['email'] ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                        <i class="fa-solid fa-user-tie text-white"></i>
                                    </span>
                                    <div class="form-control py-2 px-3 border-0 rounded-end-3 d-flex align-items-center" style="background-color: #f8f9fa;">
                                        <span class="text-dark fw-medium"><?= $hospital['nome_responsavel'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="d-flex gap-2">
                            <a href="hospital-editar.php?table=hospitais&id=<?= $hospital['id'] ?>" class="btn btn-sm flex-fill text-white rounded-3" style="background-color: #202d3b; transition: all 0.3s ease; font-size: 0.8rem;">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <button class="btn btn-sm btn-outline-danger flex-fill rounded-3" data-bs-toggle="modal" data-bs-target="#deleteModalHospital" data-hospital-id="<?php echo $hospital['id']; ?>" style="transition: all 0.3s ease; font-size: 0.8rem;">
                                <i class="fa-solid fa-trash me-1"></i>Remover
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="text-center py-5 rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                <div class="p-4 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%); width: 80px; height: 80px;">
                    <i class="fa-regular fa-hospital text-white" style="font-size: 2rem;"></i>
                </div>
                <h5 class="text-dark mb-2">Nenhum hospital cadastrado</h5>
                <p class="text-muted mb-4">Comece adicionando o primeiro hospital parceiro</p>
                <a href="hospital-criar.php" class="btn text-white px-4 py-2 rounded-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                    <i class="fa-solid fa-plus me-2"></i>Adicionar Hospital
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(32, 45, 59, 0.15) !important;
}

.badge:hover {
    color: white !important;
    transform: scale(1.1);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.btn-outline-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
}

.btn-light:hover {
    background-color: #e9ecef !important;
    transform: translateY(-2px);
}

/* Animação suave para os elementos */
.card-body > div {
    transition: all 0.3s ease;
}

.card:hover .card-body > div {
    transform: translateX(5px);
}
</style>

<!-- Modal: Remover Hospital -->
<div class="modal fade" id="deleteModalHospital" tabindex="-1" aria-labelledby="deleteModalLabelHospital" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title text-white" id="deleteModalLabelHospital">Confirmar Remoção</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="fa-solid fa-triangle-exclamation text-warning mb-3" style="font-size: 3rem;"></i>
                    <p class="text-dark mb-0">Tem a certeza de que deseja remover este hospital?</p>
                    <small class="text-muted">Esta ação não pode ser desfeita.</small>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonHospital" href="#" class="btn btn-danger px-4 rounded-3">Remover</a>
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

