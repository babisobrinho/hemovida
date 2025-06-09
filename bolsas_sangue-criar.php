<?php
ob_start();
session_start();
include 'partials/header.php';

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "hemovida";
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_dador = isset($_POST['id_dador']) ? mysqli_real_escape_string($conexao, $_POST['id_dador']) : '';
    $data_coleta = isset($_POST['data_coleta']) ? mysqli_real_escape_string($conexao, $_POST['data_coleta']) : '';
    $volume_ml = isset($_POST['volume_ml']) ? mysqli_real_escape_string($conexao, $_POST['volume_ml']) : '';
    $estado = isset($_POST['estado']) ? mysqli_real_escape_string($conexao, $_POST['estado']) : '';
    $validade = isset($_POST['validade']) ? mysqli_real_escape_string($conexao, $_POST['validade']) : '';

    if (empty($id_dador) || empty($data_coleta) || empty($volume_ml) || empty($estado) || empty($validade)) {
        $error = "Todos os campos são obrigatórios.";
    } elseif (!is_numeric($volume_ml) || $volume_ml <= 0) {
        $error = "Volume deve ser um número maior que zero.";
    } else {
        $query = "INSERT INTO bolsas_sangue (id_dador, data_coleta, volume_ml, estado, validade) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isiss", $id_dador, $data_coleta, $volume_ml, $estado, $validade);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: bolsas_sangue.php?msg=sucesso");
                exit();
            } else {
                $error = "Erro ao inserir a nova bolsa: " . mysqli_error($conexao);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Erro na preparação da consulta: " . mysqli_error($conexao);
        }
    }
}

$pageTitle = "Nova Bolsa de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Inventário', 'url' => 'bolsas_sangue.php', 'active' => false],
    ['title' => 'Nova Bolsa', 'url' => '#', 'active' => true]
];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <div class="row d-flex align-items-center justify-content-center py-4">
        <div class="col-xl-6 col-md-10 col-12">
            <div class="card">
                <form method="POST" action="">
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="id_dador">Doador</label>
                                <select class="form-select" name="id_dador" id="id_dador" required>
                                    <option value="" selected disabled>Selecione o doador</option>
                                    <?php
                                    $queryDadores = "SELECT id, nome, tipo_sanguineo FROM dadores";
                                    $resultadoDadores = mysqli_query($conexao, $queryDadores);

                                    if (mysqli_num_rows($resultadoDadores) > 0):
                                        while ($dador = mysqli_fetch_assoc($resultadoDadores)):
                                    ?>
                                    <option value="<?= htmlspecialchars($dador['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?= htmlspecialchars($dador['nome'], ENT_QUOTES, 'UTF-8'); ?> - <?= htmlspecialchars($dador['tipo_sanguineo'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php
                                        endwhile;
                                    else:
                                        echo '<option value="">Nenhum doador cadastrado</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12 mb-3">
                                <label class="form-label" for="data_coleta">Data da Coleta</label>
                                <input type="date" class="form-control" name="data_coleta" id="data_coleta" required>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mb-3">
                                <label class="form-label" for="volume_ml">Volume (ml)</label>
                                <input type="number" class="form-control" name="volume_ml" id="volume_ml" min="1" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12 mb-3">
                                <label class="form-label" for="validade">Validade</label>
                                <input type="date" class="form-control" name="validade" id="validade" required>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mb-3">
                                <label class="form-label" for="estado">Estado</label>
                                <select class="form-select" name="estado" id="estado" required>
                                    <option value="Disponível">Disponível</option>
                                    <option value="Utilizada">Utilizada</option>
                                    <option value="Vencida">Vencida</option>
                                    <option value="Reservada">Reservada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-end gap-2 border-0 bg-white">
                        <a href="bolsas_sangue.php" class="btn btn-light border">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-danger">
                            Criar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Set max date for collection date to today
        let today = new Date().toISOString().split("T")[0];
        document.getElementById("data_coleta").setAttribute("max", today);
        
        // Set min date for validity to tomorrow
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById("validade").setAttribute("min", tomorrow.toISOString().split("T")[0]);
    });
</script>

<?php 
mysqli_close($conexao);
include 'partials/footer.php'; 
?>