<?php
ob_start();
session_start(); // Inicia a sessão antes de qualquer saída
include 'partials/header.php';

// Dados de conexão com o banco de dados
$host    = "localhost";
$usuario = "root";
$senha   = "";
$banco   = "hemovida";

// Cria a conexão
$conexao = mysqli_connect($host, $usuario, $senha, $banco);
if (!$conexao) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

// Se o formulário for enviado, processa os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obter e sanitizar os dados enviados
    $id_dador    = isset($_POST['id_dador']) ? mysqli_real_escape_string($conexao, $_POST['id_dador']) : '';
    $data_coleta = isset($_POST['data_coleta']) ? mysqli_real_escape_string($conexao, $_POST['data_coleta']) : '';
    $volume_ml   = isset($_POST['volume_ml']) ? mysqli_real_escape_string($conexao, $_POST['volume_ml']) : '';
    $estado      = isset($_POST['estado']) ? mysqli_real_escape_string($conexao, $_POST['estado']) : '';
    $validade    = isset($_POST['validade']) ? mysqli_real_escape_string($conexao, $_POST['validade']) : '';

    // Validação básica dos campos
    if (empty($id_dador) || empty($data_coleta) || empty($volume_ml) || empty($estado) || empty($validade)) {
        $error = "Todos os campos são obrigatórios.";
    } elseif (!is_numeric($volume_ml) || $volume_ml <= 0) {
        $error = "Volume deve ser um número maior que zero.";
    } else {
        // Preparar a query de inserção, agora incluindo o campo 'validade'
        $query = "INSERT INTO bolsas_sangue (id_dador, data_coleta, volume_ml, estado, validade) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $query);
        if (!$stmt) {
            $error = "Erro na preparação da consulta: " . mysqli_error($conexao);
        } else {
            // Observe: "isis s" indica os tipos dos parâmetros (i: inteiro, s: string)
            mysqli_stmt_bind_param($stmt, "isiss", $id_dador, $data_coleta, $volume_ml, $estado, $validade);
            if (mysqli_stmt_execute($stmt)) {
                // Em caso de sucesso, redireciona para a listagem (ou exibe uma mensagem)
                header("Location: bolsas_sangue.php?msg=sucesso");
                exit();
            } else {
                $error = "Erro ao inserir a nova bolsa: " . mysqli_error($conexao);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Criar Nova Bolsa de Sangue</h5>
        </div>
        <div class="card-body">
            <?php
            // Exibe a mensagem de erro, se houver
            if (isset($error)) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
            }
            ?>
            <!-- Formulário para criação da bolsa de sangue -->
            <form method="POST" action="">
                <!-- Seleção do Doador -->
                <div class="mb-3">
                    <label for="id_dador" class="form-label">Doador</label>
                    <select class="form-select" name="id_dador" id="id_dador" required>
                        <option value="" selected disabled>Selecione o doador</option>
                        <?php
                        // Consulta os doadores cadastrados para preencher o dropdown
                        $queryDadores = "SELECT id, nome, tipo_sanguineo FROM dadores";
                        $resultadoDadores = mysqli_query($conexao, $queryDadores);

                        if (mysqli_num_rows($resultadoDadores) > 0):
                            while ($dador = mysqli_fetch_assoc($resultadoDadores)):
                        ?>
                        <option value="<?= htmlspecialchars($dador['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($dador['nome'], ENT_QUOTES, 'UTF-8'); ?> - <?= htmlspecialchars($dador['tipo_sanguineo'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php
                            endwhile;
                        else:
                            echo '<option value="">Nenhum doador cadastrado</option>';
                        endif;
                        ?>
                    </select>
                </div>

                <!-- Data da Coleta -->
                <div class="mb-3">
                    <label for="data_coleta" class="form-label">Data da Coleta</label>
                    <input type="date" class="form-control" name="data_coleta" id="data_coleta" required>
                </div>

                <!-- Volume da Bolsa -->
                <div class="mb-3">
                    <label for="volume_ml" class="form-label">Volume (ml)</label>
                    <input type="number" class="form-control" name="volume_ml" id="volume_ml" min="1" required>
                </div>

                <!-- Estado da Bolsa -->
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="estado" required>
                        <option value="Disponível">Disponível</option>
                        <option value="Utilizada">Utilizada</option>
                        <option value="Vencida">Vencida</option>
                        <option value="Reservada">Reservada</option>
                    </select>
                </div>

                <!-- Data de Validade -->
                <div class="mb-3">
                    <label for="validade" class="form-label">Validade</label>
                    <input type="date" class="form-control" name="validade" id="validade" required>
                </div>

                <!-- Botão de Envio -->
                <button type="submit" class="btn text-white" style="background-color: #202d3b;">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar Bolsa
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>