<?php

    require_once __DIR__ . '/db_connection.php';

    if (!$pdo) {
        die("Erro: Não foi possível conectar à base de dados.");
    }

    $table = $_POST['table'];

    if (!$table) {
        die("Erro: Nome da tabela inválido.");
    }

    $data = [];

    switch ($table) {
        case "bolsas_sangue":

            // Adicionar campos
            $location = "../inventario.php";

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
            if (isset($_POST['data_inscricao'])) { $data_inscricao = $_POST['data_inscricao']; $data['data_inscricao'] = $data_inscricao; }

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

            if (!isset($data['id_dador']) || !isset($data['data']) || !isset($data['hora']) || !isset($data['estado'])) {
                die("Erro: Todos os campos são obrigatórios.");
            }

            if ($data['data'] < date("Y-m-d")) {
                die("Erro: A data não pode ser no passado.");
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

    $dateFields = ['data_nascimento', 'data_inscricao'];
    foreach ($data as $field => $value) {
        if (in_array($field, $dateFields) && !validateDate($value)) {
            die("Erro: Data inválida.");
        }
    }

    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));

    $query_create = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($query_create);

    foreach ($data as $field => $value) {
        if (is_numeric($value)) {
            $stmt->bindValue(":$field", (int) $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$field", $value, PDO::PARAM_STR);
        }
    }

    try {
        $stmt->execute();
        header("Location: " . $location . "?status=success");
        exit;
    } catch (PDOException $e) {
        die("Erro ao criar registo: " . $e->getMessage());
    }

?>