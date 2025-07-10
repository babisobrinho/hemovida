<?php
require_once __DIR__ . '/db_connection.php';

if (!$pdo) {
    die("Erro: Não foi possível conectar à base de dados.");
}

$table = $_POST['table'] ?? '';

if (!$table) {
    die("Erro: Nome da tabela inválido.");
}

$data = [];
$location = '';

switch ($table) {
    case "bolsas_sangue":
        $location = "../inventario.php";
        break;
        
    case "dadores":
        if (isset($_POST['nome'])) { $data['nome'] = $_POST['nome']; }
        if (isset($_POST['email'])) { $data['email'] = $_POST['email']; }
        if (isset($_POST['n_utente'])) { $data['n_utente'] = $_POST['n_utente']; }
        if (isset($_POST['data_nascimento'])) { $data['data_nascimento'] = $_POST['data_nascimento']; }
        if (isset($_POST['tipo_sanguineo'])) { $data['tipo_sanguineo'] = $_POST['tipo_sanguineo']; }
        if (isset($_POST['peso'])) { $data['peso'] = $_POST['peso']; }
        if (isset($_POST['sexo'])) { $data['sexo'] = $_POST['sexo']; }
        if (isset($_POST['estado'])) { $data['estado'] = $_POST['estado']; }
        if (isset($_POST['data_inscricao'])) { $data['data_inscricao'] = $_POST['data_inscricao']; }
        $location = "../dadores.php";
        break;

    case "doacoes":
        // Validação dos campos obrigatórios
        $requiredFields = ['dador', 'data', 'hora', 'estado'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                die("Erro: O campo '$field' é obrigatório.");
            }
        }

        // Preparar dados
        $data = [
            'id_dador' => (int)$_POST['dador'],
            'data' => $_POST['data'],
            'hora' => $_POST['hora'],
            'estado' => match($_POST['estado']) {
                'concluido' => 'Concluído',
                'cancelado' => 'Cancelado',
                'em_atendimento' => 'Em Atendimento',
                default => 'Agendado'
            }
        ];

        // Validação da data
        if ($data['data'] < date("Y-m-d")) {
            die("Erro: A data não pode ser no passado.");
        }

        $location = "../doacoes.php?dataSelecionada=" . $data['data'];
        break;

    case "exames":
        $location = "../exames.php";
        break;

    case "hospitais":
        if (isset($_POST['nome'])) { $data['nome'] = $_POST['nome']; }
        if (isset($_POST['endereco'])) { $data['endereco'] = $_POST['endereco']; }
        if (isset($_POST['telefone'])) { $data['telefone'] = $_POST['telefone']; }
        if (isset($_POST['email'])) { $data['email'] = $_POST['email']; }
        if (isset($_POST['nome_responsavel'])) { $data['nome_responsavel'] = $_POST['nome_responsavel']; }
        $data['estado'] = isset($_POST['estado']) ? 1 : 0;
        $location = "../hospitais.php";
        break;

    case "transfusoes":
        $location = "../transfusoes.php";
        break;

    default:
        die("Erro: Tabela inválida.");
}

// Validação de campos de data
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

$dateFields = ['data_nascimento', 'data_inscricao'];
foreach ($data as $field => $value) {
    if (in_array($field, $dateFields) && !validateDate($value)) {
        die("Erro: Data inválida para o campo $field.");
    }
}

// Preparar e executar a query
$columns = implode(", ", array_keys($data));
$placeholders = ":" . implode(", :", array_keys($data));
$query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

try {
    $stmt = $pdo->prepare($query);
    
    foreach ($data as $field => $value) {
        $stmt->bindValue(":$field", $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    
    if ($stmt->execute()) {
        // Atualização manual das campanhas (substitui a trigger)
        if ($table === "doacoes") {
            try {
                $updateQuery = "UPDATE campanhas c SET c.progresso = (
                    SELECT (COUNT(d.id)/c.meta)*100 
                    FROM doacoes d 
                    WHERE d.id_campanha = c.id
                    AND d.data BETWEEN c.data_inicio AND c.data_fim
                ) WHERE c.id = (SELECT id_campanha FROM doacoes WHERE id = ?)";
                
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([$pdo->lastInsertId()]);
            } catch (PDOException $e) {
                error_log("Erro ao atualizar campanhas: " . $e->getMessage());
            }
        }
        
        header("Location: $location");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao criar registo: " . $e->getMessage());
}