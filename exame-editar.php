<?php
include 'partials/header.php';
require_once 'includes/db_connection.php';

$pageTitle = "Editar Exame";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Exames', 'url' => 'exames.php', 'active' => false],
    ['title' => 'Editar', 'url' => '#', 'active' => true]
];

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do exame não fornecido");
}

$error = null;
$success = false;

$exameQuery = "SELECT * FROM exames WHERE id = :id";
$stmt = $pdo->prepare($exameQuery);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$exame = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exame) {
    die("Exame não encontrado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bolsa = $_POST['id_bolsa'] ?? null;
    $data = $_POST['data'] ?? null;
    $hemoglobina = $_POST['hemoglobina'] ?? null;
    $hepatite = $_POST['hepatite'] ?? null;
    $hiv = $_POST['hiv'] ?? null;
    $chagas = $_POST['chagas'] ?? null;
    $sifilis = $_POST['sifilis'] ?? null;
    $resultado = $_POST['resultado'] ?? null;

    if (empty($id_bolsa)) {
        $error = "Informe o ID da bolsa";
    } elseif (empty($data)) {
        $error = "Informe a data do exame";
    } elseif (empty($hemoglobina)) {
        $error = "Informe o nível de hemoglobina";
    } elseif (empty($resultado)) {
        $error = "Selecione o resultado";
    } elseif (strlen($resultado) > 20) { 
        $error = "Resultado excede o tamanho máximo permitido (20 caracteres)";
    } else {
        try {
            $sql = "UPDATE exames SET 
                    id_bolsa = :id_bolsa,
                    data = :data,
                    hemoglobina = :hemoglobina,
                    hepatite = :hepatite,
                    hiv = :hiv,
                    chagas = :chagas,
                    sifilis = :sifilis,
                    resultado = :resultado
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id_bolsa", $id_bolsa, PDO::PARAM_INT);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":hemoglobina", $hemoglobina);
            $stmt->bindParam(":hepatite", $hepatite, PDO::PARAM_INT);
            $stmt->bindParam(":hiv", $hiv, PDO::PARAM_INT);
            $stmt->bindParam(":chagas", $chagas, PDO::PARAM_INT);
            $stmt->bindParam(":sifilis", $sifilis, PDO::PARAM_INT);
            $stmt->bindParam(":resultado", $resultado);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $success = true;
                
                echo '<div class="container p-4">
                        <div class="alert alert-success d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="alert-heading mb-1">Exame atualizado com sucesso!</h4>
                                <p class="mb-0">As alterações no exame #'.htmlspecialchars($id).' foram guardadas.</p>
                            </div>
                        </div>
                      </div>';
                
                echo '<script>
                        setTimeout(function() {
                            window.location.href = "exames.php?updated='.$id.'";
                        }, 3000);
                      </script>';
                
                include 'partials/footer.php';
                exit();
            } else {
                $error = "Erro ao atualizar o exame.";
            }
        } catch (PDOException $e) {
            $error = "Erro ao atualizar o exame: " . $e->getMessage();
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
                <h4 class="alert-heading mb-1">Erro ao atualizar exame</h4>
                <p class="mb-0"><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-edit me-2"></i>Editar Exame #<?= htmlspecialchars($exame['id']) ?>
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form method="POST" action="exame-editar.php?id=<?= htmlspecialchars($id) ?>" class="row g-3">
                <div class="col-md-6">
                    <label for="id_bolsa" class="form-label">ID Bolsa <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="id_bolsa" name="id_bolsa" 
                           value="<?= htmlspecialchars($exame['id_bolsa']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="data" class="form-label">Data <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data" name="data" 
                           value="<?= htmlspecialchars($exame['data']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="hemoglobina" class="form-label">Hemoglobina (g/dL) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="hemoglobina" name="hemoglobina" 
                           value="<?= htmlspecialchars($exame['hemoglobina']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="resultado" class="form-label">Resultado <span class="text-danger">*</span></label>
                    <select class="form-select" id="resultado" name="resultado" required>
                        <option value="Aprovado" <?= $exame['resultado'] === 'Aprovado' ? 'selected' : '' ?>>Aprovado</option>
                        <option value="Reprovado" <?= $exame['resultado'] === 'Reprovado' ? 'selected' : '' ?>>Reprovado</option>
                        <option value="Em Análise" <?= $exame['resultado'] === 'Em Análise' ? 'selected' : '' ?>>Em Análise</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="hepatite" class="form-label">Hepatite</label>
                    <select class="form-select" id="hepatite" name="hepatite">
                        <option value="0" <?= !$exame['hepatite'] ? 'selected' : '' ?>>Negativo</option>
                        <option value="1" <?= $exame['hepatite'] ? 'selected' : '' ?>>Positivo</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="hiv" class="form-label">HIV</label>
                    <select class="form-select" id="hiv" name="hiv">
                        <option value="0" <?= !$exame['hiv'] ? 'selected' : '' ?>>Negativo</option>
                        <option value="1" <?= $exame['hiv'] ? 'selected' : '' ?>>Positivo</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="chagas" class="form-label">Chagas</label>
                    <select class="form-select" id="chagas" name="chagas">
                        <option value="0" <?= !$exame['chagas'] ? 'selected' : '' ?>>Negativo</option>
                        <option value="1" <?= $exame['chagas'] ? 'selected' : '' ?>>Positivo</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="sifilis" class="form-label">Sífilis</label>
                    <select class="form-select" id="sifilis" name="sifilis">
                        <option value="0" <?= !$exame['sifilis'] ? 'selected' : '' ?>>Negativo</option>
                        <option value="1" <?= $exame['sifilis'] ? 'selected' : '' ?>>Positivo</option>
                    </select>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn text-white me-2" style="background-color: #202d3b;">
                        <i class="fa-solid fa-save me-2"></i>Atualizar
                    </button>
                    <a href="exames.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>