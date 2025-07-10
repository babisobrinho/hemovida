<?php
include 'partials/header.php';
require_once 'includes/db_connection.php'; // Inclui a conexão PDO

$pageTitle = "Transfusões de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Transfusões', 'url' => '#', 'active' => true]
];

// Obter tipos sanguíneos
$tiposSanguineosQuery = "SELECT DISTINCT tipo_sanguineo FROM dadores";
$tiposSanguineosStmt = $pdo->query($tiposSanguineosQuery);
$tiposSanguineos = $tiposSanguineosStmt->fetchAll(PDO::FETCH_ASSOC);

// Obter hospitais
$hospitaisQuery = "SELECT id, nome FROM hospitais";
$hospitaisStmt = $pdo->query($hospitaisQuery);
$hospitais = $hospitaisStmt->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$tipoSanguineo = $_GET['tipo_sanguineo'] ?? '';
$hospital = $_GET['hospital'] ?? '';

// Configuração de paginação
$registrosPorPagina = 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Consulta para total de registros
$sqlCount = "SELECT COUNT(*) AS total FROM transfusoes t
             JOIN hospitais h ON t.id_hospital = h.id
             JOIN bolsas_sangue b ON t.id_bolsa = b.id
             JOIN dadores d ON b.id_dador = d.id
             WHERE 1";

$paramsCount = [];
$whereConditions = [];

if (!empty($dataInicio)) {
    $whereConditions[] = "t.data >= ?";
    $paramsCount[] = $dataInicio;
}

if (!empty($dataFim)) {
    $whereConditions[] = "t.data <= ?";
    $paramsCount[] = $dataFim;
}

if (!empty($tipoSanguineo)) {
    $whereConditions[] = "d.tipo_sanguineo = ?";
    $paramsCount[] = $tipoSanguineo;
}

if (!empty($hospital)) {
    $whereConditions[] = "h.id = ?";
    $paramsCount[] = $hospital;
}

if (!empty($whereConditions)) {
    $sqlCount .= " AND " . implode(" AND ", $whereConditions);
}

$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($paramsCount);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta principal com paginação
$sql = "SELECT t.id, t.id_bolsa, t.n_utente, t.data AS data_transfusao, 
               h.nome AS hospital, d.tipo_sanguineo
        FROM transfusoes t
        JOIN hospitais h ON t.id_hospital = h.id
        JOIN bolsas_sangue b ON t.id_bolsa = b.id
        JOIN dadores d ON b.id_dador = d.id
        WHERE 1";

$params = [];
$whereConditions = [];

if (!empty($dataInicio)) {
    $whereConditions[] = "t.data >= ?";
    $params[] = $dataInicio;
}

if (!empty($dataFim)) {
    $whereConditions[] = "t.data <= ?";
    $params[] = $dataFim;
}

if (!empty($tipoSanguineo)) {
    $whereConditions[] = "d.tipo_sanguineo = ?";
    $params[] = $tipoSanguineo;
}

if (!empty($hospital)) {
    $whereConditions[] = "h.id = ?";
    $params[] = $hospital;
}

if (!empty($whereConditions)) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY t.data DESC LIMIT ? OFFSET ?";
$params[] = $registrosPorPagina;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transfusoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas
$allTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$statsQuery = "SELECT d.tipo_sanguineo, COUNT(*) AS total 
               FROM transfusoes t 
               JOIN bolsas_sangue b ON t.id_bolsa = b.id 
               JOIN dadores d ON b.id_dador = d.id 
               GROUP BY d.tipo_sanguineo
               ORDER BY total DESC";
$statsStmt = $pdo->query($statsQuery);
$dbStats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

// Obter tipo sanguíneo com maior demanda
$tipoMaisNecessario = !empty($dbStats) ? $dbStats[0]['tipo_sanguineo'] : 'N/A';
$totalTipoMaisNecessario = !empty($dbStats) ? $dbStats[0]['total'] : 0;

$stats = [];
foreach ($allTypes as $type) {
    $found = false;
    foreach ($dbStats as $dbStat) {
        if ($dbStat['tipo_sanguineo'] == $type) {
            $stats[] = $dbStat;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $stats[] = ['tipo_sanguineo' => $type, 'total' => 0];
    }
}

$totalGeralQuery = "SELECT COUNT(*) as total FROM transfusoes";
$totalGeralStmt = $pdo->query($totalGeralQuery);
$totalGeral = $totalGeralStmt->fetchColumn();

$topHospitalQuery = "SELECT h.nome, COUNT(*) as total 
                   FROM transfusoes t 
                   JOIN hospitais h ON t.id_hospital = h.id 
                   GROUP BY h.nome 
                   ORDER BY total DESC 
                   LIMIT 1";
$topHospitalStmt = $pdo->query($topHospitalQuery);
$topHospital = $topHospitalStmt->fetch(PDO::FETCH_ASSOC);

$diasFuncionamento = 365;
$mediaDiaria = $totalGeral > 0 ? round($totalGeral / $diasFuncionamento, 1) : 0;

// Mensagens
$success = $_GET['success'] ?? null;
$updated = $_GET['updated'] ?? null;
$new_id = $_GET['new_id'] ?? null;
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Transfusão registada com sucesso!</h4>
                <p class="mb-0">A nova transfusão #<?= htmlspecialchars($new_id, ENT_QUOTES, 'UTF-8') ?> foi adicionada ao sistema.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($updated): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Transfusão atualizada com sucesso!</h4>
                <p class="mb-0">As alterações na transfusão #<?= htmlspecialchars($new_id ?? $_GET['id'] ?? '', ENT_QUOTES, 'UTF-8') ?> foram guardadas.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Seção de Estatísticas -->
    <div class="container-fluid mb-5 px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
                <h5 class="mb-0 font-weight-bold text-danger">
                    <i class="fas fa-chart-line me-2"></i>Estatísticas de Transfusões
                </h5>
                    
                <ul class="nav nav-tabs border-0 mt-3" id="statsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 border-0 fw-bold <?= (!isset($_GET['tab']) || $_GET['tab']) === 'overview' ? 'active text-danger' : 'text-body-secondary' ?> link-danger link-opacity-75-hover" 
                        id="overview-tab" 
                        data-bs-toggle="tab" 
                        href="#overview" 
                        role="tab">
                            <i class="fas fa-eye me-1"></i> Visão Geral
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 border-0 fw-bold <?= (isset($_GET['tab']) && $_GET['tab']) === 'bloodtypes' ? 'active text-danger' : 'text-body-secondary' ?> link-danger link-opacity-75-hover" 
                        id="bloodtypes-tab" 
                        data-bs-toggle="tab" 
                        href="#bloodtypes" 
                        role="tab">
                            <i class="fas fa-heartbeat me-1"></i> Tipos Sanguíneos
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-0">
                <div class="tab-content" id="statsTabContent">
                    <!-- Tab 1: Visão Geral -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row no-gutters">
                            <!-- Total Geral -->
                            <div class="col-lg-2 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-tint text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">TOTAL</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($totalGeral) ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">Transfusões</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hospital Mais Ativo -->
                            <div class="col-lg-5 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-hospital text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">HOSPITAL COM MAIS ATIVIDADE</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= htmlspecialchars($topHospital['nome'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($topHospital['nome'] ?? 'N/A')?>
                                            </h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                <?= ($topHospital['total'] ?? 0) ?> transfusões
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Média Diária -->
                            <div class="col-lg-2 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-calendar-day text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">MÉDIA DIÁRIA</p>
                                            <h3 class="mb-0 fs-4 font-weight-bold mx-2"><?= $mediaDiaria ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">por dia</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tipo Mais Necessitado -->
                            <div class="col-lg-3 col-md-6">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">TIPO MAIS SOLICITADO</p>
                                            <h3 class="mb-0  fs-4 mx-2 font-weight-bold"><?= htmlspecialchars($tipoMaisNecessario, ENT_QUOTES, 'UTF-8') ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                <?= $totalTipoMaisNecessario ?> solicitações
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab 2: Tipos Sanguíneos -->
                    <div class="tab-pane fade" id="bloodtypes" role="tabpanel">
                        <div class="p-4">
                            <h6 class="font-weight-bold text-muted mb-3">Distribuição por Tipo Sanguíneo</h6>
                            <div class="row">
                                <?php 
                                $totalTransfusoes = $totalGeral > 0 ? $totalGeral : 1;
                                
                                foreach ($stats as $stat): 
                                    $totalTipo = $stat['total'];
                                    $percentagem = ($totalTipo / $totalTransfusoes) * 100;
                                    $percentagemFormatada = number_format($percentagem, 1);
                                ?>
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1">
                                                    <?= htmlspecialchars($stat['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span class="font-weight-bold h5 mb-0"><?= $totalTipo ?></span>
                                            </div>
                                            <div class="progress bg-light" style="height: 8px;">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                     style="width: <?= $percentagem ?>%" 
                                                     aria-valuenow="<?= $percentagem ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <small class="text-muted">Percentagem</small>
                                                <small class="font-weight-bold text-danger">
                                                    <?= $percentagemFormatada ?>%
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-end small text-muted mt-2">
                                * Baseado em <?= $totalGeral ?> transfusões registadas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Tabela -->
    <div class="card border-0 p-0">
        <div class="card-body p-0">
            <form method="GET" class="mb-4 p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($dataInicio, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= htmlspecialchars($dataFim, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                        <select class="form-control" id="tipo_sanguineo" name="tipo_sanguineo">
                            <option value="">Todos</option>
                            <?php foreach ($tiposSanguineos as $tipo): ?>
                                <option value="<?= htmlspecialchars($tipo['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>" <?= $tipoSanguineo == $tipo['tipo_sanguineo'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="hospital" class="form-label">Hospital</label>
                        <select class="form-control" id="hospital" name="hospital">
                            <option value="">Todos</option>
                            <?php foreach ($hospitais as $hosp): ?>
                                <option value="<?= htmlspecialchars($hosp['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $hospital == $hosp['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hosp['nome'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar 
                        </button>
                        <a href="transfusoes.php" class="btn btn-danger">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Limpar Filtros 
                        </a>
                    </div>
                </div>
                <input type="hidden" name="pagina" value="1">
            </form>

            <div class="table-responsive py-2 px-3">
                <table class="table text-nowrap table-hover">
                    <caption>Total de Transfusões: <?= $totalRegistros ?> | Página <?= $paginaAtual ?> de <?= $totalPaginas ?></caption>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Bolsa</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Utente</th>
                            <th class="text-center">Data</th>
                            <th>Hospital</th>
                            <th scope="col">
                                <a href="transfusao-adicionar.php" class="btn btn-sm text-white" style="background-color: #202d3b;">
                                    <i class="fa-solid fa-plus"></i> Nova
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (empty($transfusoes)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-database me-2"></i>Nenhum registo encontrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transfusoes as $row): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['id_bolsa'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['n_utente'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= date("d/m/Y", strtotime($row['data_transfusao'])) ?></td>
                                    <td><?= htmlspecialchars($row['hospital'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="m-5">
                                        <a href="transfusao-editar.php?id=<?= $row['id'] ?>" class="text-decoration-none me-2" title="Editar">
                                            <i class="fa-solid fa-file-pen text-dark"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none text-danger" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteModalTransfusao" 
                                           data-transfusao-id="<?= $row['id'] ?>"
                                           title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Page navigation" class="px-3 pb-3">
                    <ul class="pagination justify-content-center">
                        <?php if ($paginaAtual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => 1])) ?>" style="color: #202d3b;">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual - 1])) ?>" style="color: #202d3b;">
                                    <span aria-hidden="true">‹</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $inicio = max(1, $paginaAtual - 2);
                        $fim = min($totalPaginas, $paginaAtual + 2);
                        
                        if ($inicio > 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link" style="color: #202d3b;">...</span>
                            </li>
                        <?php endif;
                        
                        for ($i = $inicio; $i <= $fim; $i++): ?>
                            <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>" 
                                   style="<?= $i == $paginaAtual ? 'background-color: #BB2D3B; color: white; border-color: #BB2D3B;' : 'color: #202d3b;' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor;
                        
                        if ($fim < $totalPaginas): ?>
                            <li class="page-item disabled">
                                <span class="page-link" style="color: #202d3b;">...</span>
                            </li>
                        <?php endif; ?>

                        <?php if ($paginaAtual < $totalPaginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual + 1])) ?>" style="color: #202d3b;">
                                    <span aria-hidden="true">›</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $totalPaginas])) ?>" style="color: #202d3b;">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="deleteModalTransfusao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem a certeza que deseja eliminar esta transfusão? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonTransfusao" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
// Atualiza a URL quando as abas são alteradas e mantém o estado
document.querySelectorAll('#statsTabs .nav-link').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function(e) {
        const tabId = e.target.getAttribute('href').substring(1);
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url);
        
        // Atualiza as classes dos links
        document.querySelectorAll('#statsTabs .nav-link').forEach(link => {
            link.classList.remove('active', 'text-danger');
            link.classList.add('text-secondary');
        });
        e.target.classList.add('active', 'text-danger');
        e.target.classList.remove('text-secondary');
    });
});
document.addEventListener("DOMContentLoaded", function() {
   
    const tabLinks = document.querySelectorAll('#statsTabs .nav-link.hover-effect');
    
    tabLinks.forEach(link => {
        link.classList.add('transition-all', 'duration-300', 'ease-in-out');
        
        link.addEventListener('mouseenter', () => {
            if (!link.classList.contains('active')) {
                link.classList.remove('text-secondary');
                link.classList.add('text-danger-emphasis'); // Vermelho escuro do Bootstrap
            }
        });
        
        link.addEventListener('mouseleave', () => {
            if (!link.classList.contains('active')) {
                link.classList.remove('text-danger-emphasis');
                link.classList.add('text-secondary');
            }
        });
    });
});
// Ativa a aba correta ao carregar a página
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = 'overview';
    
    // Ativa a aba correta
    const tabElement = document.querySelector(`#statsTabs a[href="#${activeTab}"]`);
    if (tabElement) {
        new bootstrap.Tab(tabElement).show();
    }
});

document.addEventListener("DOMContentLoaded", function() {
    var deleteModal = document.getElementById("deleteModalTransfusao");
    deleteModal.addEventListener("show.bs.modal", function(event) {
        var button = event.relatedTarget;
        var transfusaoId = button.getAttribute("data-transfusao-id");
        var confirmButton = document.getElementById("deleteConfirmButtonTransfusao");
        confirmButton.href = "transfusao-excluir.php?id=" + transfusaoId;
    });
});
</script>

<?php include 'partials/footer.php'; ?>