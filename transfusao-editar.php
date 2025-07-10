<?php
include 'partials/header.php';
require_once 'includes/db_connection.php';

$pageTitle = "Editar Transfusão";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Transfusões', 'url' => 'transfusoes.php', 'active' => false],
    ['title' => 'Editar', 'url' => '#', 'active' => true]
];

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID da transfusão não fornecido");
}

$error = null;
$success = false;

// Obter dados da transfusão atual
$transfusaoQuery = "SELECT t.*, b.estado AS bolsa_estado, b.validade, d.tipo_sanguineo, h.nome AS hospital_nome
                    FROM transfusoes t
                    JOIN bolsas_sangue b ON t.id_bolsa = b.id
                    JOIN dadores d ON b.id_dador = d.id
                    JOIN hospitais h ON t.id_hospital = h.id
                    WHERE t.id = :id";
$stmt = $pdo->prepare($transfusaoQuery);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$transfusao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transfusao) {
    die("Transfusão não encontrada");
}

// Obter bolsas disponíveis (incluindo a atual da transfusão)
$bolsasQuery = "SELECT b.id, d.tipo_sanguineo, b.data_coleta, b.validade, b.estado
                FROM bolsas_sangue b
                JOIN dadores d ON b.id_dador = d.id
                WHERE b.estado = 'Disponível' OR b.id = :id_bolsa
                ORDER BY b.estado = 'Disponível' DESC, b.validade ASC";
$stmt = $pdo->prepare($bolsasQuery);
$stmt->bindParam(":id_bolsa", $transfusao['id_bolsa'], PDO::PARAM_INT);
$stmt->execute();
$bolsas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter hospitais
$hospitaisQuery = "SELECT id, nome FROM hospitais";
$hospitais = $pdo->query($hospitaisQuery)->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bolsa_nova = $_POST['id_bolsa'] ?? null;
    $n_utente = $_POST['n_utente'] ?? null;
    $data = $_POST['data'] ?? null;
    $id_hospital = $_POST['id_hospital'] ?? null;
    $id_bolsa_antiga = $transfusao['id_bolsa'];

    // Validações básicas
    if (empty($id_bolsa_nova)) {
        $error = "Selecione uma bolsa de sangue válida";
    } elseif (empty($n_utente)) {
        $error = "Informe o número de utente";
    } elseif (empty($data)) {
        $error = "Informe a data da transfusão";
    } elseif (empty($id_hospital)) {
        $error = "Selecione um hospital";
    } else {
        try {
            $pdo->beginTransaction();

            // Verificar se a nova bolsa está disponível (exceto se for a mesma)
            if ($id_bolsa_nova != $id_bolsa_antiga) {
                $checkBolsaQuery = "SELECT id, validade FROM bolsas_sangue WHERE (id = :id_bolsa_nova AND estado = 'Disponível') OR id = :id_bolsa_antiga";
                $checkStmt = $pdo->prepare($checkBolsaQuery);
                $checkStmt->bindParam(":id_bolsa_nova", $id_bolsa_nova, PDO::PARAM_INT);
                $checkStmt->bindParam(":id_bolsa_antiga", $id_bolsa_antiga, PDO::PARAM_INT);
                $checkStmt->execute();
                $bolsa_nova = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if (!$bolsa_nova) {
                    throw new Exception("A bolsa selecionada não está disponível. Por favor, atualize a página e selecione outra bolsa.");
                }

                if (strtotime($bolsa_nova['validade']) < strtotime($data)) {
                    throw new Exception("A bolsa selecionada estará vencida na data da transfusão");
                }
            }

            // Atualizar transfusão
            $updateTransfusaoQuery = "UPDATE transfusoes SET id_bolsa = :id_bolsa, n_utente = :n_utente, data = :data, id_hospital = :id_hospital WHERE id = :id";
            $updateStmt = $pdo->prepare($updateTransfusaoQuery);
            $updateStmt->bindParam(":id_bolsa", $id_bolsa_nova, PDO::PARAM_INT);
            $updateStmt->bindParam(":n_utente", $n_utente);
            $updateStmt->bindParam(":data", $data);
            $updateStmt->bindParam(":id_hospital", $id_hospital, PDO::PARAM_INT);
            $updateStmt->bindParam(":id", $id, PDO::PARAM_INT);
            
            $updateStmt->execute();

            // Se a bolsa foi alterada, atualizar estados
            if ($id_bolsa_nova != $id_bolsa_antiga) {
                // Marcar nova bolsa como utilizada
                $updateBolsaNovaQuery = "UPDATE bolsas_sangue SET estado = 'Utilizada' WHERE id = :id_bolsa_nova";
                $updateStmt = $pdo->prepare($updateBolsaNovaQuery);
                $updateStmt->bindParam(":id_bolsa_nova", $id_bolsa_nova, PDO::PARAM_INT);
                $updateStmt->execute();

                // Marcar bolsa antiga como disponível
                $updateBolsaAntigaQuery = "UPDATE bolsas_sangue SET estado = 'Disponível' WHERE id = :id_bolsa_antiga";
                $updateStmt = $pdo->prepare($updateBolsaAntigaQuery);
                $updateStmt->bindParam(":id_bolsa_antiga", $id_bolsa_antiga, PDO::PARAM_INT);
                $updateStmt->execute();
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
                            <h4 class="alert-heading mb-1">Transfusão atualizada com sucesso!</h4>
                            <p class="mb-0">As alterações na transfusão #'.htmlspecialchars($id).' foram guardadas no sistema.</p>
                        </div>
                    </div>
                  </div>';
            
            // Redirecionamento com JavaScript após 3 segundos
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "transfusoes.php?updated=1&id='.$id.'";
                    }, 3000);
                  </script>';
            
            include 'partials/footer.php';
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao atualizar transfusão: " . $e->getMessage();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <?php if ($error && !$success): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-exclamation-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Erro ao atualizar transfusão</h4>
                <p class="mb-0"><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-edit me-2"></i>Editar Transfusão #<?= htmlspecialchars($transfusao['id']) ?>
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form method="POST" action="transfusao-editar.php?id=<?= htmlspecialchars($id) ?>" class="row g-3" id="transfusaoForm">
                <div class="col-md-6">
                    <label for="id_bolsa" class="form-label">Bolsa de Sangue <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_bolsa" name="id_bolsa" required>
                        <option value="">Selecione uma bolsa</option>
                        <?php foreach ($bolsas as $bolsa): ?>
                            <option value="<?= htmlspecialchars($bolsa['id']) ?>" 
                                    <?= $bolsa['id'] == $transfusao['id_bolsa'] ? 'selected' : '' ?>
                                    data-validade="<?= htmlspecialchars($bolsa['validade']) ?>">
                                <?php if ($bolsa['id'] == $transfusao['id_bolsa']): ?>
                                    [ATUAL] Bolsa <?= htmlspecialchars($bolsa['id']) ?> 
                                    (<?= htmlspecialchars($bolsa['tipo_sanguineo']) ?>)
                                <?php else: ?>
                                    Bolsa <?= htmlspecialchars($bolsa['id']) ?> 
                                    (<?= htmlspecialchars($bolsa['tipo_sanguineo']) ?> - 
                                    Válida até <?= date('d/m/Y', strtotime($bolsa['validade'])) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (count($bolsas) === 1): ?>
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle me-2"></i>Esta é a única bolsa disponível no momento
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="n_utente" class="form-label">Número de Utente <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="n_utente" name="n_utente" 
                           value="<?= htmlspecialchars($transfusao['n_utente']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="data" class="form-label">Data da Transfusão <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data" name="data" 
                           value="<?= htmlspecialchars($transfusao['data']) ?>" required
                           min="<?= date('Y-m-d', strtotime('-1 month')) ?>"
                           max="<?= date('Y-m-d', strtotime('+3 months')) ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="id_hospital" class="form-label">Hospital <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_hospital" name="id_hospital" required>
                        <?php foreach ($hospitais as $hosp): ?>
                            <option value="<?= htmlspecialchars($hosp['id']) ?>" 
                                    <?= $hosp['id'] == $transfusao['id_hospital'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hosp['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn text-white me-2" style="background-color: #202d3b;">
                        <i class="fa-solid fa-save me-2"></i>Atualizar
                    </button>
                    <a href="transfusoes.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('transfusaoForm');
    const dataInput = document.getElementById('data');
    const bolsaSelect = document.getElementById('id_bolsa');
    
    form.addEventListener('submit', function(e) {
        const selectedBolsa = bolsaSelect.options[bolsaSelect.selectedIndex];
        const validadeBolsa = selectedBolsa.getAttribute('data-validade');
        const dataTransfusao = dataInput.value;
        
        if (validadeBolsa && dataTransfusao > validadeBolsa) {
            e.preventDefault();
            alert('A data da transfusão não pode ser posterior à validade da bolsa selecionada');
            return false;
        }
        return true;
    });
});
</script>

<?php include 'partials/footer.php'; ?>