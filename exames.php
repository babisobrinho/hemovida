<?php

    include 'partials/header.php';

    


    $pageTitle = "Análises Clínicas";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Exames', 'url' => '#', 'active' => true]
    ];
    
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $database = "hemovida"; 
    
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
?> 


<?php

$sql = "SELECT * FROM exames";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2 class="mb-4">Lista de Exames</h2>

    <!-- Botão para adicionar exame -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalExame">
        Adicionar Novo Exame
    </button>

    <!-- Modal para adicionar exame -->
    <div class="modal fade" id="modalExame" tabindex="-1" aria-labelledby="modalExameLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExameLabel">Adicionar Novo Exame</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="includes/store.php">
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

    <!-- Tabela com exames -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ID Bolsa</th>
                    <th>Data</th>
                    <th>Hemoglobina</th>
                    <th>Hepatite</th>
                    <th>HIV</th>
                    <th>Chagas</th>
                    <th>Sífilis</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['id_bolsa']}</td>
                            <td>" . date("d/m/Y", strtotime($row['data'])) . "</td>
                            <td>{$row['hemoglobina']} g/dL</td>
                            <td>" . ($row['hepatite'] ? '<span class="badge bg-danger">Positivo</span>' : '<span class="badge bg-success">Negativo</span>') . "</td>
                            <td>" . ($row['hiv'] ? '<span class="badge bg-danger">Positivo</span>' : '<span class="badge bg-success">Negativo</span>') . "</td>
                            <td>" . ($row['chagas'] ? '<span class="badge bg-danger">Positivo</span>' : '<span class="badge bg-success">Negativo</span>') . "</td>
                            <td>" . ($row['sifilis'] ? '<span class="badge bg-danger">Positivo</span>' : '<span class="badge bg-success">Negativo</span>') . "</td>
                            <td><strong>{$row['resultado']}</strong></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>Nenhum exame encontrado</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>



    
                



<?php
    include 'partials/footer.php';
?>