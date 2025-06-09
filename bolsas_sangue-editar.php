<?php
session_start();
include 'partials/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: bolsas_sangue.php');
    exit();
}

$id_bolsa = $_GET['id'];

// Conecta ao banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "hemovida";
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

// Busca os dados da bolsa de sangue
$query = "SELECT bolsas_sangue.*, dadores.tipo_sanguineo, dadores.nome 
          FROM bolsas_sangue
          JOIN dadores ON bolsas_sangue.id_dador = dadores.id
          WHERE bolsas_sangue.id = $id_bolsa";
$resultado = mysqli_query($conexao, $query);

if (mysqli_num_rows($resultado) == 0) {
    header('Location: bolsas_sangue.php');
    exit();
}

$bolsa = mysqli_fetch_assoc($resultado);

// Fecha a conexão com o banco de dados
mysqli_close($conexao);

$pageTitle = "Editar Bolsa de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Inventário', 'url' => 'bolsas_sangue.php', 'active' => false],
    ['title' => 'Editar Bolsa', 'url' => '#', 'active' => true]
];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <div class="row d-flex align-items-center justify-content-center py-4">
        <div class="col-xl-6 col-md-10 col-12">
            <div class="card">
                <form method="POST" action="includes/update.php">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="id_dador">Doador</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($bolsa['nome'] . ' (' . $bolsa['tipo_sanguineo'] . ')', ENT_QUOTES, 'UTF-8') ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="data_coleta">Data de Coleta</label>
                                <input type="date" class="form-control" id="data_coleta" name="data_coleta" value="<?= htmlspecialchars($bolsa['data_coleta'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="validade">Validade</label>
                                <input type="date" class="form-control" id="validade" name="validade" value="<?= htmlspecialchars($bolsa['validade'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="volume_ml">Volume (ml)</label>
                                <input type="number" class="form-control" id="volume_ml" name="volume_ml" value="<?= htmlspecialchars($bolsa['volume_ml'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="estado">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="Disponível" <?= $bolsa['estado'] == 'Disponível' ? 'selected' : '' ?>>Disponível</option>
                                    <option value="Utilizada" <?= $bolsa['estado'] == 'Utilizada' ? 'selected' : '' ?>>Utilizada</option>
                                    <option value="Vencida" <?= $bolsa['estado'] == 'Vencida' ? 'selected' : '' ?>>Vencida</option>
                                    <option value="Reservada" <?= $bolsa['estado'] == 'Reservada' ? 'selected' : '' ?>>Reservada</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="table" value="bolsas_sangue">
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-end gap-2 border-0 bg-white">
                        <a href="bolsas_sangue.php" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>