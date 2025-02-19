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
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$tipoSanguineo = $_GET['tipo_sanguineo'] ?? '';
$hospital = $_GET['hospital'] ?? '';
$sql = "SELECT 
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
    $sql .= " AND h.nome LIKE ?";
    $params[] = '%' . $hospital . '%';
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
$sqlEstatisticas = "SELECT d.tipo_sanguineo, COUNT(*) AS total_transfusoes
                    FROM transfusoes t
                    JOIN bolsas_sangue b ON t.id_bolsa = b.id
                    JOIN dadores d ON b.id_dador = d.id
                    GROUP BY d.tipo_sanguineo";
$resultEstatisticas = $conn->query($sqlEstatisticas);
if ($resultEstatisticas === false) {
    die("Erro ao consultar estatísticas: " . $conn->error);
}
$estatisticas = $resultEstatisticas->fetch_all(MYSQLI_ASSOC);
?>
<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    <div class="card border-0 p-0">
        <div class="card-body p-0">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="filtros-tab" data-bs-toggle="tab" data-bs-target="#filtros" type="button" role="tab" aria-controls="filtros" aria-selected="true">Filtros</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="estatisticas-tab" data-bs-toggle="tab" data-bs-target="#estatisticas" type="button" role="tab" aria-controls="estatisticas" aria-selected="false">Estatísticas</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="filtros" role="tabpanel" aria-labelledby="filtros-tab">
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
                                <select class="form-select" id="tipo_sanguineo" name="tipo_sanguineo">
                                    <option value="">Todos</option>
                                    <option value="A+" <?= $tipoSanguineo === 'A+' ? 'selected' : '' ?>>A+</option>
                                    <option value="A-" <?= $tipoSanguineo === 'A-' ? 'selected' : '' ?>>A-</option>
                                    <option value="B+" <?= $tipoSanguineo === 'B+' ? 'selected' : '' ?>>B+</option>
                                    <option value="B-" <?= $tipoSanguineo === 'B-' ? 'selected' : '' ?>>B-</option>
                                    <option value="AB+" <?= $tipoSanguineo === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                    <option value="AB-" <?= $tipoSanguineo === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                    <option value="O+" <?= $tipoSanguineo === 'O+' ? 'selected' : '' ?>>O+</option>
                                    <option value="O-" <?= $tipoSanguineo === 'O-' ? 'selected' : '' ?>>O-</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="hospital" class="form-label">Hospital</label>
                                <input type="text" class="form-control" id="hospital" name="hospital" placeholder="Nome do Hospital" value="<?= htmlspecialchars($hospital) ?>">
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn w-100 text-white" style="background-color: #202D3B; height: 48px; font-size: 16px;">Filtrar</button>
                                <?php if (!empty($dataInicio) || !empty($dataFim) || !empty($tipoSanguineo) || !empty($hospital)): ?>
                                    <a href="?data_inicio=&data_fim=&tipo_sanguineo=&hospital=" class="btn w-100 text-white" style="background-color: #FF3031; height: 48px; font-size: 16px;">Limpar Filtros</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="estatisticas" role="tabpanel" aria-labelledby="estatisticas-tab">
                    <div class="p-3">
                        <h5>Estatísticas de Transfusões</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tipo Sanguíneo</th>
                                        <th>Total de Transfusões</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estatisticas as $estatistica): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($estatistica['tipo_sanguineo']) ?></td>
                                            <td><?= htmlspecialchars($estatistica['total_transfusoes']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive mt-4 p-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">ID Bolsa</th>
                            <th class="text-center">Tipo Sanguíneo</th>
                            <th class="text-center">Número de Utente</th>
                            <th class="text-center">Data da Transfusão</th>
                            <th>Hospital</th> <!-- Não centralizar esta coluna -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transfusoes)): ?>
                            <?php foreach ($transfusoes as $row): ?>
                                <tr>
                                    <th class="text-center"><?= htmlspecialchars($row['id_bolsa']) ?></th>
                                    <td class="text-center"><?= htmlspecialchars($row['tipo_sanguineo']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['n_utente']) ?></td>
                                    <th class="text-center"><?= date("d/m/Y", strtotime($row['data_transfusao'])) ?></th>
                                    <td><?= htmlspecialchars($row['hospital']) ?></td> <!-- Não centralizar esta coluna -->
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-danger fw-bold py-3">Nenhuma transfusão encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>