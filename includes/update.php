<?php
 require_once __DIR__ . '/db_connection.php';

    if (!$pdo) {
        die("Erro: Não foi possível conectar ao banco de dados.");
    }

    $table = $_POST['table'];
    $id = $_POST['id'];

    if (!$table || !$id || !is_numeric($id)) {
        die("Erro: ID ou nome da tabela inválidos.");
    }

    $data = [];

    switch ($table) {
        case "bolsas_sangue":
            // Coleta os dados do formulário
            if (isset($_POST['id'])) { 
                $id = $_POST['id']; 
                $data['id'] = $id; 
            }
            if (isset($_POST['tipo_sanguineo'])) { 
                $tipo_sanguineo = $_POST['tipo_sanguineo']; 
                $data['tipo_sanguineo'] = $tipo_sanguineo; 
            }
            if (isset($_POST['volume_ml'])) { 
                $volume_ml = $_POST['volume_ml']; 
                $data['volume_ml'] = $volume_ml; 
            }
            if (isset($_POST['data_coleta'])) { 
                $data_coleta = $_POST['data_coleta']; 
                $data['data_coleta'] = $data_coleta; 
            }
            if (isset($_POST['estado'])) { 
                $estado = $_POST['estado']; 
                switch ($estado) {
                    case "concluido":
                        $data['estado'] = "Disponível";
                        break;
                    case "Reservada":
                        $data['estado'] = "Reservada";
                        break;
                    case "Utilizada":
                        $data['estado'] = "Utilizada";
                        break;
                    case "Vencida":
                    default:
                        $data['estado'] = "Vencida";
                        break;
                }
            }
    
            $location = "../bolsas_sangue.php"; // Redirecionamento após a atualização
            break;
    
        
        case "dadores":

            if (isset($_POST['nome'])) { $nome = $_POST['nome']; $data['nome'] = $nome; }
            if (isset($_POST['email'])) { $email = $_POST['email']; $data['email'] = $email; }
            if (isset($_POST['n_utente'])) { $n_utente = $_POST['n_utente']; $data['n_utente'] = $n_utente; }
            if (isset($_POST['data_nascimento'])) { $data_nascimento = $_POST['data_nascimento']; $data['data_nascimento'] = $data_nascimento; }
            if (isset($_POST['tipo_sanguineo'])) { $tipo_sanguineo = $_POST['tipo_sanguineo']; $data['tipo_sanguineo'] = $tipo_sanguineo; }
            if (isset($_POST['peso'])) { $peso = $_POST['peso']; $data['peso'] = $peso; }
            if (isset($_POST['sexo'])) { $sexo = $_POST['sexo']; $data['sexo'] = $sexo; }
            if (isset($_POST['estado'])) { $estado = $_POST['estado']; $data['estado'] = $estado; }

            $location = "../dadores.php";

            break;

        case "doacoes":

            if (isset($_POST['dador'])) { $data['id_dador'] = $_POST['dador']; }
            if (isset($_POST['data'])) { $data['data'] = $_POST['data']; }
            if (isset($_POST['hora'])) { $data['hora'] = $_POST['hora']; }
            if (isset($_POST['estado'])) { 
                $estado = $_POST['estado']; 
                switch ($estado) {
                    case "concluido":
                        $data['estado'] = "Concluído";
                        break;
                    case "cancelado":
                        $data['estado'] = "Cancelado";
                        break;
                    case "agendado":
                    default:
                        $data['estado'] = "Agendado";
                        break;
                }
            }

            $location = "../doacoes.php?dataSelecionada=" . $data['data'];

            break;

        case "exames":

            // Adicionar campos
            $location = "../exames.php";

            break;

        case "hospitais":

            // Adicionar campos
            $location = "../hospitais.php";

            break;

        case "transfucoes":

            // Adicionar campos
            $location = "../transfusoes.php";

            break;
    }

    function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    $dateFields = ['data_nascimento', 'data_inscricao', 'data'];
    foreach ($data as $field => $value) {
        if (in_array($field, $dateFields) && !validateDate($value)) {
            die("Erro: Data inválida.");
        }
    }

    $dateFields = ['data_coleta']; // Adicione outros campos de data, se necessário
foreach ($data as $field => $value) {
    if (in_array($field, $dateFields) && !validateDate($value)) {
        die("Erro: Data inválida.");
    }
}

    $setParts = [];
    foreach ($data as $field => $value) {
        if (is_numeric($value)) {
            $value = (int) $value;
        }
        $setParts[] = "$field = :$field";
    }

    $setQuery = implode(", ", $setParts);
    $query_editar = "UPDATE $table SET $setQuery WHERE id = :id";
    $stmt = $pdo->prepare($query_editar);

    foreach ($data as $field => $value) {
        if (is_numeric($value)) {
            $stmt->bindValue(":$field", (int) $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$field", $value, PDO::PARAM_STR);
        }
    }

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    foreach ($data as $field => $value) {
        echo "$field = $value <br>";
    }

    try {
        $stmt->execute();
        header("Location: " . $location);
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar registo: " . $e->getMessage());
    }
   
?>