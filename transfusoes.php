<?php
include 'partials/header.php';

$pageTitle = "Transfusões de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Transfusões', 'url' => '#', 'active' => true]
];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hemovida";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}


$tiposSanguineosQuery = "SELECT DISTINCT tipo_sanguineo FROM dadores";
$tiposSanguineosResult = $conn->query($tiposSanguineosQuery);
$tiposSanguineos = $tiposSanguineosResult->fetch_all(MYSQLI_ASSOC);


$hospitaisQuery = "SELECT id, nome FROM hospitais";
$hospitaisResult = $conn->query($hospitaisQuery);
$hospitais = $hospitaisResult->fetch_all(MYSQLI_ASSOC);

$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$tipoSanguineo = $_GET['tipo_sanguineo'] ?? '';
$hospital = $_GET['hospital'] ?? '';

$sql = "SELECT 
            t.id, 
            t.id_bolsa, 
            t.n_utente, 
            t.data AS data_transfusao, 
            h.nome AS hospital, 
            d.tipo_sanguineo
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

if (!empty($tipoSanguineo)) {
    $sql .= " AND d.tipo_sanguineo = ?";
    $params[] = $tipoSanguineo;
    $types .= "s";
}

if (!empty($hospital)) {
    $sql .= " AND h.id = ?";
    $params[] = $hospital;
    $types .= "s";
}

$sql .= " ORDER BY t.data DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$transfusoes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$statsQuery = "SELECT COUNT(*) AS total, d.tipo_sanguineo FROM transfusoes t JOIN bolsas_sangue b ON t.id_bolsa = b.id JOIN dadores d ON b.id_dador = d.id GROUP BY d.tipo_sanguineo";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_all(MYSQLI_ASSOC);
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    <label for="Estatísticas" class="form-label">Estatísticas</label> 
   
    <div class="row mb-5">
        <?php foreach ($stats as $stat): ?>
            <div class="col-md-1 d-flex justify-content-center">
                <div class="card shadow-sm text-center p-3 w-100">
                    <h5 class="text-danger"> <?= htmlspecialchars($stat['tipo_sanguineo']) ?> </h5>
                    <h3 class="fw-bold"> <?= htmlspecialchars($stat['total']) ?> </h3>
                    <p>Total</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="card border-0 p-0">
        <div class="card-body p-0">
            <form method="GET" class="mb-4 p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($dataInicio) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= htmlspecialchars($dataFim) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                        <select class="form-control" id="tipo_sanguineo" name="tipo_sanguineo">
                            <option value="">Todos</option>
                            <?php foreach ($tiposSanguineos as $tipo): ?>
                                <option value="<?= htmlspecialchars($tipo['tipo_sanguineo']) ?>" <?= $tipoSanguineo == $tipo['tipo_sanguineo'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo['tipo_sanguineo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="hospital" class="form-label">Hospital</label>
                        <select class="form-control" id="hospital" name="hospital">
                            <option value="">Todos</option>
                            <?php foreach ($hospitais as $hosp): ?>
                                <option value="<?= htmlspecialchars($hosp['id']) ?>" <?= $hospital == $hosp['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hosp['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar 
                        </button>
                        <a href="transfusoes.php" class="btn btn-danger">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Remover Filtros 
                        </a>
                    </div>
                </div>
            </form>

           
            <div class="text-end mb-3">
                <a href="adicionar_transfusao.php" class="btn btn-sm text-white" style="background-color: #202d3b;">
                    <i class="fa-solid fa-plus"></i> Novo
                </a>
            </div>

            
            <div class="table-responsive py-2">
                <table class="table text-nowrap table-hover">
                    <caption>Total de Transfusões: <?php echo count($transfusoes); ?></caption>
                    <thead>
                        <tr>
                            <th class="text-center">ID Transfusão</th>
                            <th class="text-center">ID Bolsa</th>
                            <th class="text-center">Tipo Sanguíneo</th>
                            <th class="text-center">Número de Utente</th>
                            <th class="text-center">Data da Transfusão</th>
                            <th>Hospital</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transfusoes)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Nenhum resultado encontrado com os filtros aplicados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transfusoes as $row): ?>
                                <tr>
                                    <th class="text-center"> <?= htmlspecialchars($row['id']) ?> </th>
                                    <td class="text-center"> <?= htmlspecialchars($row['id_bolsa']) ?> </td>
                                    <td class="text-center"> <?= htmlspecialchars($row['tipo_sanguineo']) ?> </td>
                                    <td class="text-center"> <?= htmlspecialchars($row['n_utente']) ?> </td>
                                    <td class="text-center"> <?= date("d/m/Y", strtotime($row['data_transfusao'])) ?> </td>
                                    <td> <?= htmlspecialchars($row['hospital']) ?> </td>
                                    <td class="text-center">
                                        <a href="editar_transfusao.php?id=<?= $row['id'] ?>" class="text-decoration-none" style="color: #202d3b;">
                                            <i class="fa-solid fa-file-pen"></i>
                                        </a>
                                        <a href="excluir_transfusao.php?id=<?= $row['id'] ?>" aria-label="Close" onclick="return confirm('Tem certeza que deseja excluir esta transfusão?');">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>