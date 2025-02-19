<?php
    include 'partials/header.php';

    $pageTitle = "Bolsas de Sangues";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Inventário', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
</div>

<div class="d-flex justify-content-around">
    <div>
        <a href="bolsas_sangue-criar.php" class="btn text-red" style="background-color: #202d3b;">
            <i class="fa-solid fa-plus" style="color: #ff0000;"></i> Adicionar Bolsa
        </a>
    </div>
</div>

<div class="container py-2 mt-4 mb-4">
    <!-- Filtro de Bolsas de Sangue -->
    <div class="accordion py-2" id="accordionFiltro">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button text-dark" style="background-color: #f1f1f1;" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltro" aria-expanded="true" aria-controls="collapseFiltro">
                    Aplicar Filtros
                </button>
            </h2>
            <div id="collapseFiltro" class="accordion-collapse collapse show" data-bs-parent="#accordionFiltro">
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
                                            $tipos_sanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                            foreach ($tipos_sanguineos as $tipo) {
                                                $id = strtolower(str_replace(['+', '-'], ['positivo', 'negativo'], $tipo));
                                                echo '
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="' . $tipo . '" name="tipo_sanguineo[]" id="' . $id . '" ' . (in_array($tipo, (array)($_GET['tipo_sanguineo'] ?? [])) ? 'checked' : '') . '>
                                                    <label class="form-check-label" for="' . $id . '">
                                                        ' . $tipo . '
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end">' . ($contagens['count' . str_replace(['+', '-'], ['Positivo', 'Negativo'], $tipo)] ?? 0) . '</span>
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
                                            $estados = ['Disponível', 'Utilizada', 'Vencida', 'Reservada'];
                                            foreach ($estados as $estado) {
                                                $id = strtolower($estado);
                                                echo '
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="' . $estado . '" name="estado[]" id="' . $id . '" ' . (in_array($estado, (array)($_GET['estado'] ?? [])) ? 'checked' : '') . '>
                                                    <label class="form-check-label" for="' . $id . '">
                                                        ' . $estado . '
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end">' . ($contagens['count' . $estado] ?? 0) . '</span>
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

    <!-- Listagem das Bolsas de Sangue -->
    <div class="accordion py-2">
        <?php
        $host = "localhost";  // ou o nome do seu servidor
        $usuario = "root";  // seu nome de usuário
        $senha = "";  // sua senha
        $banco = "hemovida";  // o nome do seu banco de dados

        // Crie a conexão
        $conexao = mysqli_connect($host, $usuario, $senha, $banco);

        // Verifique se a conexão foi bem-sucedida
        if (!$conexao) {
            die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
        }

        // Obter os valores dos filtros
        $tipos_sanguineos = $_GET['tipo_sanguineo'] ?? [];
        $estados = $_GET['estado'] ?? [];
        
        // Construir a consulta SQL com base nos filtros
        $query = "SELECT bolsas_sangue.id, bolsas_sangue.data_coleta, bolsas_sangue.volume_ml, bolsas_sangue.estado, dadores.tipo_sanguineo
                  FROM bolsas_sangue
                  JOIN dadores ON bolsas_sangue.id_dador = dadores.id
                  WHERE 1=1";
        
        // Filtro: Tipo Sanguíneo
        if (!empty($tipos_sanguineos)) {
            // Garantir que $tipos_sanguineos seja um array
            $tipos_sanguineos = (array)$tipos_sanguineos;
        
            // Escapar os valores para evitar SQL injection
            $tipos_sanguineos = array_map(function($valor) use ($conexao) {
                return mysqli_real_escape_string($conexao, $valor);
            }, $tipos_sanguineos);
        
            $query .= " AND dadores.tipo_sanguineo IN ('" . implode("','", $tipos_sanguineos) . "')";
        }
        
        // Filtro: Estado
        if (!empty($estados)) {
            // Garantir que $estados seja um array
            $estados = (array)$estados;
        
            // Escapar os valores para evitar SQL injection
            $estados = array_map(function($valor) use ($conexao) {
                return mysqli_real_escape_string($conexao, $valor);
            }, $estados);
        
            $query .= " AND bolsas_sangue.estado IN ('" . implode("','", $estados) . "')";
        }
        
        // Executar a consulta
        $resultado = mysqli_query($conexao, $query);
        
        if (mysqli_num_rows($resultado) > 0):
            while ($bolsa = mysqli_fetch_assoc($resultado)):
        ?>
        <!-- Exibir as bolsas de sangue -->
        <div class="row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <h5 class="m-2"></h5>
                <div class="col">
                    <div class="card p-3 shadow-sm">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Data coleta: <?= htmlspecialchars($bolsa['data_coleta'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <a href="bolsas_sangue-editar.php" class="lh-1 text-decoration-none" style="color: #202d3b;">
                                    <i class="fa-regular fa-pen-to-square" style="color: #901818;"></i>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModalBolsas" data-bolsas-sangue-id="<?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?>">
    <i class="fa-regular fa-trash-can" style="color: #901818;"></i>
</a>
                            </div>
                        </div>
                        <div class="card-header d-flex justify-content-between">
                            <p class="card-text flex-grow-1">
                                <strong>ID:</strong> <?= htmlspecialchars($bolsa['id'], ENT_QUOTES, 'UTF-8') ?><br>
                                <strong>Tipo Sanguíneo:</strong> <?= htmlspecialchars($bolsa['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') ?><br>
                                <strong>Data de Coleta:</strong> <?= htmlspecialchars($bolsa['data_coleta'], ENT_QUOTES, 'UTF-8') ?><br>
                                <strong>Volume:</strong> <?= htmlspecialchars($bolsa['volume_ml'], ENT_QUOTES, 'UTF-8') ?> ml
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <p>
                                    <?php
                                    if (isset($bolsa) && is_array($bolsa) && isset($bolsa['estado'])) {
                                        $estado = $bolsa['estado'];
                                    } else {
                                        $estado = 'Indefinido'; // Define um valor padrão caso a chave não exista
                                    }
        
                                    switch ($estado) {
                                        case "Disponível":
                                            echo '<span class="float-right badge bg-success">';
                                            echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
                                            echo '</span>';
                                            break;
                                        case "Utilizada":
                                            echo '<span class="float-right badge bg-warning">';
                                            echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
                                            echo '</span>';
                                            break;
                                        case "Vencida":
                                            echo '<span class="float-right badge bg-danger">';
                                            echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
                                            echo '</span>';
                                            break;
                                        case "Reservada":
                                        default:
                                            echo '<span class="float-right badge" style="background-color: #202d3b;">';
                                            echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
                                            echo '</span>';
                                            break;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            endwhile;
        else:
            echo "Não há bolsas de sangue disponíveis.";
        endif;
        ?>
        <!-- Modal: Remover bolsas -->
        <div class="modal fade" id="deleteModalBolsas" tabindex="-1" aria-labelledby="deleteModalLabelBolsas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabelBolsas">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem a certeza de que deseja remover esta bolsa de sangue?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonBolsas" href="" class="btn btn-danger">Remover</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var deleteModal = document.getElementById("deleteModalBolsas");
    deleteModal.addEventListener("show.bs.modal", function(event) {
        var button = event.relatedTarget; 
        // Obtenha o ID da bolsa a partir do atributo data-bolsas-sangue-id
        var bolsaId = button.getAttribute("data-bolsas-sangue-id"); 
        var confirmButton = document.getElementById("deleteConfirmButtonBolsas");
        // Configure o link para o script de deleção, passando a tabela e o ID da bolsa
        confirmButton.href = "includes/destroy.php?table=bolsas_sangue&id=" + bolsaId;
    });
});
</script>
        <?php include 'partials/footer.php'; ?>