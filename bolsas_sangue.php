<?php
ob_start();
include 'partials/header.php';

$pageTitle = "Bolsas de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Inventário', 'url' => '#', 'active' => true]
];

// Conexão PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=hemovida", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Processar remoção se houver POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_bolsa'])) {
    // Limpa qualquer saída que possa ter sido gerada antes
    ob_clean();
    
    $id_bolsa = intval($_POST['id_bolsa']);
    $response = ['success' => false, 'message' => 'Erro desconhecido'];
    
    if ($id_bolsa > 0) {
        try {
            // Verificar se a bolsa existe antes de excluir
            $stmt = $pdo->prepare("SELECT id FROM bolsas_sangue WHERE id = ?");
            $stmt->execute([$id_bolsa]);
            $bolsa = $stmt->fetch();
            
            if (!$bolsa) {
                throw new Exception("Bolsa não encontrada");
            }
            
            // Iniciar transação para garantir integridade
            $pdo->beginTransaction();
            
            try {
                // Primeiro, excluir os exames relacionados
                $stmt = $pdo->prepare("DELETE FROM exames WHERE id_bolsa = ?");
                $stmt->execute([$id_bolsa]);
                
                // Depois, excluir a bolsa
                $stmt = $pdo->prepare("DELETE FROM bolsas_sangue WHERE id = ?");
                $stmt->execute([$id_bolsa]);
                
                // Confirmar transação
                $pdo->commit();
                
                // Verificar se alguma linha foi afetada
                if ($stmt->rowCount() > 0) {
                    $response = ['success' => true, 'message' => 'Bolsa e exames relacionados removidos com sucesso!'];
                } else {
                    $response = ['success' => false, 'message' => 'Nenhuma bolsa foi removida'];
                }
                
            } catch (PDOException $e) {
                // Reverter transação em caso de erro
                $pdo->rollBack();
                throw $e;
            }
            
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Erro ao remover a bolsa: ' . $e->getMessage()];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }
    } else {
        $response = ['success' => false, 'message' => 'ID de bolsa inválido'];
    }
    
    // Se for uma requisição AJAX, retorne JSON e encerre
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Se não for AJAX, defina mensagem normal
    if (isset($response['success'])) {
        if ($response['success']) {
            $msg = $response['message'];
        } else {
            $error = $response['message'];
        }
    }
}

// Get statistics data
$queryStats = "SELECT 
    COUNT(*) as total_bolsas,
    SUM(CASE WHEN estado = 'Disponível' THEN 1 ELSE 0 END) as disponiveis,
    SUM(CASE WHEN estado = 'Utilizada' THEN 1 ELSE 0 END) as utilizadas,
    SUM(CASE WHEN estado = 'Vencida' THEN 1 ELSE 0 END) as vencidas,
    SUM(CASE WHEN estado = 'Reservada' THEN 1 ELSE 0 END) as reservadas
    FROM bolsas_sangue";

$stmt = $pdo->query($queryStats);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get blood type distribution
$queryTypes = "SELECT 
    d.tipo_sanguineo, 
    COUNT(*) as total,
    SUM(CASE WHEN b.estado = 'Disponível' THEN 1 ELSE 0 END) as disponiveis
    FROM bolsas_sangue b
    JOIN dadores d ON b.id_dador = d.id
    GROUP BY d.tipo_sanguineo
    ORDER BY total DESC";

$stmt = $pdo->query($queryTypes);
$bloodTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configuração da paginação
$registrosPorPagina = 30;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaAtual < 1) {
    $paginaAtual = 1;
}
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Obter os valores dos filtros
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$tipoSanguineo = $_GET['tipo_sanguineo'] ?? '';
$estado = $_GET['estado'] ?? '';

// Construir a consulta SQL base para listagem
$queryBase = "SELECT bolsas_sangue.id, bolsas_sangue.data_coleta, bolsas_sangue.volume_ml, bolsas_sangue.estado, dadores.tipo_sanguineo
            FROM bolsas_sangue
            JOIN dadores ON bolsas_sangue.id_dador = dadores.id
            WHERE 1=1";

$params = [];
$whereConditions = [];

// Filtro: Data Início
if (!empty($dataInicio)) {
    $whereConditions[] = "bolsas_sangue.data_coleta >= ?";
    $params[] = $dataInicio;
}

// Filtro: Data Fim
if (!empty($dataFim)) {
    $whereConditions[] = "bolsas_sangue.data_coleta <= ?";
    $params[] = $dataFim;
}

// Filtro: Tipo Sanguíneo
if (!empty($tipoSanguineo)) {
    $whereConditions[] = "dadores.tipo_sanguineo = ?";
    $params[] = $tipoSanguineo;
}

// Filtro: Estado
if (!empty($estado)) {
    $whereConditions[] = "bolsas_sangue.estado = ?";
    $params[] = $estado;
}

if (!empty($whereConditions)) {
    $queryBase .= " AND " . implode(" AND ", $whereConditions);
}

// Consulta para contar o total de registros
$queryCount = "SELECT COUNT(*) as total FROM (" . $queryBase . ") AS total_query";
$stmt = $pdo->prepare($queryCount);
$stmt->execute($params);
$rowCount = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRegistros = $rowCount['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta final com LIMIT para paginação
$query = $queryBase . " ORDER BY bolsas_sangue.data_coleta DESC LIMIT " . (int)$registrosPorPagina . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params); // Execute only with the WHERE clause parameters
$resultado = $stmt;
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?> 
    
    <!-- Mensagens de feedback -->
    <?php if (!empty($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-end mb-4">
        <a href="bolsas_sangue-criar.php" class="btn text-white" style="background-color: #202d3b;">
            <i class="fa-solid fa-plus"></i> Adicionar Bolsa
        </a>
    </div>

    <!-- Seção de Estatísticas -->
    <div class="container-fluid mb-4 px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
                <h5 class="mb-0 font-weight-bold text-danger">
                    <i class="fas fa-chart-pie me-2"></i>Estatísticas de Bolsas de Sangue
                </h5>
                <ul class="nav nav-tabs border-0 mt-3" id="statsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active px-3 py-2 border-0 font-weight-bold text-danger" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">
                            <i class="fas fa-eye me-1"></i> Visão Geral
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 border-0 font-weight-bold text-muted" id="bloodtypes-tab" data-bs-toggle="tab" href="#bloodtypes" role="tab">
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
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-tint text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">TOTAL</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($stats['total_bolsas']) ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">Bolsas</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Disponíveis -->
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">DISPONÍVEIS</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($stats['disponiveis']) ?></h3>
                                            <span class="badge bg-success bg-opacity-10 text-success small"><?= round(($stats['disponiveis']/$stats['total_bolsas'])*100, 1) ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Utilizadas -->
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-check-double text-secondary fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">UTILIZADAS</p>
                                            <h3 class="mb-0 fs-4 font-weight-bold mx-2"><?= number_format($stats['utilizadas']) ?></h3>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary small"><?= round(($stats['utilizadas']/$stats['total_bolsas'])*100, 1) ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vencidas/Reservadas -->
                            <div class="col-lg-3 col-md-6">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-exclamation-triangle text-warning fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">VENCIDAS/RESERVADAS</p>
                                            <h3 class="mb-0 fs-4 mx-2 font-weight-bold">
                                                <?= number_format($stats['vencidas'] + $stats['reservadas']) ?>
                                            </h3>
                                            <span class="badge bg-warning bg-opacity-10 text-warning small">
                                                <?= round((($stats['vencidas'] + $stats['reservadas'])/$stats['total_bolsas'])*100, 1) ?>%
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
                                $allBloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                $bloodTypesMap = [];
                                foreach ($bloodTypes as $type) {
                                    $bloodTypesMap[$type['tipo_sanguineo']] = $type;
                                }
                                
                                foreach ($allBloodTypes as $bloodType): 
                                    $typeData = $bloodTypesMap[$bloodType] ?? [
                                        'tipo_sanguineo' => $bloodType,
                                        'total' => 0,
                                        'disponiveis' => 0
                                    ];
                                    
                                    $percentage = $stats['total_bolsas'] > 0 ? ($typeData['total'] / $stats['total_bolsas']) * 100 : 0;
                                    $availablePercentage = $typeData['total'] > 0 ? ($typeData['disponiveis'] / $typeData['total']) * 100 : 0;
                                ?>
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1">
                                                    <?= htmlspecialchars($typeData['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span class="font-weight-bold h5 mb-0"><?= $typeData['total'] ?></span>
                                            </div>
                                            <div class="progress bg-light mb-2" style="height: 8px;">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                     style="width: <?= $percentage ?>%" 
                                                     aria-valuenow="<?= $percentage ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Percentagem</small>
                                                <small class="font-weight-bold text-danger">
                                                    <?= round($percentage, 1) ?>%
                                                </small>
                                            </div>
                                            <div class="progress bg-light" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?= $availablePercentage ?>%" 
                                                     aria-valuenow="<?= $availablePercentage ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">Disponíveis</small>
                                                <small class="font-weight-bold text-success">
                                                    <?= $typeData['disponiveis'] ?> (<?= $typeData['total'] > 0 ? round($availablePercentage, 1) : 0 ?>%)
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-end small text-muted mt-2">
                                * Baseado em <?= number_format($stats['total_bolsas']) ?> bolsas registadas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filtro de Bolsas de Sangue -->
    <div class="card border-0 p-0 mb-4">
        <div class="card-body p-0">
            <form method="GET" class="mb-4 p-3">
                <div class="row g-3">
                    <!-- Data de Coleta - Início -->
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Coleta (Início)</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                            value="<?= htmlspecialchars($_GET['data_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    
                    <!-- Data de Coleta - Fim -->
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Coleta (Fim)</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" 
                            value="<?= htmlspecialchars($_GET['data_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    
                    <!-- Tipo Sanguíneo -->
                    <div class="col-md-3">
                        <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                        <select class="form-select" id="tipo_sanguineo" name="tipo_sanguineo">
                            <option value="">Todos</option>
                            <?php 
                            $tiposSanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                            foreach ($tiposSanguineos as $tipo): 
                            ?>
                                <option value="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= (isset($_GET['tipo_sanguineo'])) && $_GET['tipo_sanguineo'] == $tipo ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Estado -->
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos</option>
                            <?php 
                            $estadosOptions = ['Disponível', 'Utilizada', 'Vencida', 'Reservada'];
                            foreach ($estadosOptions as $estado): 
                            ?>
                                <option value="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= (isset($_GET['estado'])) && $_GET['estado'] == $estado ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="col-md-12">
                        <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                            <i class="fa-solid fa-filter"></i> Filtrar 
                        </button>
                        <a href="bolsas_sangue.php" class="btn btn-danger">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Limpar Filtros 
                        </a>
                    </div>
                </div>
                <input type="hidden" name="pagina" value="1">
            </form>
        </div>
    </div>

    <!-- Listagem das Bolsas de Sangue com paginação -->
    <div class="table-responsive py-2">
        <table class="table text-nowrap table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tipo Sanguíneo</th>
                    <th scope="col">Data de Coleta</th>
                    <th scope="col">Volume (ml)</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php
                if ($resultado->rowCount() > 0):
                    while ($bolsa = $resultado->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr id="bolsa-<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>">
                    <th scope="row"><?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?></th>
                    <td><?= htmlspecialchars($bolsa['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($bolsa['data_coleta'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($bolsa['volume_ml'], ENT_QUOTES, 'UTF-8') ?> ml</td>
                    <td>
                        <?php
                            $estadoBanco = strtolower(trim($bolsa['estado']));
                            $classesBadge = [
                                'disponível' => 'bg-success text-white',
                                'disponivel' => 'bg-success text-white',
                                'utilizada' => 'bg-secondary text-white',
                                'vencida' => 'bg-danger text-white',
                                'reservada' => 'bg-warning text-dark',
                            ];
                            $classe = $classesBadge[$estadoBanco] ?? 'bg-light text-dark';
                            $estadoLabel = ucfirst($estadoBanco);
                        ?>
                        <span class="badge <?= $classe ?>">
                            <?= htmlspecialchars($estadoLabel, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="bolsas_sangue-editar.php?id=<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none" style="color: #202d3b;">
                                <i class="fa-solid fa-file-pen"></i>
                            </a>
                            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#deleteModalBolsa" data-bolsa-id="<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fa-solid fa-trash-can text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                    echo '<tr><td colspan="6" class="text-center">Não há bolsas de sangue disponíveis.</td></tr>';
                endif;
                ?>
            </tbody>
        </table>
        
        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center">
                <!-- Link para a página anterior -->
                <li class="page-item <?= $paginaAtual == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual - 1] )) ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php
                $inicio = max(1, $paginaAtual - 2);
                $fim = min($totalPaginas, $paginaAtual + 2);
                
                if ($inicio > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => 1] )) . '">1</a></li>';
                    if ($inicio > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                
                for ($i = $inicio; $i <= $fim; $i++):
                    $active = $i == $paginaAtual ? 'active' : '';
                ?>
                    <li class="page-item <?= $active ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i] )) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php
                if ($fim < $totalPaginas) {
                    if ($fim < $totalPaginas - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => $totalPaginas] )) . '">' . $totalPaginas . '</a></li>';
                }
                ?>
                
                <!-- Link para a próxima página -->
                <li class="page-item <?= $paginaAtual == $totalPaginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual + 1] )) ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="text-center text-muted small mt-2">
            Página <?= $paginaAtual ?> de <?= $totalPaginas ?> | 
            Total de registros: <?= $totalRegistros ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Remover Bolsa -->
<div class="modal fade" id="deleteModalBolsa" tabindex="-1" aria-labelledby="deleteModalLabelBolsa" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabelBolsa">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem a certeza de que deseja remover esta bolsa de sangue?</p>
                <p class="text-danger"><strong>Esta ação não pode ser desfeita!</strong></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <input type="hidden" name="id_bolsa" id="id_bolsa_input" value="">
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can me-1"></i> Remover
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast para mensagens -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header text-white">
            <strong class="me-auto toast-title">Sucesso</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const deleteModal = document.getElementById("deleteModalBolsa");
    const idBolsaInput = document.getElementById("id_bolsa_input");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const liveToast = new bootstrap.Toast(document.getElementById('liveToast'));
    let modalInstance = null;

    // Inicializa a instância do modal
    if (deleteModal) {
        modalInstance = new bootstrap.Modal(deleteModal);
    }

    deleteModal.addEventListener("show.bs.modal", function(event) {
        const button = event.relatedTarget;
        const bolsaId = button.getAttribute("data-bolsa-id");
        idBolsaInput.value = bolsaId;
    });

    confirmDeleteBtn.addEventListener("click", async function() {
        const bolsaId = idBolsaInput.value;
        
        if (!bolsaId) {
            showToast('Erro', 'ID da bolsa não encontrado', 'danger');
            return;
        }

        // Desabilita o botão para evitar múltiplos cliques
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...';

        try {
            const response = await fetch('bolsas_sangue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `remover_bolsa=1&id_bolsa=${bolsaId}`
            });

            const data = await response.json();
            
            if (data.success) {
                showToast('Sucesso', data.message, 'success');
                // Remove a linha da tabela
                const row = document.getElementById(`bolsa-${bolsaId}`);
                if (row) row.remove();
                
                // Recarrega a página após 1 segundo para atualizar estatísticas
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('Erro', data.message, 'danger');
            }
        } catch (error) {
            console.error('Erro:', error);
            showToast('Erro', 'Falha ao comunicar com o servidor', 'danger');
        } finally {
            // Reabilita o botão
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.innerHTML = '<i class="fa-solid fa-trash-can me-1"></i> Remover';
            
            // Fecha o modal de forma segura
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Limpa qualquer backdrop residual do modal
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    });

    function showToast(title, message, type) {
        // Tratamento especial para erros de constraint
        if (message.includes('foreign key constraint fails')) {
            message = 'Esta bolsa possui exames relacionados e não pode ser excluída diretamente.';
        }

        const toastHeader = document.querySelector('#liveToast .toast-header');
        const toastTitle = document.querySelector('#liveToast .toast-title');
        const toastBody = document.getElementById('toastMessage');
        
        // Atualiza classes e conteúdo
        toastHeader.className = `toast-header text-white bg-${type}`;
        toastTitle.textContent = title;
        toastBody.textContent = message;
        
        // Configura o toast para ficar visível por mais tempo
        const toastElement = document.getElementById('liveToast');
        const toast = bootstrap.Toast.getOrCreateInstance(toastElement, {
            delay: 5000, // 5 segundos
            autohide: true,
            animation: true
        });
        
        // Mostra o toast
        toast.show();
    }
});
</script>

<?php 
ob_end_flush();
include 'partials/footer.php'; 
?>