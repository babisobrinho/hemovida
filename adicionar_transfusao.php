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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bolsa = $_POST['id_bolsa'];
    $n_utente = $_POST['n_utente'];
    $data = $_POST['data'];
    $id_hospital = $_POST['id_hospital'];

    
    $maxIdQuery = "SELECT MAX(id) AS max_id FROM transfusoes";
    $maxIdResult = $conn->query($maxIdQuery);
    $maxIdRow = $maxIdResult->fetch_assoc();
    $novoId = $maxIdRow['max_id'] + 1; 

    
    $sql = "INSERT INTO transfusoes (id, id_bolsa, n_utente, data, id_hospital) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $novoId, $id_bolsa, $n_utente, $data, $id_hospital);

    if ($stmt->execute()) {
      
        $updateBolsaQuery = "UPDATE bolsas_sangue SET estado = 'Utilizada' WHERE id = ?";
        $updateStmt = $conn->prepare($updateBolsaQuery);
        $updateStmt->bind_param("i", $id_bolsa);
        $updateStmt->execute();
        $updateStmt->close();

      
        header(header: "Location: transfusoes.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erro ao adicionar transfusão.</div>";
    }

    $stmt->close();
}


$tiposSanguineosQuery = "SELECT DISTINCT tipo_sanguineo FROM dadores";
$tiposSanguineosResult = $conn->query($tiposSanguineosQuery);
$tiposSanguineos = $tiposSanguineosResult->fetch_all(MYSQLI_ASSOC);


$hospitaisQuery = "SELECT id, nome FROM hospitais";
$hospitaisResult = $conn->query($hospitaisQuery);
$hospitais = $hospitaisResult->fetch_all(MYSQLI_ASSOC);


$bolsasQuery = "
    SELECT b.id, d.tipo_sanguineo 
    FROM bolsas_sangue b
    JOIN dadores d ON b.id_dador = d.id
    WHERE b.estado = 'Disponível'
";
$bolsasResult = $conn->query($bolsasQuery);
$bolsas = $bolsasResult->fetch_all(MYSQLI_ASSOC);
?>

<div class="container p-4">
    <h2>Adicionar Transfusão</h2>
    <form method="POST" action="adicionar_transfusao.php">
        <div class="mb-3">
            <label for="id_bolsa" class="form-label">Bolsa de Sangue</label>
            <select class="form-control" id="id_bolsa" name="id_bolsa" required>
                <?php foreach ($bolsas as $bolsa): ?>
                    <option value="<?= htmlspecialchars($bolsa['id']) ?>">
                        Bolsa <?= htmlspecialchars($bolsa['id']) ?> (Tipo Sanguíneo: <?= htmlspecialchars($bolsa['tipo_sanguineo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="n_utente" class="form-label">Número de Utente</label>
            <input type="text" class="form-control" id="n_utente" name="n_utente" required>
        </div>
        <div class="mb-3">
            <label for="data" class="form-label">Data da Transfusão</label>
            <input type="date" class="form-control" id="data" name="data" required>
        </div>
        <div class="mb-3">
            <label for="id_hospital" class="form-label">Hospital</label>
            <select class="form-control" id="id_hospital" name="id_hospital" required>
                <?php foreach ($hospitais as $hosp): ?>
                    <option value="<?= htmlspecialchars($hosp['id']) ?>">
                        <?= htmlspecialchars($hosp['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>