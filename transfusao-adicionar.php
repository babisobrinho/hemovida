<?php
include 'partials/header.php';
require_once 'includes/db_connection.php';

$pageTitle = "Adicionar Transfusão";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Transfusões', 'url' => 'transfusoes.php', 'active' => false],
    ['title' => 'Adicionar', 'url' => '#', 'active' => true]
];

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bolsa = $_POST['id_bolsa'] ?? null;
    $n_utente = $_POST['n_utente'] ?? null;
    $data = $_POST['data'] ?? null;
    $id_hospital = $_POST['id_hospital'] ?? null;

    // Validações básicas
    if (empty($id_bolsa)) {
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

            // Verificar se a bolsa ainda está disponível
            $checkBolsaQuery = "SELECT id, validade FROM bolsas_sangue WHERE id = :id_bolsa AND estado = 'Disponível'";
            $checkStmt = $pdo->prepare($checkBolsaQuery);
            $checkStmt->bindParam(':id_bolsa', $id_bolsa, PDO::PARAM_INT);
            $checkStmt->execute();
            $bolsa = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$bolsa) {
                throw new Exception("A bolsa selecionada não está mais disponível. Por favor, atualize a página e selecione outra bolsa.");
            }

            if (strtotime($bolsa['validade']) < strtotime($data)) {
                throw new Exception("A bolsa selecionada estará vencida na data da transfusão");
            }

            // Obter o próximo ID
            $maxIdQuery = "SELECT MAX(id) AS max_id FROM transfusoes";
            $maxIdResult = $pdo->query($maxIdQuery);
            $maxIdRow = $maxIdResult->fetch(PDO::FETCH_ASSOC);
            $novoId = ($maxIdRow['max_id'] ?? 0) + 1;

            // Inserir a transfusão
            $sql = "INSERT INTO transfusoes (id, id_bolsa, n_utente, data, id_hospital) VALUES (:id, :id_bolsa, :n_utente, :data, :id_hospital)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(":id", $novoId, PDO::PARAM_INT);
            $stmt->bindParam(":id_bolsa", $id_bolsa, PDO::PARAM_INT);
            $stmt->bindParam(":n_utente", $n_utente);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":id_hospital", $id_hospital, PDO::PARAM_INT);
            
            $stmt->execute();

            // Atualizar estado da bolsa
            $updateBolsaQuery = "UPDATE bolsas_sangue SET estado = 'Utilizada' WHERE id = :id_bolsa";
            $updateStmt = $pdo->prepare($updateBolsaQuery);
            $updateStmt->bindParam(":id_bolsa", $id_bolsa, PDO::PARAM_INT);
            $updateStmt->execute();
            
            $pdo->commit();
            $success = true;
            
            // Mensagem de sucesso
            echo '<div class="container p-4">
                    <div class="alert alert-success d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="alert-heading mb-1">Transfusão registada com sucesso!</h4>
                            <p class="mb-0">A nova transfusão #'.htmlspecialchars($novoId).' foi adicionada ao sistema.</p>
                        </div>
                    </div>
                  </div>';
            
            // Redirecionamento com JavaScript
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "transfusoes.php?success=1&new_id='.$novoId.'";
                    }, 3000);
                  </script>';
            
            include 'partials/footer.php';
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao processar a transfusão: " . $e->getMessage();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// Obter bolsas disponíveis e com validade futura
$bolsasQuery = "SELECT b.id, d.tipo_sanguineo, b.data_coleta, b.validade
                FROM bolsas_sangue b
                JOIN dadores d ON b.id_dador = d.id
                WHERE b.estado = 'Disponível' AND b.validade >= CURDATE()";
$bolsas = $pdo->query($bolsasQuery)->fetchAll(PDO::FETCH_ASSOC);

// Obter hospitais
$hospitaisQuery = "SELECT id, nome FROM hospitais";
$hospitais = $pdo->query($hospitaisQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <?php if ($error && !$success): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-exclamation-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Erro ao registar transfusão</h4>
                <p class="mb-0"><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-plus-circle me-2"></i>Adicionar Nova Transfusão
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form method="POST" action="transfusao-adicionar.php" class="row g-3" id="transfusaoForm">
                <div class="col-md-6">
                    <label for="id_bolsa" class="form-label">Bolsa de Sangue <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_bolsa" name="id_bolsa" required>
                        <option value="">Selecione uma bolsa</option>
                        <?php foreach ($bolsas as $bolsa): ?>
                            <option value="<?= htmlspecialchars($bolsa['id']) ?>" 
                                    data-validade="<?= htmlspecialchars($bolsa['validade']) ?>">
                                Bolsa <?= htmlspecialchars($bolsa['id']) ?> 
                                (Tipo: <?= htmlspecialchars($bolsa['tipo_sanguineo']) ?>,
                                Validade: <?= date('d/m/Y', strtotime($bolsa['validade'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($bolsas)): ?>
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>Nenhuma bolsa disponível no momento
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="n_utente" class="form-label">Número de Utente <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="n_utente" name="n_utente" required>
                </div>
                
                <div class="col-md-6">
                    <label for="data" class="form-label">Data da Transfusão <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data" name="data" required
                           value="<?= date('Y-m-d') ?>"
                           min="<?= date('Y-m-d') ?>"
                           max="<?= date('Y-m-d', strtotime('+3 months')) ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="id_hospital" class="form-label">Hospital <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_hospital" name="id_hospital" required>
                        <option value="">Selecione um hospital</option>
                        <?php foreach ($hospitais as $hosp): ?>
                            <option value="<?= htmlspecialchars($hosp['id']) ?>">
                                <?= htmlspecialchars($hosp['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn text-white me-2" style="background-color: #202d3b;" <?= empty($bolsas) ? 'disabled' : '' ?>>
                        <i class="fa-solid fa-save me-2"></i>Guardar
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
    
    // Validar data vs validade da bolsa
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