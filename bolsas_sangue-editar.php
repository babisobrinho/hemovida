<?php
session_start();
include 'partials/header.php';
include 'includes/obter_registo.php';

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
$query = "SELECT bolsas_sangue.id, bolsas_sangue.data_coleta, bolsas_sangue.volume_ml, bolsas_sangue.estado, dadores.tipo_sanguineo
          FROM bolsas_sangue
          JOIN dadores ON bolsas_sangue.id_dador = dadores.id
          WHERE bolsas_sangue.id = $id_bolsa";
$resultado = mysqli_query($conexao, $query);

if (mysqli_num_rows($resultado) == 0) {
    echo "Bolsa de sangue não encontrada.";
    exit();
}

$bolsas = mysqli_fetch_assoc($resultado);
var_dump($bolsas);

// Fecha a conexão com o banco de dados
mysqli_close($conexao);
?>

<div class="container p-4">
    <h2>Editar Bolsa de Sangue</h2>
    <form method="POST" action="update.php">
        <input type="hidden" name="table" value="bolsas_sangue">
        <input type="hidden" name="id" value="<?= htmlspecialchars($bolsas['id'], ENT_QUOTES, 'UTF-8') ?>">

        <div class="mb-3">
            <label for="data_coleta" class="form-label">Data de Coleta</label>
            <input type="date" class="form-control" id="data_coleta" name="data_coleta" value="<?= htmlspecialchars($bolsas['data_coleta'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-3">
            <label for="volume_ml" class="form-label">Volume (ml)</label>
            <input type="number" class="form-control" id="volume_ml" name="volume_ml" value="<?= htmlspecialchars($bolsas['volume_ml'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="Disponível" <?= $bolsas['estado'] == 'Disponível' ? 'selected' : '' ?>>Disponível</option>
                <option value="Utilizada" <?= $bolsas['estado'] == 'Utilizada' ? 'selected' : '' ?>>Utilizada</option>
                <option value="Vencida" <?= $bolsas['estado'] == 'Vencida' ? 'selected' : '' ?>>Vencida</option>
                <option value="Reservada" <?= $bolsas['estado'] == 'Reservada' ? 'selected' : '' ?>>Reservada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="bolsas_sangue.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include 'partials/footer.php'; ?>
