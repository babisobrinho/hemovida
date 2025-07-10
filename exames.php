<?php
include 'partials/header.php';
require_once 'includes/db_connection.php'; 
$pageTitle = "Análises Clínicas";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Exames', 'url' => '#', 'active' => true]
];

// Filtros
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$resultado = $_GET['resultado'] ?? '';
$id_bolsa = $_GET['id_bolsa'] ?? '';

// Configuração de paginação
$registrosPorPagina = 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Consulta para total de registros
$sqlCount = "SELECT COUNT(*) AS total FROM exames WHERE 1";
$paramsCount = [];
$whereConditions = [];

if (!empty($dataInicio)) {
    $whereConditions[] = "data >= ?";
    $paramsCount[] = $dataInicio;
}

if (!empty($dataFim)) {
    $whereConditions[] = "data <= ?";
    $paramsCount[] = $dataFim;
}

if (!empty($resultado)) {
    $whereConditions[] = "resultado = ?";
    $paramsCount[] = $resultado;
}

if (!empty($id_bolsa)) {
    $whereConditions[] = "id_bolsa = ?";
    $paramsCount[] = $id_bolsa;
}

if (!empty($whereConditions)) {
    $sqlCount .= " AND " . implode(" AND ", $whereConditions);
}

$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($paramsCount);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta principal com paginação
$sql = "SELECT * FROM exames WHERE 1";
$params = $paramsCount; // Mesmos parâmetros do count

if (!empty($whereConditions)) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY data DESC LIMIT ? OFFSET ?";
$params[] = $registrosPorPagina;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exames = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas
$totalExames = $pdo->query("SELECT COUNT(*) AS total FROM exames")->fetchColumn();
$aprovadosQuery = "SELECT COUNT(*) AS total FROM exames WHERE resultado = 'Aprovado'";
$aprovados = $pdo->query($aprovadosQuery)->fetchColumn();

$reprovadosQuery = "SELECT COUNT(*) AS total FROM exames WHERE resultado = 'Reprovado'";
$reprovados = $pdo->query($reprovadosQuery)->fetchColumn();

$emAnaliseQuery = "SELECT COUNT(*) AS total FROM exames WHERE resultado = 'Em Análise'";
$emAnalise = $pdo->query($emAnaliseQuery)->fetchColumn();

$resultadosQuery = "SELECT resultado, COUNT(*) AS total FROM exames GROUP BY resultado";
$resultadosStats = $pdo->query($resultadosQuery)->fetchAll(PDO::FETCH_ASSOC);

// Obter bolsas para o filtro
$bolsasQuery = "SELECT id FROM bolsas_sangue";
$bolsas = $pdo->query($bolsasQuery)->fetchAll(PDO::FETCH_ASSOC);

// Mensagens
$success = $_GET['success'] ?? null;
$new_id = $_GET['new_id'] ?? null;
$updated = $_GET['updated'] ?? null;
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
                <h4 class="alert-heading mb-1">Exame registado com sucesso!</h4>
                <p class="mb-0">O novo exame #<?= htmlspecialchars($new_id, ENT_QUOTES, 'UTF-8') ?> foi adicionado ao sistema.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($updated): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Exame atualizado com sucesso!</h4>
                <p class="mb-0">As alterações no exame #<?= htmlspecialchars($updated, ENT_QUOTES, 'UTF-8') ?> foram guardadas.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($deleted): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <div class="me-3">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Exame excluído com sucesso!</h4>
                <p class="mb-0">O exame #<?= htmlspecialchars($deleted, ENT_QUOTES, 'UTF-8') ?> foi removido do sistema.</p>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Seção de Estatísticas -->
    <div class="container-fluid mb-5 px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
                <h5 class="mb-0 font-weight-bold text-danger">
                    <i class="fas fa-chart-line me-2"></i>Estatísticas de Exames
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
                        <a class="nav-link px-3 py-2 border-0 fw-bold <?= (isset($_GET['tab']) && $_GET['tab']) === 'resultados' ? 'active text-danger' : 'text-body-secondary' ?> link-danger link-opacity-75-hover" 
                        id="resultados-tab" 
                        data-bs-toggle="tab" 
                        href="#resultados" 
                        role="tab">
                            <i class="fas fa-vial me-1"></i> Resultados
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
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-flask text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">TOTAL</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($totalExames) ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">Exames</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Aprovados -->
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">APROVADOS</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($aprovados) ?></h3>
                                            <span class="badge bg-success bg-opacity-10 text-success small">
                                                <?= $totalExames > 0 ? round(($aprovados / $totalExames) * 100, 1) : 0 ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reprovados -->
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">REPROVADOS</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($reprovados) ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                <?= $totalExames > 0 ? round(($reprovados / $totalExames) * 100, 1) : 0 ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Em Análise -->
                            <div class="col-lg-3 col-md-6">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-hourglass-half text-warning fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">EM ANÁLISE</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($emAnalise) ?></h3>
                                            <span class="badge bg-warning bg-opacity-10 text-warning small">
                                                <?= $totalExames > 0 ? round(($emAnalise / $totalExames) * 100, 1) : 0 ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab 2: Resultados -->
                    <div class="tab-pane fade" id="resultados" role="tabpanel">
                        <div class="p-4">
                            <h6 class="font-weight-bold text-muted mb-3">Distribuição por Resultado</h6>
                            <div class="row">
                                <?php foreach ($resultadosStats as $stat): 
                                    $percentagem = $totalExames > 0 ? ($stat['total'] / $totalExames) * 100 : 0;
                                    $percentagemFormatada = number_format($percentagem, 1);
                                    
                                    // Definir classes com base no resultado
                                    $bgBarClass = '';
                                    $textClass = '';
                                    $bgClass = '';
                                    
                                    if ($stat['resultado'] === 'aprovado') {
                                        $bgBarClass = 'bg-success';
                                        $bgClass = 'bg-success bg-opacity-10';
                                        $textClass = 'text-success';
                                    } elseif ($stat['resultado'] === 'reprovado') {
                                        $bgBarClass = 'bg-danger';
                                        $bgClass = 'bg-danger bg-opacity-10';
                                        $textClass = 'text-danger';
                                    } else {
                                        $bgBarClass = 'bg-warning';
                                        $bgClass = 'bg-warning bg-opacity-10';
                                        $textClass = 'text-warning';
                                    }
                                ?>
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge <?= $bgClass ?> <?= $textClass ?> px-3 py-1">
                                                    <?= htmlspecialchars($stat['resultado'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span class="font-weight-bold h5 mb-0"><?= $stat['total'] ?></span>
                                            </div>
                                            <div class="progress bg-light" style="height: 8px;">
                                                <div class="progress-bar <?= $bgBarClass ?>" role="progressbar" 
                                                    style="width: <?= $percentagem ?>%" 
                                                    aria-valuenow="<?= $percentagem ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <small class="text-muted">Percentagem</small>
                                                <small class="font-weight-bold <?= $textClass ?>">
                                                    <?= $percentagemFormatada ?>%
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-end small text-muted mt-2">
                                * Baseado em <?= $totalExames ?> exames registados
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
                        <label for="resultado" class="form-label">Resultado</label>
                        <select class="form-control" id="resultado" name="resultado">
                            <option value="">Todos</option>
                            <option value="Aprovado" <?= $resultado === 'Aprovado' ? 'selected' : '' ?>>Aprovado</option>
                            <option value="Reprovado" <?= $resultado === 'Reprovado' ? 'selected' : '' ?>>Reprovado</option>
                            <option value="Em Análise" <?= $resultado === 'Em Análise' ? 'selected' : '' ?>>Em Análise</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="id_bolsa" class="form-label">ID Bolsa</label>
                        <select class="form-control" id="id_bolsa" name="id_bolsa">
                            <option value="">Todas</option>
                            <?php foreach ($bolsas as $bolsa): ?>
                                <option value="<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $id_bolsa == $bolsa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar 
                        </button>
                        <a href="exames.php" class="btn btn-danger">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Limpar Filtros 
                        </a>
                    </div>
                </div>
                <input type="hidden" name="pagina" value="1">
            </form>

            <div class="table-responsive py-2 px-3">
                <table class="table text-nowrap table-hover">
                    <caption>Total de Exames: <?= $totalRegistros ?> | Página <?= $paginaAtual ?> de <?= $totalPaginas ?></caption>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">ID Bolsa</th>
                            <th class="text-center">Data</th>
                            <th class="text-center">Hemoglobina</th>
                            <th>Hepatite</th>
                            <th>HIV</th>
                            <th>Chagas</th>
                            <th>Sífilis</th>
                            <th>Resultado</th>
                            <th scope="col">
                                <button type="button" class="btn btn-sm text-white" style="background-color: #202d3b;" data-bs-toggle="modal" data-bs-target="#modalExame">
                                    <i class="fa-solid fa-plus"></i> Novo
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (empty($exames)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-database me-2"></i>Nenhum registo encontrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($exames as $exame): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($exame['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($exame['id_bolsa'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= date("d/m/Y", strtotime($exame['data'])) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($exame['hemoglobina']) ?> g/dL</td>
                                    <td><?= $exame['hepatite'] ? '<span class="badge bg-danger bg-opacity-10 text-danger">Positivo</span>' : '<span class="badge bg-success bg-opacity-10 text-success">Negativo</span>' ?></td>
                                    <td><?= $exame['hiv'] ? '<span class="badge bg-danger bg-opacity-10 text-danger">Positivo</span>' : '<span class="badge bg-success bg-opacity-10 text-success">Negativo</span>' ?></td>
                                    <td><?= $exame['chagas'] ? '<span class="badge bg-danger bg-opacity-10 text-danger">Positivo</span>' : '<span class="badge bg-success bg-opacity-10 text-success">Negativo</span>' ?></td>
                                    <td><?= $exame['sifilis'] ? '<span class="badge bg-danger bg-opacity-10 text-danger">Positivo</span>' : '<span class="badge bg-success bg-opacity-10 text-success">Negativo</span>' ?></td>
                                    <td>
                                        <?php
                                        $resultadoClass = '';
                                        if ($exame['resultado'] === 'aprovado') {
                                            $resultadoClass = 'bg-success text-white';
                                        } elseif ($exame['resultado'] === 'reprovado') {
                                            $resultadoClass = 'bg-danger text-white';
                                        } else {
                                            $resultadoClass = 'bg-warning text-white';
                                        }
                                        ?>
                                        <span class="badge <?= $resultadoClass ?>"><?= htmlspecialchars($exame['resultado']) ?></span>
                                    </td>
                                    <td>
                                        <a href="exame-editar.php?id=<?= $exame['id'] ?>" class="text-decoration-none me-2" title="Editar">
                                            <i class="fa-solid fa-file-pen text-dark"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none text-danger" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteModalExame" 
                                           data-exame-id="<?= $exame['id'] ?>"
                                           title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
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

<!-- Modal para adicionar exame -->
<div class="modal fade" id="modalExame" tabindex="-1" aria-labelledby="modalExameLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExameLabel">Adicionar Novo Exame</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="includes/store-exame.php">
                    <div class="mb-3">
                        <label>ID Bolsa</label>
                        <input type="number" name="id_bolsa" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Data</label>
                        <input type="date" name="data" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Hemoglobina (g/dL)</label>
                        <input type="text" name="hemoglobina" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Hepatite</label>
                        <select name="hepatite" class="form-control">
                            <option value="0">Negativo</option>
                            <option value="1">Positivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>HIV</label>
                        <select name="hiv" class="form-control">
                            <option value="0">Negativo</option>
                            <option value="1">Positivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Chagas</label>
                        <select name="chagas" class="form-control">
                            <option value="0">Negativo</option>
                            <option value="1">Positivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Sífilis</label>
                        <select name="sifilis" class="form-control">
                            <option value="0">Negativo</option>
                            <option value="1">Positivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Resultado</label>
                        <select name="resultado" class="form-control">
                            <option value="Aprovado">Aprovado</option>
                            <option value="Reprovado">Reprovado</option>
                            <option value="Em Análise">Em Análise</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Salvar Exame</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div class="modal fade" id="deleteModalExame" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem a certeza que deseja eliminar este exame? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonExame" href="#" class="btn btn-danger">Eliminar</a>
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

// Configura o modal de exclusão
document.addEventListener("DOMContentLoaded", function() {
    var deleteModal = document.getElementById("deleteModalExame");
    deleteModal.addEventListener("show.bs.modal", function(event) {
        var button = event.relatedTarget;
        var exameId = button.getAttribute("data-exame-id");
        var confirmButton = document.getElementById("deleteConfirmButtonExame");
        confirmButton.href = "exame-excluir.php?id=" + exameId;
    });
});
</script>

<?php include 'partials/footer.php'; ?>