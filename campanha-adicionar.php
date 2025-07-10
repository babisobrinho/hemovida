<?php
include 'partials/header.php';
require_once 'includes/db_connection.php';

$pageTitle = "Nova Campanha";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Campanhas', 'url' => 'campanhas.php', 'active' => false],
    ['title' => 'Nova', 'url' => '#', 'active' => true]
];

$error = null;
$success = false;

// Tipos sanguíneos disponíveis
$tiposDisponiveis = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $tipos_selecionados = $_POST['tipos_sanguineos'] ?? [];
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $meta = $_POST['meta'] ?? 0;
    $prioridade = $_POST['prioridade'] ?? 'normal';
    $descricao = $_POST['descricao'] ?? '';

    // Validações
    if (empty($titulo)) {
        $error = "O título da campanha é obrigatório";
    } elseif (empty($data_inicio)) {
        $error = "A data de início é obrigatória";
    } elseif (empty($data_fim)) {
        $error = "A data de término é obrigatória";
    } elseif (strtotime($data_fim) < strtotime($data_inicio)) {
        $error = "A data de término deve ser posterior à data de início";
    } elseif ($meta <= 0) {
        $error = "A meta deve ser maior que zero";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Inserir a campanha principal
            $sqlCampanha = "INSERT INTO campanhas 
                            (titulo, data_inicio, data_fim, meta, prioridade, descricao) 
                            VALUES 
                            (:titulo, :data_inicio, :data_fim, :meta, :prioridade, :descricao)";
            
            $stmtCampanha = $pdo->prepare($sqlCampanha);
            $stmtCampanha->bindParam(':titulo', $titulo);
            $stmtCampanha->bindParam(':data_inicio', $data_inicio);
            $stmtCampanha->bindParam(':data_fim', $data_fim);
            $stmtCampanha->bindParam(':meta', $meta, PDO::PARAM_INT);
            $stmtCampanha->bindParam(':prioridade', $prioridade);
            $stmtCampanha->bindParam(':descricao', $descricao);
            
            if (!$stmtCampanha->execute()) {
                throw new Exception("Erro ao inserir campanha principal");
            }
            
            $campanhaId = $pdo->lastInsertId();

            // 2. Inserir os tipos sanguíneos relacionados
            if (!empty($tipos_selecionados)) {
                $sqlTipos = "INSERT INTO campanhas_tipos_sanguineos 
                            (id_campanha, tipo_sanguineo) 
                            VALUES 
                            (:id_campanha, :tipo_sanguineo)";
                
                $stmtTipos = $pdo->prepare($sqlTipos);
                $stmtTipos->bindParam(':id_campanha', $campanhaId, PDO::PARAM_INT);
                
                foreach ($tipos_selecionados as $tipo) {
                    if (in_array($tipo, $tiposDisponiveis)) {
                        $stmtTipos->bindParam(':tipo_sanguineo', $tipo);
                        if (!$stmtTipos->execute()) {
                            throw new Exception("Erro ao inserir tipo sanguíneo: $tipo");
                        }
                    }
                }
            }
            
            $pdo->commit();
            $success = true;
            
            // Mensagem de sucesso
            echo '<div class="container p-4">
                    <div class="alert alert-success d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="alert-heading mb-1">Campanha criada com sucesso!</h4>
                            <p class="mb-0">A nova campanha foi adicionada ao sistema.</p>
                        </div>
                    </div>
                  </div>';
            
            // Redirecionamento com JavaScript
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "campanhas.php?success=1&new_id='.$campanhaId.'";
                    }, 3000);
                  </script>';
            
            include 'partials/footer.php';
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao criar campanha: " . $e->getMessage();
            error_log("Erro PDO: " . $e->getMessage());
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erro: " . $e->getMessage();
            error_log("Erro Geral: " . $e->getMessage());
        }
    }
}
?>

<!-- Restante do seu HTML permanece o mesmo -->
<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <?php if ($error && !$success): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-exclamation-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Erro ao criar campanha</h4>
                <p class="mb-0"><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-plus-circle me-2"></i>Nova Campanha
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form method="POST" action="campanha-adicionar.php" class="row g-3" id="campanhaForm">
                <div class="col-md-12">
                    <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Tipos Sanguíneos</label>
                    <div class="row">
                        <?php foreach ($tiposDisponiveis as $tipo): ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="tipo_<?= $tipo ?>" name="tipos_sanguineos[]" 
                                           value="<?= $tipo ?>">
                                    <label class="form-check-label" for="tipo_<?= $tipo ?>">
                                        <?= $tipo ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="text-muted">Se nenhum for selecionado, a campanha será para todos os tipos.</small>
                </div>
                
                <div class="col-md-6">
                    <label for="data_inicio" class="form-label">Data Início <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" required
                           min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="data_fim" class="form-label">Data Término <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" required
                           min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="meta" class="form-label">Meta (doações) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="meta" name="meta" min="1" required>
                </div>
                
                <div class="col-md-6">
                    <label for="prioridade" class="form-label">Prioridade <span class="text-danger">*</span></label>
                    <select class="form-select" id="prioridade" name="prioridade" required>
                        <option value="normal">Normal</option>
                        <option value="urgente">Urgente</option>
                        <option value="critica">Crítica</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn text-white me-2" style="background-color: #202d3b;">
                        <i class="fa-solid fa-save me-2"></i>Guardar
                    </button>
                    <a href="campanhas.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('campanhaForm');
    const dataInicio = document.getElementById('data_inicio');
    const dataFim = document.getElementById('data_fim');
    
    // Atualizar data mínima do fim quando a data de início muda
    dataInicio.addEventListener('change', function() {
        dataFim.min = this.value;
    });
    
    // Validação do formulário
    form.addEventListener('submit', function(e) {
        if (dataFim.value && dataInicio.value > dataFim.value) {
            e.preventDefault();
            alert('A data de término deve ser posterior à data de início');
            return false;
        }
        return true;
    });
});
</script>

<?php include 'partials/footer.php'; ?>