<?php
include 'partials/header.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hemovida";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$id = $_GET['id'];

$transfusaoQuery = "SELECT * FROM transfusoes WHERE id = ?";
$stmt = $conn->prepare($transfusaoQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$transfusao = $stmt->get_result()->fetch_assoc();
$stmt->close();

$tiposSanguineosQuery = "SELECT DISTINCT tipo_sanguineo FROM dadores";
$tiposSanguineosResult = $conn->query($tiposSanguineosQuery);
$tiposSanguineos = $tiposSanguineosResult->fetch_all(MYSQLI_ASSOC);

$hospitaisQuery = "SELECT id, nome FROM hospitais";
$hospitaisResult = $conn->query($hospitaisQuery);
$hospitais = $hospitaisResult->fetch_all(MYSQLI_ASSOC);

$bolsasQuery = "SELECT id, id_dador FROM bolsas_sangue WHERE estado = 'Utilizada'";
$bolsasResult = $conn->query($bolsasQuery);
$bolsas = $bolsasResult->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bolsa = $_POST['id_bolsa'];
    $n_utente = $_POST['n_utente'];
    $data = $_POST['data'];
    $id_hospital = $_POST['id_hospital'];


    $sql = "UPDATE transfusoes SET id_bolsa = ?, n_utente = ?, data = ?, id_hospital = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $id_bolsa, $n_utente, $data, $id_hospital, $id);

    if ($stmt->execute()) {
        
             header("Location: transfusoes.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar transfusão.</div>";
    }

    $stmt->close();
}
?>

<div class="container p-4">
    <h2>Editar Transfusão</h2>
    <form method="POST" action="editar_transfusao.php?id=<?= htmlspecialchars($id) ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($transfusao['id']) ?>">
        <div class="mb-3">
            <label for="id_bolsa" class="form-label">Bolsa de Sangue</label>
            <select class="form-control" id="id_bolsa" name="id_bolsa" required>
                <?php foreach ($bolsas as $bolsa): ?>
                    <option value="<?= htmlspecialchars($bolsa['id']) ?>" <?= $bolsa['id'] == $transfusao['id_bolsa'] ? 'selected' : '' ?>>
                        Bolsa <?= htmlspecialchars($bolsa['id']) ?> (Dador: <?= htmlspecialchars($bolsa['id_dador']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="n_utente" class="form-label">Número de Utente</label>
            <input type="text" class="form-control" id="n_utente" name="n_utente" value="<?= htmlspecialchars($transfusao['n_utente']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="data" class="form-label">Data da Transfusão</label>
            <input type="date" class="form-control" id="data" name="data" value="<?= htmlspecialchars($transfusao['data']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="id_hospital" class="form-label">Hospital</label>
            <select class="form-control" id="id_hospital" name="id_hospital" required>
                <?php foreach ($hospitais as $hosp): ?>
                    <option value="<?= htmlspecialchars($hosp['id']) ?>" <?= $hosp['id'] == $transfusao['id_hospital'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($hosp['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-warning">Atualizar</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>