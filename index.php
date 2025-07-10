<?php
include 'partials/header.php';
include 'includes/index_info.php';

// Consultas otimizadas para o dashboard
$estoqueCritico = $pdo->query("
    SELECT d.tipo_sanguineo, COUNT(*) as total 
    FROM bolsas_sangue b
    JOIN dadores d ON b.id_dador = d.id
    WHERE b.estado = 'Disponível'
    GROUP BY d.tipo_sanguineo
    HAVING total < 5
    ORDER BY total ASC
")->fetchAll();

$proximasDoacoes = $pdo->query("
    SELECT d.nome, do.data, do.hora, d.tipo_sanguineo 
    FROM doacoes do
    JOIN dadores d ON do.id_dador = d.id
    WHERE do.data >= CURDATE()
    ORDER BY do.data ASC, do.hora ASC
    LIMIT 5
")->fetchAll();

$campanhasAtivas = $pdo->query("
    SELECT * FROM campanhas
    WHERE data_fim >= CURDATE()
    ORDER BY data_fim ASC
    LIMIT 3
")->fetchAll();

// Nova consulta para hospitais ativos
$hospitaisAtivos = $pdo->query("SELECT COUNT(*) FROM hospitais WHERE estado = 1")->fetchColumn();

$pageTitle = "Painel de Controle HemoVida";
$breadcrumbItems = [['title' => 'Dashboard', 'url' => 'index.php', 'active' => true]];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <!-- Painel de Status Moderno -->
    <div class="row g-4 mb-4">
        <!-- Card Doações Hoje -->
        <div class="col-sm col-md-3 col-lg">
            <div class="card bg-white border-0 shadow-sm h-100 hover-scale">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-heart-pulse fa-2x text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold" id="doacoes-hoje-stat"><?= $doacoesHoje ?></h3>
                            <p class="text-muted mb-0">Doações Hoje</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="doacoes.php" class="btn btn-sm btn-outline-danger stretched-link">
                            Ver detalhes <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Dadores Ativos -->
        <div class="col col-md-3 col-lg">
            <div class="card bg-white border-0 shadow-sm h-100 hover-scale">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold" id="dadores-ativos-stat"><?= $dadoresAtivos ?></h3>
                            <p class="text-muted mb-0">Dadores Ativos</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="dadores.php" class="btn btn-sm btn-outline-primary stretched-link">
                            Ver lista <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Estoque -->
        <div class="col col-md-3 col-lg">
            <div class="card bg-white border-0 shadow-sm h-100 hover-scale">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-droplet fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold" id="estoque-stat"><?= $totalSangueDisponivel ?>L</h3>
                            <p class="text-muted mb-0">Estoque Disponível</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="bolsas_sangue.php" class="btn btn-sm btn-outline-warning stretched-link">
                            Ver inventário <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100"></div>
        <!-- Card Transfusões -->
        <div class="col col-md-3 col-lg">
            <div class="card bg-white border-0 shadow-sm h-100 hover-scale">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-hand-holding-medical fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold" id="transfusoes-hoje-stat"><?= $transfusoesHoje ?></h3>
                            <p class="text-muted mb-0">Transfusões Hoje</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="transfusoes.php" class="btn btn-sm btn-outline-success stretched-link">
                            Ver registros <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Exames Hoje -->
        <div class="col col-md-3 col-lg">
            <div class="card bg-white border-0 shadow-sm h-100 hover-scale">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-flask fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold" id="exames-hoje-stat"><?= $examesHoje ?></h3>
                            <p class="text-muted mb-0">Exames Hoje</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="exames.php" class="btn btn-sm btn-outline-info stretched-link">
                            Ver detalhes <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- NOVO CARD: Hospitais Ativos -->
        <div class="col col-md-3 col-lg">
            <div class="card bg-white border-0 shadow-sm h-100 hover-scale">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-dark bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-hospital fa-2x text-dark"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold" id="hospitais-ativos-stat"><?= $hospitaisAtivos ?></h3>
                            <p class="text-muted mb-0">Hospitais Ativos</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="hospitais.php" class="btn btn-sm btn-outline-dark stretched-link">
                            Ver lista <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Seção Principal -->
    <div class="row g-4">
        <!-- Gráfico de Necessidades -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line text-danger me-2"></i>Demanda de Sangue</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-danger dropdown-toggle" type="button" id="periodoDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Últimos 6 meses
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="periodoDropdown">
                            <li><a class="dropdown-item periodo-option" href="#" data-meses="3">3 meses</a></li>
                            <li><a class="dropdown-item periodo-option" href="#" data-meses="6">6 meses</a></li>
                            <li><a class="dropdown-item periodo-option" href="#" data-meses="12">1 ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="demandChart" height="300"></canvas>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <small class="text-muted">Dados atualizados em <?= date('d/m/Y H:i') ?></small>
                </div>
            </div>
        </div>

        <!-- Alertas Críticos -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Estoque Crítico</h5>
                    <span class="badge bg-danger"><?= count($estoqueCritico) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if(empty($estoqueCritico)): ?>
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-4x text-success opacity-25"></i>
                            </div>
                            <h5 class="text-muted mb-0">Estoque estável</h5>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($estoqueCritico as $estoque): ?>
                            <div class="list-group-item border-0 py-3 d-flex justify-content-between align-items-center hover-bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-droplet text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Tipo <?= $estoque['tipo_sanguineo'] ?></h6>
                                        <small class="text-muted"><?= $estoque['total'] ?> bolsa(s) restante(s)</small>
                                    </div>
                                </div>
                                <a href="bolsas_sangue.php?filter=<?= $estoque['tipo_sanguineo'] ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#novaCampanhaModal">
                        <i class="fas fa-bullhorn me-1"></i> Criar Campanha
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção Secundária -->
    <div class="row g-4 mt-4">
        <!-- Próximas Doações -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-day text-primary me-2"></i>Próximas Doações</h5>
                    <a href="doacoes.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    <?php if(empty($proximasDoacoes)): ?>
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-calendar-plus fa-4x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted mb-0">Nenhuma doação agendada</h5>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($proximasDoacoes as $doacao): 
                                $isToday = (date('Y-m-d') == $doacao['data']);
                            ?>
                            <div class="list-group-item border-0 py-3 hover-bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($doacao['nome']) ?></h6>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($doacao['data'])) ?> às <?= substr($doacao['hora'], 0, 5) ?>
                                                <?php if($isToday): ?>
                                                    <span class="badge bg-primary ms-2">Hoje</span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                        <i class="fas fa-tint me-1"></i> <?= $doacao['tipo_sanguineo'] ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Campanhas Ativas -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-bullhorn text-success me-2"></i>Campanhas Ativas</h5>
                    <a href="campanhas.php" class="btn btn-sm btn-outline-success">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <?php if(empty($campanhasAtivas)): ?>
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-bullhorn fa-4x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted mb-0">Nenhuma campanha ativa</h5>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($campanhasAtivas as $campanha): 
                                $progress = min(100, ($campanha['arrecadado'] / $campanha['meta']) * 100);
                                $daysLeft = ceil((strtotime($campanha['data_fim']) - time()) / (60 * 60 * 24));
                            ?>
                            <div class="list-group-item border-0 py-3 hover-bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="mb-0"><?= htmlspecialchars($campanha['titulo']) ?></h6>
                                    <small class="text-muted"><?= $campanha['arrecadado'] ?>/<?= $campanha['meta'] ?></small>
                                </div>
                                <div class="progress bg-light" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($campanha['data_inicio'])) ?> - 
                                        <?= date('d/m/Y', strtotime($campanha['data_fim'])) ?>
                                    </small>
                                    <small class="fw-bold <?= $progress >= 100 ? 'text-success' : ($progress > 50 ? 'text-primary' : 'text-danger') ?>">
                                        <?= round($progress) ?>% • <?= $daysLeft ?> dias
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Campanha -->
<div class="modal fade" id="novaCampanhaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-bullhorn me-2"></i>Nova Campanha</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="campanhaForm" action="includes/criar_campanha.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="titulo" class="form-label">Título da Campanha</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                       <div class="col-12">
                        <label class="form-label">Tipos Sanguíneos</label>
                        <div class="row">
                            <?php 
                            $tiposDisponiveis = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                            foreach ($tiposDisponiveis as $tipo): ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                        id="modal_tipo_<?= $tipo ?>" name="tipos_sanguineos[]" 
                                        value="<?= $tipo ?>">
                                    <label class="form-check-label" for="modal_tipo_<?= $tipo ?>">
                                        <?= $tipo ?>
                                    </label>
                                </div>
                            </div>
                             <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Se nenhum for selecionado, a campanha será para todos os tipos.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="data_inicio" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="data_fim" class="form-label">Data Término</label>
                            <input type="date" class="form-control" id="data_fim" name="data_fim" required>
                        </div>
                        <div class="col-md-6">
                            <label for="meta" class="form-label">Meta (número de doações)</label>
                            <input type="number" class="form-control" id="meta" name="meta" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="prioridade" class="form-label">Prioridade</label>
                            <select class="form-select" id="prioridade" name="prioridade">
                                <option value="normal">Normal</option>
                                <option value="urgente">Urgente</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-plus me-1"></i> Criar Campanha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.hover-scale {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-scale:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.hover-bg-light:hover {
    background-color: rgba(0,0,0,0.02) !important;
}
.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
}
.badge {
    font-weight: 500;
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
// Variável global para o gráfico
let demandChart;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializa o gráfico
    const ctx = document.getElementById('demandChart').getContext('2d');
    demandChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            datasets: [
                {
                    label: 'Doações (últimos 6 meses)',
                    data: [45, 15, 30, 10, 8, 5, 60, 20],
                    backgroundColor: 'rgba(187, 45, 59, 0.8)',
                    borderColor: 'rgba(187, 45, 59, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Transfusões (últimos 6 meses)',
                    data: [40, 18, 28, 12, 10, 6, 65, 25],
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'system-ui, -apple-system',
                            size: 12
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Configura o dropdown de período
    document.querySelectorAll('.periodo-option').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const meses = this.getAttribute('data-meses');
            
            // Atualiza o texto do botão
            document.getElementById('periodoDropdown').textContent = 
                `Últimos ${meses} ${meses > 1 ? 'meses' : 'mês'}`;
            
            // Atualiza o gráfico
            updateChartData(meses);
        });
    });

    // Função para atualizar o gráfico
    window.updateChartData = function(meses) {
        fetch(`includes/get_chart_data.php?meses=${meses}`)
            .then(response => response.json())
            .then(data => {
                if (demandChart) {
                    // Atualiza os rótulos e dados do gráfico
                    demandChart.data.labels = data.labels;
                    
                    // Atualiza os datasets 
                    demandChart.data.datasets.forEach((dataset, index) => {
                        if (index === 0) {
                            dataset.data = data.doacoes;
                            dataset.label = `Doações (últimos ${meses} ${meses > 1 ? 'meses' : 'mês'})`;
                        } else if (index === 1) {
                            dataset.data = data.transfusoes;
                            dataset.label = `Transfusões (últimos ${meses} ${meses > 1 ? 'meses' : 'mês'})`;
                        }
                    });
                    
                    demandChart.update();
                    
                    // Atualiza o timestamp
                    document.querySelector('.card-footer small').textContent = 
                        `Dados atualizados em ${new Date().toLocaleString('pt-PT')}`;
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar gráfico:', error);
            });
    };
});
document.getElementById('campanhaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const tiposSelecionados = [];
    document.querySelectorAll('input[name="tipos_sanguineos[]"]:checked').forEach(checkbox => {
        tiposSelecionados.push(checkbox.value);
    });
    
    // Add blood types to form data
    tiposSelecionados.forEach((tipo, index) => {
        formData.append(`tipos_sanguineos[${index}]`, tipo);
    });

    fetch('includes/criar_campanha.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and refresh
            $('#novaCampanhaModal').modal('hide');
            alert('Campanha criada com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    });
});

// Atualização automática a cada 5 minutos
setInterval(() => {
    // Obtém o período atual selecionado
    const periodoText = document.getElementById('periodoDropdown').textContent;
    const mesesMatch = periodoText.match(/\d+/);
    const meses = mesesMatch ? parseInt(mesesMatch[0]) : 6;
    
    // Atualiza os dados do dashboard
    fetch('includes/update_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            // Atualiza os cards principais
            document.getElementById('doacoes-hoje-stat').textContent = data.doacoesHoje;
            document.getElementById('dadores-ativos-stat').textContent = data.dadoresAtivos;
            document.getElementById('estoque-stat').textContent = data.totalSangueDisponivel + 'L';
            document.getElementById('transfusoes-hoje-stat').textContent = data.transfusoesHoje;
            document.getElementById('exames-hoje-stat').textContent = data.examesHoje;
            // Atualiza o novo card de hospitais ativos
            document.getElementById('hospitais-ativos-stat').textContent = data.hospitaisAtivos;
            
            // Atualiza o gráfico com o período selecionado
            updateChartData(meses);
        });
}, 300000); // 5 minutos
</script>

<?php include 'partials/footer.php'; ?>