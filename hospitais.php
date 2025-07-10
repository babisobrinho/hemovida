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

$totalHospitaisQuery = "SELECT COUNT(*) as total FROM hospitais";
$totalHospitaisStmt = $pdo->query($totalHospitaisQuery);
$totalHospitais = $totalHospitaisStmt->fetchColumn();

$ativosQuery = "SELECT COUNT(*) as total FROM hospitais WHERE estado = 1";
$ativosStmt = $pdo->query($ativosQuery);
$totalAtivos = $ativosStmt->fetchColumn();

$inativosQuery = "SELECT COUNT(*) as total FROM hospitais WHERE estado = 0";
$inativosStmt = $pdo->query($inativosQuery);
$totalInativos = $inativosStmt->fetchColumn();

$percentAtivos = $totalHospitais > 0 ? round(($totalAtivos / $totalHospitais) * 100) : 0;
$percentInativos = $totalHospitais > 0 ? round(($totalInativos / $totalHospitais) * 100) : 0;

$topTransfusoesQuery = "SELECT h.nome, COUNT(t.id) as total 
                        FROM hospitais h 
                        LEFT JOIN transfusoes t ON h.id = t.id_hospital 
                        GROUP BY h.id 
                        ORDER BY total DESC
                        LIMIT 1";
$topTransfusoesStmt = $pdo->query($topTransfusoesQuery);
$topTransfusoes = $topTransfusoesStmt->fetch(PDO::FETCH_ASSOC);

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

    <div class="container-fluid mb-5 px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
                <h5 class="mb-0 font-weight-bold" style="color: #202d3b;">
                    <i class="fas fa-chart-pie me-2"></i>Estatísticas de Hospitais
                </h5>
            </div>
            
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <!-- Total de Hospitais -->
                    <div class="col-lg-2 col-md-4 border-right">
                        <div class="p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background-color: rgba(32, 45, 59, 0.1);">
                                    <i class="fas fa-hospital" style="color: #202d3b;"></i>
                                </div>
                                <div>
                                    <p class="mb-1 small text-muted">TOTAL</p>
                                    <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= $totalHospitais ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hospitais Ativos -->
                    <div class="col-lg-2 col-md-4 border-right">
                        <div class="p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background-color: rgba(25, 135, 84, 0.1);">
                                    <i class="fas fa-check-circle" style="color: #198754;"></i>
                                </div>
                                <div>
                                    <p class="mb-1 small text-muted">ATIVOS</p>
                                    <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= $totalAtivos ?></h3>
                                    <span class="badge" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                                        <?= $percentAtivos ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hospitais Inativos -->
                    <div class="col-lg-2 col-md-4">
                        <div class="p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background-color: rgba(220, 53, 69, 0.1);">
                                    <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                                </div>
                                <div>
                                    <p class="mb-1 small text-muted">INATIVOS</p>
                                    <h3 class="mb-0 fs-4 font-weight-bold mx-2"><?= $totalInativos ?></h3>
                                    <span class="badge" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                        <?= $percentInativos ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hospital + Transfusões -->
                    <div class="col-lg-6 border-right">
                        <div class="p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background-color: rgba(220, 53, 69, 0.1);">
                                    <i class="fas fa-tint" style="color: #dc3545;"></i>
                                </div>
                                <div>
                                    <p class="mb-1 small text-muted">MAIS TRANSFUSÕES</p>
                                    <h3 class="mb-0 mx-2 fs-4 font-weight-bold">
                                        <?= htmlspecialchars($topTransfusoes['nome'] ?? 'Nenhum', ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <span class="badge" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                        <?= $topTransfusoes['total'] ?? 0 ?> transfusões
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
</div>

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
        // Modal de delete (original)
        document.querySelectorAll('.btn-danger[data-bs-toggle="modal"]').forEach(function(deleteButton) {
            deleteButton.addEventListener('click', function () {
                const hospitalId = this.getAttribute('data-hospital-id');
                const deleteConfirmButton = document.getElementById('deleteConfirmButtonHospital');
                deleteConfirmButton.setAttribute('href', 'includes/destroy.php?table=hospitais&id=' + hospitalId);
            });
        });

        // ===== EASTER EGG 2.0 (INFALÍVEL) ===== //
        const cardTransfusoes = document.querySelector('.col-lg-6.border-right .p-4'); // Seleciona a DIV interna do card
        
        if (cardTransfusoes) {
            let passadasMouse = 0;
            const timer = 3000; // 3 segundos para resetar
            
            cardTransfusoes.addEventListener('mouseenter', () => {
                passadasMouse++;
                
                if (passadasMouse === 3) {
                    const titulo = cardTransfusoes.querySelector('h3');
                    if (titulo && !titulo.querySelector('.easter-egg')) {
                        // Adiciona o emoji secreto
                        const emojiSecreto = document.createElement('span');
                        emojiSecreto.className = 'easter-egg ms-2';
                        emojiSecreto.innerHTML = '❤️ <small class="text-danger">Herói do sangue!</small>';
                        titulo.appendChild(emojiSecreto);
                        
                        // Efeitos especiais
                        cardTransfusoes.parentElement.style.transform = 'scale(1.05)';
                        cardTransfusoes.parentElement.style.boxShadow = '0 0 25px rgba(220, 53, 69, 0.7)';
                        
                        // Reset após 3 segundos
                        setTimeout(() => {
                            cardTransfusoes.parentElement.style.transform = '';
                            cardTransfusoes.parentElement.style.boxShadow = '';
                            passadasMouse = 0;
                        }, timer);
                    }
                }
            });
        }
    });
</script>

<?php include 'partials/footer.php'; ?>