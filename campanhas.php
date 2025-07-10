<?php
include 'partials/header.php';
require_once 'includes/db_connection.php';

// Atualizar progresso das campanhas
$pdo->query("CALL atualizar_progresso_campanhas()");

// Configurações básicas
$pageTitle = "Campanhas de Doação";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Campanhas', 'url' => '#', 'active' => true]
];

// Obter tipos sanguíneos para filtros
$tiposSanguineos = $pdo->query("SELECT DISTINCT tipo_sanguineo FROM dadores")->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$status = $_GET['status'] ?? '';
$tipoSanguineo = $_GET['tipo_sanguineo'] ?? '';
$prioridade = $_GET['prioridade'] ?? '';

// Configuração de paginação
$registrosPorPagina = 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $registrosPorPagina;


// Consulta para total de registros
$sqlCount = "SELECT COUNT(*) AS total FROM campanhas c
             LEFT JOIN campanhas_tipos_sanguineos cts ON c.id = cts.id_campanha
             WHERE 1";

$paramsCount = [];
$whereConditionsCount = [];

// Filtro de status (CORREÇÃO ADICIONADA)
if (!empty($status)) {
    if ($status == 'ativas') {
        $whereConditionsCount[] = "c.data_fim >= CURDATE()";
    } elseif ($status == 'finalizadas') {
        $whereConditionsCount[] = "c.data_fim < CURDATE()";
    }
}

if (!empty($tipoSanguineo)) {
    $whereConditionsCount[] = "(cts.tipo_sanguineo = ? OR cts.id_campanha IS NULL)";
    $paramsCount[] = $tipoSanguineo;
}

if (!empty($prioridade)) {
    $whereConditionsCount[] = "c.prioridade = ?";
    $paramsCount[] = $prioridade;
}

if (!empty($whereConditionsCount)) {
    $sqlCount .= " AND " . implode(" AND ", $whereConditionsCount);
}

$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($paramsCount);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta principal com paginação
$sql = "SELECT c.*, GROUP_CONCAT(DISTINCT cts.tipo_sanguineo) as tipos_sanguineos
        FROM campanhas c
        LEFT JOIN campanhas_tipos_sanguineos cts ON c.id = cts.id_campanha
        WHERE 1";

$params = [];
$whereConditions = [];

// Filtro de status (CORREÇÃO ADICIONADA)
if (!empty($status)) {
    if ($status == 'ativas') {
        $whereConditions[] = "c.data_fim >= CURDATE()";
    } elseif ($status == 'finalizadas') {
        $whereConditions[] = "c.data_fim < CURDATE()";
    }
}

if (!empty($tipoSanguineo)) {
    $whereConditions[] = "(cts.tipo_sanguineo = ? OR cts.id_campanha IS NULL)";
    $params[] = $tipoSanguineo;
}

if (!empty($prioridade)) {
    $whereConditions[] = "c.prioridade = ?";
    $params[] = $prioridade;
}

if (!empty($whereConditions)) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
}

$sql .= " GROUP BY c.id
          ORDER BY 
            CASE 
              WHEN c.data_fim >= CURDATE() THEN 0 
              ELSE 1 
            END,
            c.data_fim ASC,
            CASE c.prioridade
              WHEN 'critica' THEN 0
              WHEN 'urgente' THEN 1
              ELSE 2
            END
          LIMIT " . (int)$registrosPorPagina . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$campanhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas
$statsQuery = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN data_fim >= CURDATE() THEN 1 ELSE 0 END) as ativas,
                SUM(CASE WHEN data_fim < CURDATE() THEN 1 ELSE 0 END) as finalizadas,
                SUM(CASE WHEN prioridade = 'critica' THEN 1 ELSE 0 END) as criticas,
                SUM(CASE WHEN prioridade = 'urgente' THEN 1 ELSE 0 END) as urgentes,
                SUM(CASE WHEN prioridade = 'normal' THEN 1 ELSE 0 END) as normais,
                SUM(arrecadado) as total_arrecadado,
                SUM(meta) as total_meta,
                ROUND(SUM(arrecadado) / SUM(meta) * 100, 2) as progresso_geral
              FROM campanhas";

// Estatísticas filtradas (CORREÇÃO ATUALIZADA)
$statsFilteredData = null;
if (!empty($whereConditions)) {
    // Construir a mesma condição WHERE da consulta principal
    $statsFilteredQuery = "SELECT 
                            COALESCE(COUNT(*), 0) as total_filtrado,
                            COALESCE(SUM(CASE WHEN c.data_fim >= CURDATE() THEN 1 ELSE 0 END), 0) as ativas_filtrado,
                            COALESCE(SUM(CASE WHEN c.data_fim < CURDATE() THEN 1 ELSE 0 END), 0) as finalizadas_filtrado,
                            COALESCE(SUM(CASE WHEN c.prioridade = 'critica' THEN 1 ELSE 0 END), 0) as criticas_filtrado,
                            COALESCE(SUM(CASE WHEN c.prioridade = 'urgente' THEN 1 ELSE 0 END), 0) as urgentes_filtrado,
                            COALESCE(SUM(CASE WHEN c.prioridade = 'normal' THEN 1 ELSE 0 END), 0) as normais_filtrado,
                            COALESCE(SUM(c.arrecadado), 0) as total_arrecadado_filtrado,
                            COALESCE(SUM(c.meta), 0) as total_meta_filtrado,
                            COALESCE(ROUND(SUM(c.arrecadado) / SUM(c.meta) * 100, 2), 0) as progresso_geral_filtrado
                          FROM campanhas c
                          LEFT JOIN campanhas_tipos_sanguineos cts ON c.id = cts.id_campanha
                          WHERE " . implode(" AND ", $whereConditions);

    $statsFiltered = $pdo->prepare($statsFilteredQuery);
    $statsFiltered->execute($params);
    $statsFilteredData = $statsFiltered->fetch(PDO::FETCH_ASSOC);
    if (!$statsFilteredData) {
        $statsFilteredData = [
            'total_filtrado' => 0,
            'ativas_filtrado' => 0,
            'finalizadas_filtrado' => 0,
            'criticas_filtrado' => 0,
            'urgentes_filtrado' => 0,
            'normais_filtrado' => 0,
            'total_arrecadado_filtrado' => 0,
            'total_meta_filtrado' => 0,
            'progresso_geral_filtrado' => 0
        ];
    }
}
$stats = $pdo->query($statsQuery)->fetch(PDO::FETCH_ASSOC);

// Mensagens
$success = $_GET['success'] ?? null;
$updated = $_GET['updated'] ?? null;
$new_id = $_GET['new_id'] ?? null;
$deleted = $_GET['deleted'] ?? null;
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Campanha criada com sucesso!</h4>
                <p class="mb-0">A nova campanha #<?= htmlspecialchars($new_id, ENT_QUOTES, 'UTF-8') ?> foi adicionada ao sistema.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($updated): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Campanha atualizada com sucesso!</h4>
                <p class="mb-0">As alterações na campanha #<?= htmlspecialchars($new_id ?? $_GET['id'] ?? '', ENT_QUOTES, 'UTF-8') ?> foram guardadas.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($deleted): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Campanha excluída com sucesso!</h4>
                <p class="mb-0">A campanha foi removida do sistema.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Seção de Estatísticas -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-chart-pie me-2"></i>Estatísticas de Campanhas
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Estatísticas Gerais -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Estatísticas Gerais</h6>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="card bg-light bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-danger"><?= $stats['total'] ?></h3>
                                    <p class="text-muted mb-0">Total de Campanhas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-success"><?= $stats['ativas'] ?></h3>
                                    <p class="text-muted mb-0">Ativas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-secondary"><?= $stats['finalizadas'] ?></h3>
                                    <p class="text-muted mb-0">Finalizadas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h3><?= $stats['total_arrecadado'] ?>/<?= $stats['total_meta'] ?></h3>
                                    <p class="text-muted mb-0">Doações (Total)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estatísticas com Filtros (se aplicado) -->
                <div class="col-md-6">
                    <?php if (!empty($whereConditions)): ?>
                        <h6 class="border-bottom pb-2 mb-3">Estatísticas Filtradas</h6>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="card bg-light bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-danger"><?= $statsFilteredData['total_filtrado'] ?></h3>
                                        <p class="text-muted mb-0">Campanhas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-light bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-success"><?= $statsFilteredData['ativas_filtrado'] ?></h3>
                                        <p class="text-muted mb-0">Ativas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-light bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-secondary"><?= $statsFilteredData['finalizadas_filtrado'] ?></h3>
                                        <p class="text-muted mb-0">Finalizadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-light bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3><?= $statsFilteredData['total_arrecadado_filtrado'] ?>/<?= $statsFilteredData['total_meta_filtrado'] ?></h3>
                                        <p class="text-muted mb-0">Doações</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <h6 class="border-bottom pb-2 mb-3">Distribuição por Prioridade</h6>
                        <div class="row">
                            <div class="col-4 mb-3">
                                <div class="card bg-danger bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-danger"><?= $stats['criticas'] ?></h3>
                                        <p class="text-muted mb-0">Críticas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="card bg-warning bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-warning"><?= $stats['urgentes'] ?></h3>
                                        <p class="text-muted mb-0">Urgentes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="card bg-primary bg-opacity-10 border-0 h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary"><?= $stats['normais'] ?></h3>
                                        <p class="text-muted mb-0">Normais</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Progresso Geral</span>
                                <span><?= $stats['progresso_geral'] ?>%</span>
                            </div>
                            <div class="progress bg-light" style="height: 10px;">
                                <div class="progress-bar 
                                    <?= $stats['progresso_geral'] >= 100 ? 'bg-success' : 
                                       ($stats['progresso_geral'] > 50 ? 'bg-primary' : 
                                       ($stats['progresso_geral'] > 25 ? 'bg-warning' : 'bg-danger')) ?>" 
                                    style="width: <?= $stats['progresso_geral'] ?>%">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Tabela -->
    <div class="card border-0 p-0">
        <div class="card-body p-0">
            <form method="GET" class="mb-4 p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="ativas" <?= $status == 'ativas' ? 'selected' : '' ?>>Ativas</option>
                            <option value="finalizadas" <?= $status == 'finalizadas' ? 'selected' : '' ?>>Finalizadas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                        <select class="form-control" id="tipo_sanguineo" name="tipo_sanguineo">
                            <option value="">Todos</option>
                            <?php foreach ($tiposSanguineos as $tipo): ?>
                                <option value="<?= htmlspecialchars($tipo['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= $tipoSanguineo == $tipo['tipo_sanguineo'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="prioridade" class="form-label">Prioridade</label>
                        <select class="form-control" id="prioridade" name="prioridade">
                            <option value="">Todas</option>
                            <option value="critica" <?= $prioridade == 'critica' ? 'selected' : '' ?>>Crítica</option>
                            <option value="urgente" <?= $prioridade == 'urgente' ? 'selected' : '' ?>>Urgente</option>
                            <option value="normal" <?= $prioridade == 'normal' ? 'selected' : '' ?>>Normal</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar 
                        </button>
                        <a href="campanhas.php" class="btn btn-danger">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Limpar Filtros 
                        </a>
                        <a href="campanha-adicionar.php" class="btn text-white float-end" style="background-color: #202d3b;">
                            <i class="fa-solid fa-plus me-2"></i> Nova Campanha
                        </a>
                    </div>
                </div>
                <input type="hidden" name="pagina" value="1">
            </form>

            <div class="table-responsive py-2 px-3">
                <table class="table text-nowrap table-hover">
                    <caption>Total de Campanhas: <?= $totalRegistros ?> | Página <?= $paginaAtual ?> de <?= $totalPaginas ?></caption>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Título</th>
                            <th class="text-center">Tipos Sanguíneos</th>
                            <th class="text-center">Período</th>
                            <th class="text-center">Progresso</th>
                            <th class="text-center">Prioridade</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (empty($campanhas)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-database me-2"></i>Nenhum registo encontrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($campanhas as $campanha): 
                                $progresso = min(100, ($campanha['arrecadado'] / $campanha['meta']) * 100);
                                $isAtiva = strtotime($campanha['data_fim']) >= time();
                            ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($campanha['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($campanha['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <div class="small text-muted"><?= htmlspecialchars($campanha['descricao'], ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($campanha['tipos_sanguineos'])): ?>
                                            <?php foreach (explode(',', $campanha['tipos_sanguineos']) as $tipo): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger me-1"><?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Todos</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= date('d/m/Y', strtotime($campanha['data_inicio'])) ?> - 
                                        <?= date('d/m/Y', strtotime($campanha['data_fim'])) ?>
                                        <?php if ($isAtiva): ?>
                                            <div class="small text-muted">
                                                <?= ceil((strtotime($campanha['data_fim']) - time()) / (60 * 60 * 24)) ?> dias restantes
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress bg-light mx-auto" style="height: 8px; width: 80px;">
                                            <div class="progress-bar 
                                                <?= $progresso >= 100 ? 'bg-success' : ($progresso > 50 ? 'bg-primary' : ($progresso > 25 ? 'bg-warning' : 'bg-danger')) ?>" 
                                                style="width: <?= $progresso ?>%">
                                            </div>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <?= $campanha['arrecadado'] ?>/<?= $campanha['meta'] ?> (<?= round($progresso) ?>%)
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $badgeClass = [
                                            'critica' => 'danger',
                                            'urgente' => 'warning',
                                            'normal' => 'primary'
                                        ][$campanha['prioridade']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?> bg-opacity-10 text-<?= $badgeClass ?>">
                                            <?= ucfirst($campanha['prioridade']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="campanha-editar.php?id=<?= $campanha['id'] ?>" class="text-decoration-none me-2" title="Editar">
                                            <i class="fa-solid fa-file-pen text-dark"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none text-danger" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteModalCampanha" 
                                           data-campanha-id="<?= $campanha['id'] ?>"
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
<div class="modal fade" id="deleteModalCampanha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem a certeza que deseja eliminar esta campanha? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonCampanha" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var deleteModal = document.getElementById("deleteModalCampanha");
    deleteModal.addEventListener("show.bs.modal", function(event) {
        var button = event.relatedTarget;
        var campanhaId = button.getAttribute("data-campanha-id");
        var confirmButton = document.getElementById("deleteConfirmButtonCampanha");
        confirmButton.href = "campanha-excluir.php?id=" + campanhaId;
    });
});
</script>

<?php include 'partials/footer.php'; ?>