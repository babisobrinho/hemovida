<?php
include 'partials/header.php';

$pageTitle = "Transfusões de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Transfusões', 'url' => '#', 'active' => true]
];

$dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

$servername = "localhost";  
$username = "root";        
$password = "";             
$dbname = "hemovida";       

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT t.id, t.id_bolsa, t.n_utente, t.data, h.nome AS hospital, d.nome AS paciente
        FROM transfusoes t
        JOIN hospitais h ON t.id_hospital = h.id
        JOIN bolsas_sangue b ON t.id_bolsa = b.id
        JOIN dadores d ON b.id_dador = d.id
        WHERE 1";

$params = [];
$types = "";


if (!empty($dataInicio)) {
    $sql .= " AND t.data >= ?";
    $params[] = $dataInicio;
    $types .= "s";
}
if (!empty($dataFim)) {
    $sql .= " AND t.data <= ?";
    $params[] = $dataFim;
    $types .= "s";
}


$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$transfusoes=$result->fetch_all(MYSQLI_ASSOC);

$sql_tipo_sanguineo="SELECT dadores.tipo_sanguineo FROM dadores JOIN bolsas_sangue ON dadores.id=bolsas_sangue=id_dador WHERE bolsas_sangue.id=5";
$stmt=$conn->query($sql_tipo_sanguineo);
$stmt->execute();
$tipo_sanguineo = $stmt->get_result();
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <div class="card border-0 p-0">
        
        <div class="card-body p-0">

            
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($dataInicio) ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= htmlspecialchars($dataFim) ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn w-100 text-white" style="background-color: #202D3B;">Filtrar</button>
                    </div>
                </div>
            </form>

            
            <div class="table-responsive">
                <table class="table table-hover mt-3">
                    <thead>
                        <tr>
                            <th>Nome Paciente</th>
                            <th>ID Bolsa</th>
                            <th>Número Utente</th>
                            <th>Data da Transfusão</th>
                            <th>Hospital</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                        if (count($transfusoes) > 0): 
                            foreach($transfusoes as $row): 
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $row['paciente']; ?></th>
                                    <td><?php echo $row['id_bolsa'];?></td>
                                    <td><?php echo $row['n_utente']; ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row['data'])); ?></td>
                                    <td><?php echo $row['hospital']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                           <tr><td colspan='5' class='text-center text-danger fw-bold py-3'>Nenhuma transfusão encontrada para o intervalo de datas selecionado.</td></tr>
                       
                       <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php
include 'partials/footer.php';
?>
