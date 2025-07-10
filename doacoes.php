<?php
    include 'partials/header.php';
    include 'includes/db_functions.php';
    
    $dataSelecionada = $_GET['dataSelecionada'] ?? date('Y-m-d');
    $doacoes = getDoacoesByDate($pdo, $dataSelecionada);

    $pageTitle = "Agenda de Doações";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Doações', 'url' => '#', 'active' => true]
    ];

    // Obter tipos sanguíneos
    $tiposSanguineosQuery = "SELECT DISTINCT tipo_sanguineo FROM dadores";
    $tiposSanguineosStmt = $pdo->query($tiposSanguineosQuery);
    $tiposSanguineos = $tiposSanguineosStmt->fetchAll(PDO::FETCH_ASSOC);

    // Estatísticas
    $allTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    
    // Total geral de doações
    $totalGeralQuery = "SELECT COUNT(*) as total FROM doacoes";
    $totalGeralStmt = $pdo->query($totalGeralQuery);
    $totalGeral = $totalGeralStmt->fetchColumn();

    // Doações por tipo sanguíneo (corrigido com JOIN)
    $statsQuery = "SELECT d.tipo_sanguineo, COUNT(*) AS total 
               FROM doacoes doa
               JOIN dadores d ON doa.id_dador = d.id
               GROUP BY d.tipo_sanguineo
               ORDER BY total DESC";
    $statsStmt = $pdo->query($statsQuery);
    $dbStats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obter tipo sanguíneo mais comum
    $tipoMaisComum = !empty($dbStats) ? $dbStats[0]['tipo_sanguineo'] : 'N/A';
    $totalTipoMaisComum = !empty($dbStats) ? $dbStats[0]['total'] : 0;

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

    // Doações na data selecionada
    $totalDataSelecionada = count($doacoes);

    // Média diária de doações
    $diasFuncionamento = 365;
    $mediaDiaria = $totalGeral > 0 ? round($totalGeral / $diasFuncionamento, 1) : 0;
?>

    <div class="container p-4">
        <?php include 'partials/page-header.php'; ?>
        
        <!-- Seção de Estatísticas -->
        <div class="container-fluid mb-5 px-0">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
                    <h5 class="mb-0 font-weight-bold text-danger">
                        <i class="fas fa-chart-line me-2"></i>Estatísticas de Doações
                    </h5>
                        
                    <ul class="nav nav-tabs border-0 mt-3" id="statsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 border-0 fw-bold <?= (!isset($_GET['tab']) || $_GET['tab'] === 'overview' ? 'active text-danger' : 'text-body-secondary') ?> link-danger link-opacity-75-hover" 
                            id="overview-tab" 
                            data-bs-toggle="tab" 
                            href="#overview" 
                            role="tab">
                                <i class="fas fa-eye me-1"></i> Visão Geral
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 border-0 fw-bold <?= (isset($_GET['tab']) && $_GET['tab'] === 'bloodtypes' ? 'active text-danger' : 'text-body-secondary') ?> link-danger link-opacity-75-hover" 
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
                                <div class="col-lg-3 col-md-6 border-right">
                                    <div class="p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                                <i class="fas fa-tint text-danger fa-lg"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 small text-muted">TOTAL</p>
                                                <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($totalGeral) ?></h3>
                                                <span class="badge bg-danger bg-opacity-10 text-danger small">Doações</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Doações na data -->
                                <div class="col-lg-3 col-md-6 border-right">
                                    <div class="p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                                <i class="fas fa-calendar-day text-danger fa-lg"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 small text-muted">DOAÇÕES HOJE</p>
                                                <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= number_format($totalDataSelecionada) ?></h3>
                                                <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                    Agendadas
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Média Diária -->
                                <div class="col-lg-3 col-md-6 border-right">
                                    <div class="p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                                <i class="fas fa-chart-bar text-danger fa-lg"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 small text-muted">MÉDIA DIÁRIA</p>
                                                <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= $mediaDiaria ?></h3>
                                                <span class="badge bg-danger bg-opacity-10 text-danger small">por dia</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tipo Sanguíneo Mais Comum -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 mr-3">
                                                <i class="fas fa-heartbeat text-danger fa-lg"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 small text-muted">TIPO MAIS COMUM</p>
                                                <h3 class="mb-0 mx-2 fs-4 font-weight-bold"><?= htmlspecialchars($tipoMaisComum, ENT_QUOTES, 'UTF-8') ?></h3>
                                                <span class="badge bg-danger bg-opacity-10 text-danger small">
                                                    <?= $totalTipoMaisComum ?> doações
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
                                    * Baseado em <?= $totalGeral ?> doações registadas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-flex align-content-center">
            <div class="col-md-6 col-12 d-flex align-content-center justify-content-start mb-2">
                <a href="doacao-criar.php" class="btn text-white" style="background-color: #202d3b;">
                    <i class="fa-solid fa-plus"></i> Nova Doação
                </a>
            </div>
            <div class="col-md-6 col-12 d-flex align-content-center justify-content-md-end justify-content-start">
                <form action="" method="GET">
                    <div class="input-group" style="width: 300px;">
                        <input type="date" class="form-control" name="dataSelecionada" id="dataSelecionada" value="<?php echo $dataSelecionada; ?>" required>
                        <button class="btn text-white" style="background-color: #202d3b;" type="submit">Escolher Data</button>
                    </div>           
                </form>
            </div>
        </div>
        <div class="container py-2 mt-4 mb-4">
            <?php if(count($doacoes) > 0): ?>
                <?php foreach($doacoes as $index => $doacao):
                    $detalhes = getDoacaoDetails($pdo, $doacao, $dataSelecionada); ?>
                    <div class="row">
                        <div class="col-auto text-center flex-column d-none d-sm-flex">
                            <div class="row h-50">
                                <?php if($index === count($doacoes) - 1 && $index > 1): ?>
                                    <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                    <div class="col">&nbsp;</div>
                                <?php elseif($index === 0): ?>
                                <?php else: ?>
                                    <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                    <div class="col">&nbsp;</div>
                                <?php endif; ?>
                            </div>
                            <h5 class="m-2">
                                <span class="badge shadow-sm" style="background-color: #202d3b; border-color: #202d3b;">&nbsp;</span>
                            </h5>
                            <div class="row h-50">
                                <?php if($index === count($doacoes) - 1): ?>
                                <?php elseif($index === 0): ?>
                                    <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                    <div class="col">&nbsp;</div>
                                <?php else: ?>
                                    <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                    <div class="col">&nbsp;</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col py-2">
                            <div class="card shadow">
                                <div class="card-header d-flex justify-content-between">
                                    <h4 class="card-title <?php echo $detalhes['isPastDoacao'] ? 'text-muted' : 'text-dark '; ?> m-0"><span><?php echo date('H:i', strtotime($doacao['hora'])); ?></span> - <?php echo $doacao['nome']; ?></h4>
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <a href="" data-bs-toggle="modal" data-bs-target="#formModalDoacao" data-dador-id="<?php echo $dador['id']; ?>" class="lh-1 text-decoration-none" style="color: #202d3b;">
                                            <i class="fa-solid fa-list-check"></i>
                                        </a>
                                        <a href="" data-bs-toggle="modal" data-bs-target="#completeModalDoacao" data-dador-id="<?php echo $dador['id']; ?>" class="lh-1 text-decoration-none" style="color: #202d3b;">
                                            <i class="fa-solid fa-square-check"></i>
                                        </a> 
                                        <a href="doacao-editar.php?table=doacoes&id=<?php echo $doacao['id']; ?>" class="lh-1 text-decoration-none" style="color: #202d3b;">
                                            <i class="fa-solid fa-file-pen"></i>
                                        </a>
                                        <a href="#" class="lh-1" data-bs-toggle="modal" data-bs-target="#deleteModalDoacao" data-doacao-id="<?php echo $doacao['id']; ?>" class="text-dark">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <p>
                                            <?php
                                                switch($doacao['estado']) {
                                                    case "concluido":
                                                        echo '<span class="float-right badge bg-success">';
                                                        echo 'Concluído';
                                                        echo '</span>';
                                                        break;
                                                    case "em_atendimento":
                                                        echo '<span class="float-right badge text-dark bg-light border">';
                                                        echo 'Em Atendimento';
                                                        echo '</span>';
                                                        break;
                                                    case "cancelado":
                                                        echo '<span class="float-right badge bg-danger">';
                                                        echo 'Cancelado';
                                                        echo '</span>';
                                                        break;
                                                    case "agendado":
                                                    default:
                                                        echo '<span class="float-right badge" style="background-color: #202d3b;">';
                                                        echo 'Agendado';
                                                        echo '</span>';
                                                        break;
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="row <?php echo $detalhes['isPastDoacao'] ? 'text-muted' : 'text-dark '; ?>">
                                        <div class="col-md-3 col-12">
                                            <p class="m-0"><b>Número de Utente:</b> <?php echo $doacao['n_utente']; ?></p>
                                            <p class="m-0"><b>Tipo Sanguíneo:</b> <?php echo $doacao['tipo_sanguineo']; ?></p>
                                            <p class="m-0">
                                                <b>Última Doação:</b> <?php echo $detalhes['ultima_doacao'] = $detalhes['ultima_doacao'] ? $detalhes['ultima_doacao'] : "Nenhuma doação anterior"; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <p class="m-0"><b>Idade:</b> <?php echo $detalhes['idade']; ?> anos</p>
                                            <p class="m-0"><b>Sexo:</b> <?php echo $doacao['sexo']; ?></p>
                                            <p class="m-0"><b>Peso:</b> <?php echo $doacao['peso']; ?>kg</p>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <p class="m-0">
                                                <b>Observações:</b> <?php echo isset($doacao['observacoes']) ? $doacao['observacoes'] : "Sem observações."; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Nenhuma doação agendada para <?php echo $dataSelecionada; ?>.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal: Remover Doação -->
    <div class="modal fade" id="deleteModalDoacao" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem a certeza de que deseja remover esta doação?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <a id="deleteConfirmButtonDoacao" href="#" class="btn btn-danger">Remover</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Concluir Doação -->
    <div class="modal fade" id="completeModalDoacao" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">Confirmar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem a certeza de que deseja marcar esta doação como concluída?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <a id="completeConfirmButtonDoacao" href="#" class="btn btn-success      ">Concluir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Formulário de Doação -->
    <div class="modal fade" id="formModalDoacao" tabindex="-1" aria-labelledby="formModalLabelDoacao" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabelDoacao">Formulário de Doação de Sangue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="card border-0 p-0 m-0">
                            <div class="card-body p-0">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="habitos" id="habitos" <?php echo (isset($_GET['habitos']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="habitos">
                                        Tem hábitos de vida saudáveis?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="gripe-febre" id="gripe-febre" <?php echo (isset($_GET['gripe-febre']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label form-checked-danger" for="gripe-febre">
                                        Teve gripe ou febre nos últimos 15 dias?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="cirurgia" id="cirurgia" <?php echo (isset($_GET['cirurgia']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="cirurgia">
                                        Fez uma cirurgia complexa há menos de 4 meses ou uma pequena cirurgia há menos de 1 semana?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="procedimento" id="procedimento" <?php echo (isset($_GET['procedimento']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="procedimento">
                                        Fez tatuagem, piercing, endoscopia ou colonoscopia nos últimos 4 meses?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="condicao" id="condicao" <?php echo (isset($_GET['condicao']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="condicao">
                                        Tem ou já teve alguma das seguintes condições: Diabetes Tipo 1, Epilepsia e/ou Cancro (há menos de 5 anos)?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="contacto-sexual" id="contacto-sexual" <?php echo (isset($_GET['contacto-sexual']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="contacto-sexual">
                                        Teve contacto sexual com uma nova pessoa nos últimos 3 meses?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="transfusao-1980" id="transfusao-1980" <?php echo (isset($_GET['transfusao-1980']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="transfusao-1980">
                                        Recebeu transfusão de sangue após 1980?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="gravidez" id="gravidez" <?php echo (isset($_GET['gravidez']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="gravidez">
                                        Está grávida, a amamentar ou teve um aborto recentemente?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="antibiotico" id="antibiotico" <?php echo (isset($_GET['antibiotico']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="antibiotico">
                                        Está a tomar antibióticos há menos de 14 dias?
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="viagem" id="viagem" <?php echo (isset($_GET['viagem']) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="viagem">
                                        Viajou recentemente para áreas de risco de doenças infecciosas?
                                    </label>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-end mt-3 gap-2">
                                <button type="button" class="btn btn-light text-dark border" data-bs-dismiss="modal">Fechar</button>
                                <button class="btn btn-success" type="submit">
                                    Responder
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.lh-1[data-bs-toggle="modal"]').forEach(function (deleteButton) {
            deleteButton.addEventListener('click', function () {
                const doacaoId = this.getAttribute('data-doacao-id');
                
                const deleteConfirmButton = document.getElementById('deleteConfirmButtonDoacao');
                deleteConfirmButton.setAttribute('href', 'includes/destroy.php?table=doacoes&id=' + doacaoId);
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
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = 'overview';
            
            // Ativa a aba correta
            const tabElement = document.querySelector(`#statsTabs a[href="#${activeTab}"]`);
            if (tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        });
    </script>

<?php include 'partials/footer.php'; ?>