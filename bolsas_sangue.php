<?php
include 'partials/header.php';

$pageTitle = "Bolsas de Sangue";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Inventário', 'url' => '#', 'active' => true]
];

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "hemovida";
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

// Processar remoção se houver POST (mantido para compatibilidade e processamento AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_bolsa'])) {
    $id_bolsa = intval($_POST['id_bolsa']);
    
    if ($id_bolsa > 0) {
        $query = "DELETE FROM bolsas_sangue WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_bolsa);
            if (mysqli_stmt_execute($stmt)) {
                // Se a requisição for AJAX, você pode retornar um JSON de sucesso
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
                    echo json_encode(['success' => true, 'message' => 'Bolsa removida com sucesso!']);
                    exit; // Importante para parar a execução e não renderizar o HTML completo
                }
                $msg = "Bolsa removida com sucesso!";
            } else {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
                    echo json_encode(['success' => false, 'message' => 'Erro ao remover a bolsa: ' . mysqli_error($conexao)]);
                    exit;
                }
                $error = "Erro ao remover a bolsa: " . mysqli_error($conexao);
            }
            mysqli_stmt_close($stmt);
        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . mysqli_error($conexao)]);
                exit;
            }
            $error = "Erro ao preparar a consulta: " . mysqli_error($conexao);
        }
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            echo json_encode(['success' => false, 'message' => 'ID de bolsa inválido']);
            exit;
        }
        $error = "ID de bolsa inválido";
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

$resultStats = mysqli_query($conexao, $queryStats);
$stats = mysqli_fetch_assoc($resultStats);

// Get blood type distribution
$queryTypes = "SELECT 
    d.tipo_sanguineo, 
    COUNT(*) as total,
    SUM(CASE WHEN b.estado = 'Disponível' THEN 1 ELSE 0 END) as disponiveis
    FROM bolsas_sangue b
    JOIN dadores d ON b.id_dador = d.id
    GROUP BY d.tipo_sanguineo
    ORDER BY total DESC";

$resultTypes = mysqli_query($conexao, $queryTypes);
$bloodTypes = [];
while ($row = mysqli_fetch_assoc($resultTypes)) {
    $bloodTypes[] = $row;
}

// Configuração da paginação
$registrosPorPagina = 30;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaAtual < 1) {
    $paginaAtual = 1;
}
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Obter os valores dos filtros
$tipos_sanguineos = $_GET['tipo_sanguineo'] ?? [];
$estados = $_GET['estado'] ?? [];

// Construir a consulta SQL base para listagem
$queryBase = "SELECT bolsas_sangue.id, bolsas_sangue.data_coleta, bolsas_sangue.volume_ml, bolsas_sangue.estado, dadores.tipo_sanguineo
              FROM bolsas_sangue
              JOIN dadores ON bolsas_sangue.id_dador = dadores.id
              WHERE 1=1";

// Filtro: Tipo Sanguíneo
if (!empty($tipos_sanguineos)) {
    $tipos_sanguineos = array_map(function($valor) use ($conexao) {
        return mysqli_real_escape_string($conexao, $valor);
    }, $tipos_sanguineos);
    $queryBase .= " AND dadores.tipo_sanguineo IN ('" . implode("','", $tipos_sanguineos) . "')";
}

// Filtro: Estado
if (!empty($estados)) {
    $estados = array_map(function($valor) use ($conexao) {
        return mysqli_real_escape_string($conexao, $valor);
    }, $estados);
    $queryBase .= " AND bolsas_sangue.estado IN ('" . implode("','", $estados) . "')";
}

// Consulta para contar o total de registros
$queryCount = "SELECT COUNT(*) as total FROM (" . $queryBase . ") AS total_query";
$resultCount = mysqli_query($conexao, $queryCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalRegistros = $rowCount['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta final com LIMIT para paginação
$query = $queryBase . " LIMIT $registrosPorPagina OFFSET $offset";
$resultado = mysqli_query($conexao, $query);

?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?> 
    
    <!-- Mensagens de feedback (podem ser exibidas via JS após AJAX) -->
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
                                <?php foreach ($bloodTypes as $type): 
                                    $percentage = ($type['total'] / $stats['total_bolsas']) * 100;
                                    $availablePercentage = ($type['disponiveis'] / $type['total']) * 100;
                                ?>
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1">
                                                    <?= htmlspecialchars($type['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span class="font-weight-bold h5 mb-0"><?= $type['total'] ?></span>
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
                                                    <?= $type['disponiveis'] ?> (<?= round($availablePercentage, 1) ?>%)
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
    
    <!-- Filtro de Bolsas de Sangue - SEMPRE INICIA FECHADO -->
    <div class="accordion py-2" id="accordionFiltro">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button text-dark collapsed" 
                        style="background-color: #f1f1f1;" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapseFiltro" 
                        aria-expanded="false" 
                        aria-controls="collapseFiltro">
                    Aplicar Filtros
                </button>
            </h2>
            <div id="collapseFiltro" class="accordion-collapse collapse" 
                 data-bs-parent="#accordionFiltro">
                <div class="accordion-body">
                    <form method="GET" action="">
                        <div class="card border-0 p-0 m-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <!-- Filtro: Tipo Sanguíneo -->
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Tipo Sanguíneo</p>
                                        <div class="py-3 pb-0">
                                            <?php
                                            $tipos_sanguineos_options = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                            foreach ($tipos_sanguineos_options as $tipo) {
                                                $id = strtolower(str_replace(['+', '-'], ['positivo', 'negativo'], $tipo));
                                                echo '
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="' . $tipo . '" name="tipo_sanguineo[]" id="' . $id . '" ' . (in_array($tipo, $tipos_sanguineos) ? 'checked' : '') . '>
                                                    <label class="form-check-label" for="' . $id . '">
                                                        ' . $tipo . '
                                                    </label>
                                                </div>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- Filtro: Estado da Bolsa -->
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Estado</p>
                                        <div class="py-3 pb-0">
                                            <?php
                                            $estados_options = ['Disponível', 'Utilizada', 'Vencida', 'Reservada'];
                                            foreach ($estados_options as $estado) {
                                                $id = strtolower($estado);
                                                echo '
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="' . $estado . '" name="estado[]" id="' . $id . '" ' . (in_array($estado, $estados) ? 'checked' : '') . '>
                                                    <label class="form-check-label" for="' . $id . '">
                                                        ' . $estado . '
                                                    </label>
                                                </div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Botões de Ação -->
                            <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-end mt-3 gap-2">
                                <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                                    <i class="fa-solid fa-filter"></i> Filtrar 
                                </button>
                                <a href="bolsas_sangue.php" class="btn btn-danger">
                                    <i class="fa-solid fa-filter-circle-xmark"></i> Remover Filtros 
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                if (mysqli_num_rows($resultado) > 0):
                    while ($bolsa = mysqli_fetch_assoc($resultado)):
                ?>
                <tr id="bolsa-<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>"> <!-- Adicionado ID para facilitar remoção via JS -->
                    <th scope="row"><?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?></th>
                    <td><?= htmlspecialchars($bolsa['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($bolsa['data_coleta'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($bolsa['volume_ml'], ENT_QUOTES, 'UTF-8') ?> ml</td>
                    <td>
                        <?php
                            // Normalizar estado (tudo minúsculo, sem acento)
                            $estadoBanco = strtolower(trim($bolsa['estado']));

                            // Mapeamento de estado → cor da badge
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
                // Mostrar até 5 páginas ao redor da atual
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
                <!-- O input hidden ainda é útil para armazenar o ID da bolsa -->
                <input type="hidden" name="id_bolsa" id="id_bolsa_input" value="">
                <!-- Botão de remover agora tem um ID para ser manipulado pelo JS -->
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can me-1"></i> Remover
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Controle para modal de remoção
    const deleteModal = document.getElementById("deleteModalBolsa");
    const idBolsaInput = document.getElementById("id_bolsa_input");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn"); // Seleciona o botão de confirmação

    // Quando o modal é aberto, preenche o input hidden com o ID da bolsa
    deleteModal.addEventListener("show.bs.modal", function(event) {
        const button = event.relatedTarget; // Botão que acionou o modal
        const bolsaId = button.getAttribute("data-bolsa-id"); // Pega o ID do atributo data-bolsa-id
        idBolsaInput.value = bolsaId; // Define o valor do input hidden
    });

    // Adiciona um listener de clique ao botão de confirmação dentro do modal
    confirmDeleteBtn.addEventListener("click", function() {
        const bolsaId = idBolsaInput.value; // Pega o ID da bolsa do input escondido

        // Cria um objeto FormData para enviar os dados como se fosse um formulário POST
        const formData = new FormData();
        formData.append('remover_bolsa', '1'); // Indica que é uma requisição de remoção
        formData.append('id_bolsa', bolsaId); // O ID da bolsa a ser removida

        // Envia a requisição AJAX para a mesma página (bolsas_sangue.php)
        fetch('bolsas_sangue.php', {
            method: 'POST',
            body: formData,
            headers: {
                // Adiciona um cabeçalho para que o PHP possa identificar que é uma requisição AJAX
                'X-Requested-With': 'XMLHttpRequest' 
            }
        })
        .then(response => response.json()) // Espera uma resposta JSON do servidor
        .then(data => {
            if (data.success) {
                // Se a remoção foi bem-sucedida, exibe uma mensagem de sucesso
                alert(data.message); 
                // Opcional: Remover a linha da tabela sem recarregar a página
                const rowToRemove = document.getElementById(`bolsa-${bolsaId}`);
                if (rowToRemove) {
                    rowToRemove.remove();
                }
                // Ou simplesmente recarrega a página para atualizar a lista
                window.location.reload(); 
            } else {
                // Se houve um erro, exibe a mensagem de erro
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erro ao remover a bolsa:', error);
            alert('Ocorreu um erro inesperado ao tentar remover a bolsa.');
        })
        .finally(() => {
            // Fecha o modal, independentemente do sucesso ou falha
            const modal = bootstrap.Modal.getInstance(deleteModal);
            modal.hide();
        });
    });
});
</script>

<?php include 'partials/footer.php'; ?>
