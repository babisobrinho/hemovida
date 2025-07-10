<?php
include 'partials/header.php';
include 'includes/filtrar_dadores.php';

$pageTitle = "Lista de Dadores";
$breadcrumbItems = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
    ['title' => 'Dadores', 'url' => '#', 'active' => true]
];

// Estatísticas de dadores
require_once 'includes/db_connection.php';

// Configuração de paginação
$registrosPorPagina = 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Total de dadores
$totalDadoresQuery = "SELECT COUNT(*) as total FROM dadores";
$totalDadoresStmt = $pdo->query($totalDadoresQuery);
$totalDadores = $totalDadoresStmt->fetchColumn();
$totalPaginas = ceil($totalDadores / $registrosPorPagina);

// Idade média dos dadores
$idadeMediaQuery = "SELECT AVG(YEAR(CURRENT_DATE) - YEAR(data_nascimento)) as idade_media FROM dadores";
$idadeMediaStmt = $pdo->query($idadeMediaQuery);
$idadeMedia = $idadeMediaStmt->fetchColumn();
$idadeMediaFormatada = number_format($idadeMedia, 1);

// Dadores por tipo sanguíneo
$tiposSanguineosQuery = "SELECT tipo_sanguineo, COUNT(*) as total FROM dadores GROUP BY tipo_sanguineo ORDER BY total DESC";
$tiposSanguineosStmt = $pdo->query($tiposSanguineosQuery);
$tiposSanguineosStats = $tiposSanguineosStmt->fetchAll(PDO::FETCH_ASSOC);

// Tipo sanguíneo mais comum
$tipoMaisComum = !empty($tiposSanguineosStats) ? $tiposSanguineosStats[0]['tipo_sanguineo'] : 'N/A';
$totalTipoMaisComum = !empty($tiposSanguineosStats) ? $tiposSanguineosStats[0]['total'] : 0;

// Distribuição por sexo
$sexoStatsQuery = "SELECT sexo, COUNT(*) as total FROM dadores GROUP BY sexo";
$sexoStatsStmt = $pdo->query($sexoStatsQuery);
$sexoStats = $sexoStatsStmt->fetchAll(PDO::FETCH_ASSOC);

$totalMasculino = 0;
$totalFeminino = 0;

foreach ($sexoStats as $stat) {
    if ($stat['sexo'] == 'masculino') {
        $totalMasculino = $stat['total'];
    } else {
        $totalFeminino = $stat['total'];
    }
}

// Distribuição por estado
$estadoStatsQuery = "SELECT estado, COUNT(*) as total FROM dadores GROUP BY estado";
$estadoStatsStmt = $pdo->query($estadoStatsQuery);
$estadoStats = $estadoStatsStmt->fetchAll(PDO::FETCH_ASSOC);

$totalAtivos = 0;
$totalInativos = 0;

foreach ($estadoStats as $stat) {
    if ($stat['estado'] == 1) {
        $totalAtivos = $stat['total'];
    } else {
        $totalInativos = $stat['total'];
    }
}

// Todos os tipos sanguíneos possíveis
$allTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$stats = [];

foreach ($allTypes as $type) {
    $found = false;
    foreach ($tiposSanguineosStats as $dbStat) {
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

// Consulta principal com paginação
$sqlDadores = "SELECT * FROM dadores LIMIT ? OFFSET ?";
$stmtDadores = $pdo->prepare($sqlDadores);
$stmtDadores->execute([$registrosPorPagina, $offset]);
$dadores = $stmtDadores->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <!-- Seção de Estatísticas -->
    <div class="container-fluid mb-5 px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
                <h5 class="mb-0 font-weight-bold text-danger">
                    <i class="fas fa-chart-line me-2"></i>Estatísticas de Dadores
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
                                            <i class="fas fa-users text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">TOTAL DE DADORES</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($totalDadores) ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">Registados</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tipo Sanguíneo Mais Comum -->
                            <div class="col-lg-2 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-heartbeat text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">TIPO MAIS COMUM</p>
                                            <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= htmlspecialchars($tipoMaisComum, ENT_QUOTES, 'UTF-8') ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                <?= $totalTipoMaisComum ?> dadores
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Idade Média -->
                            <div class="col-lg-2 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-calendar-days text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">IDADE MÉDIA</p>
                                            <h3 class="mb-0 fs-4 font-weight-bold mx-2"><?= $idadeMediaFormatada ?></h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">anos</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Distribuição por Sexo -->
                            <div class="col-lg-3 col-md-6 border-right">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-venus-mars text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">DISTRIBUIÇÃO POR SEXO</p>
                                            <h3 class="mb-0 fs-4 font-weight-bold mx-2">
                                                <?= $totalMasculino ?>M / <?= $totalFeminino ?>F
                                            </h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                <?= round(($totalMasculino / $totalDadores) * 100) ?>% Masculino
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Distribuição por Estado -->
                            <div class="col-lg-3 col-md-6">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                            <i class="fas fa-user-check text-danger fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small text-muted">ESTADO DOS DADORES</p>
                                            <h3 class="mb-0 fs-4 mx-2 font-weight-bold">
                                                <?= $totalAtivos ?>A / <?= $totalInativos ?>I
                                            </h3>
                                            <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                <?= round(($totalAtivos / $totalDadores) * 100) ?>% Ativos
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
                                $totalDadores = $totalDadores > 0 ? $totalDadores : 1;
                                
                                foreach ($stats as $stat): 
                                    $totalTipo = $stat['total'];
                                    $percentagem = ($totalTipo / $totalDadores) * 100;
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
                                * Baseado em <?= $totalDadores ?> dadores registados
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
        <a href="dador-criar.php" class="btn text-white" style="background-color: #202d3b;">
            <i class="fa-solid fa-plus"></i> Novo
        </a>
        <a href="" data-bs-toggle="modal" data-bs-target="#filterModalDador" data-dador-id="<?php echo $dador['id']; ?>" class="btn text-white d-none d-md-block" style="background-color: #202d3b;">
            <i class="fa-solid fa-filter"></i> Filtrar
        </a>
        <a href="dadores.php" class="btn btn-danger">
            <i class="fa-solid fa-filter-circle-xmark"></i> Remover Filtros 
        </a>
    </div>
    <div class="accordion py-2 d-block d-md-none mb-2" id="accordionFiltro">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button text-dark collapsed" style="background-color: #f1f1f1;" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltro" aria-expanded="false" aria-controls="collapseFiltro">
                    Aplicar Filtros
                </button>
            </h2>
            <div id="collapseFiltro" class="accordion-collapse collapse" data-bs-parent="#accordionFiltro">
                <div class="accordion-body">
                    <form action="">
                        <div class="card border-0 p-0 m-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Tipo Sanguíneo</p>
                                        <div class="py-3 pb-0">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="a-positivo" id="a-positivo" <?php echo (isset($_GET['a-positivo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label form-checked-danger" for="a-positivo">
                                                    A +
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAPositivo']; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="a-negativo" id="a-negativo" <?php echo (isset($_GET['a-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="a-negativo">
                                                A -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countANegativo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="b-positivo" id="b-positivo" <?php echo (isset($_GET['b-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="b-positivo">
                                                B +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBPositivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="b-negativo" id="b-negativo" <?php echo (isset($_GET['b-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="b-negativo">
                                                B -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBNegativo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="ab-positivo" id="ab-positivo" <?php echo (isset($_GET['ab-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ab-positivo">
                                                AB +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABPositivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="ab-negativo" id="ab-negativo" <?php echo (isset($_GET['ab-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ab-negativo">
                                                AB -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABNegativo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="o-positivo" id="o-positivo" <?php echo (isset($_GET['o-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="o-positivo">
                                                O +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countOPositivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="o-negativo" id="o-negativo" <?php echo (isset($_GET['o-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="o-negativo">
                                                O -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countONegativo']; ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Sexo</p>
                                        <div class="py-3 pb-0">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="feminino" id="feminino" <?php echo (isset($_GET['feminino']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="feminino">
                                                    Feminino
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countFeminino']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="masculino" id="masculino" <?php echo (isset($_GET['masculino']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="masculino">
                                                    Masculino
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countMasculino']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Estado</p>
                                        <div class="py-3 pb-0">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="ativo" id="ativo" <?php echo (isset($_GET['ativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="ativo">
                                                    Ativo
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAtivo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="inativo" id="inativo" <?php echo (isset($_GET['inativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="inativo">
                                                    Inativo
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countInativo']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-end mt-3 gap-2">
                                <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                                    <i class="fa-solid fa-filter"></i> Filtrar 
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if($filtros['tipo_sanguineo'] || $filtros['sexo'] || $filtros['estado']): ?>
        <div class="d-flex align-items-center justify-content-start gap-1">
            <p class="m-0 p-0">Filtros:</p>
            <?php foreach($filtros['tipo_sanguineo'] as $tipo_sanguineo): ?>
                <div class="badge bg-light text-dark border">
                    <?php echo $tipo_sanguineo; ?>
                </div>
            <?php endforeach; ?>
            <?php foreach($filtros['sexo'] as $sexo): ?>
                <div class="badge bg-light text-dark border">
                    <?php echo $sexo; ?>
                </div>
            <?php endforeach; ?>
            <?php foreach($filtros['estado'] as $estado): ?>
                <div class="badge bg-light text-dark border">
                    <?php echo $estado == 1 ? 'Ativo' : 'Inativo'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="table-responsive py-2">
        <table class="table text-nowrap table-hover">
            <caption>Total de Dadores: <?php echo $totalDadores; ?> | Página <?= $paginaAtual ?> de <?= $totalPaginas ?></caption>
            <thead>
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Nº Utente</th>
                    <th scope="col">Data de Nascimento</th>
                    <th scope="col">Tipo Sanguíneo</th>
                    <th scope="col">Sexo</th>
                    <th scope="col">Peso</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Data da Inscrição</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php if (empty($dadores)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fa-solid fa-database me-2"></i>Nenhum registo encontrado
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($dadores as $dador): ?>
                        <tr>
                            <th scope="row"><?php echo $dador['nome']; ?></th>
                            <td><?php echo $dador['email']; ?></td>
                            <td><?php echo $dador['n_utente']; ?></td>
                            <td><?php echo $dador['data_nascimento']; ?></td>
                            <td><?php echo $dador['tipo_sanguineo']; ?></td>
                            <td><?php echo $dador['sexo']; ?></td>
                            <td><?php echo $dador['peso']; ?>kg</td>
                            <td>
                                <span class="badge <?php echo $dador['estado'] === 0 ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo $dador['estado'] === 0 ? 'Inativo' : 'Ativo'; ?>
                                </span>
                            </td>
                            <td><?php echo $dador['data_inscricao']; ?></td>
                            <td >
                                <div class="d-flex gap-2">
                                    <a href="dador-editar.php?table=dadores&id=<?php echo $dador['id']; ?>" class="text-decoration-none" style="color: #202d3b;">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </a>
                                    <a href="" data-bs-toggle="modal" data-bs-target="#deleteModalDador" data-dador-id="<?php echo $dador['id']; ?>" class="text-dark">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                    </a>
                                </div>
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

<!-- Modal: Filtrar Dador -->
<div class="modal fade" id="filterModalDador" tabindex="-1" aria-labelledby="filterModalLabelDador" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabelDador">Filtrar Dadores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="card border-0 p-0 m-0">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <p class="fw-semibold mb-0 text-muted">Tipo Sanguíneo</p>
                                    <div class="py-3 pb-0">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="a-positivo" id="a-positivo-modal" <?php echo (isset($_GET['a-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label form-checked-danger" for="a-positivo-modal">
                                                A +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAPositivo']; ?></span>
                                        </div>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="a-negativo" id="a-negativo-modal" <?php echo (isset($_GET['a-negativo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="a-negativo-modal">
                                            A -
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countANegativo']; ?></span>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="b-positivo" id="b-positivo-modal" <?php echo (isset($_GET['b-positivo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="b-positivo-modal">
                                            B +
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBPositivo']; ?></span>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="b-negativo" id="b-negativo-modal" <?php echo (isset($_GET['b-negativo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="b-negativo-modal">
                                            B -
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBNegativo']; ?></span>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="ab-positivo" id="ab-positivo-modal" <?php echo (isset($_GET['ab-positivo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="ab-positivo-modal">
                                            AB +
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABPositivo']; ?></span>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="ab-negativo" id="ab-negativo-modal" <?php echo (isset($_GET['ab-negativo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="ab-negativo-modal">
                                            AB -
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABNegativo']; ?></span>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="o-positivo" id="o-positivo-modal" <?php echo (isset($_GET['o-positivo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="o-positivo-modal">
                                            O +
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countOPositivo']; ?></span>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="" name="o-negativo" id="o-negativo-modal" <?php echo (isset($_GET['o-negativo']) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="o-negativo-modal">
                                            O -
                                        </label>
                                        <span class="badge bg-light text-muted float-end"><?php echo $contagens['countONegativo']; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <p class="fw-semibold mb-0 text-muted">Sexo</p>
                                    <div class="py-3 pb-0">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="feminino" id="feminino-modal" <?php echo (isset($_GET['feminino']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="feminino-modal">
                                                Feminino
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countFeminino']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="masculino" id="masculino-modal" <?php echo (isset($_GET['masculino']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="masculino-modal">
                                                Masculino
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countMasculino']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <p class="fw-semibold mb-0 text-muted">Estado</p>
                                    <div class="py-3 pb-0">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="ativo" id="ativo-modal" <?php echo (isset($_GET['ativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ativo-modal">
                                                Ativo
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAtivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="inativo" id="inativo-modal" <?php echo (isset($_GET['inativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="inativo-modal">
                                                Inativo
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countInativo']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-end mt-3 gap-2">
                            <button type="button" class="btn btn-light text-dark border" data-bs-dismiss="modal">Fechar</button>
                            <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                                <i class="fa-solid fa-filter"></i> Filtrar 
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Remover Dador -->
<div class="modal fade" id="deleteModalDador" tabindex="-1" aria-labelledby="deleteModalLabelDador" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabelDador">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem a certeza de que deseja remover este dador?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonDador" href="" class="btn btn-danger">Remover</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var deleteModal = document.getElementById("deleteModalDador");
        deleteModal.addEventListener("show.bs.modal", function(event) {
            var button = event.relatedTarget; 
            var dadorId = button.getAttribute("data-dador-id"); 
            var confirmButton = document.getElementById("deleteConfirmButtonDador");
            confirmButton.href = "includes/destroy.php?table=dadores&id=" + dadorId;
        });
        document.addEventListener("DOMContentLoaded", function() {
            const tabLinks = document.querySelectorAll('#statsTabs .nav-link');
            
            tabLinks.forEach(link => {
                link.classList.add('transition-all', 'duration-300', 'ease-in-out');
                
                link.addEventListener('mouseenter', () => {
                    if (!link.classList.contains('active')) {
                        link.classList.remove('text-body-secondary');
                        link.classList.add('text-danger-emphasis');
                    }
                });
                
                link.addEventListener('mouseleave', () => {
                    if (!link.classList.contains('active')) {
                        link.classList.remove('text-danger-emphasis');
                        link.classList.add('text-body-secondary');
                    }
                });
            });
        });
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
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'overview';
        
        // Ativa a aba correta
        const tabElement = document.querySelector(`#statsTabs a[href="#${activeTab}"]`);
        if (tabElement) {
            new bootstrap.Tab(tabElement).show();
        }
    });
</script>

<?php include 'partials/footer.php'; ?>