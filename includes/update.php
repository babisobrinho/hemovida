<?php
require_once __DIR__ . '/db_connection.php';

if (!$pdo) {
    die("Erro: Não foi possível conectar ao banco de dados.");
}

// Verifica se os campos obrigatórios existem
if (!isset($_POST['table']) || !isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Erro: Parâmetros inválidos.");
}

$table = $_POST['table'];
$id = $_POST['id'];

$data = [];
$location = '';

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

switch ($table) {
    case "bolsas_sangue":
        // Campos permitidos para atualização
        $allowedFields = ['volume_ml', 'data_coleta', 'validade', 'estado'];
        
        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                // Validações específicas
                if ($field === 'volume_ml' && !is_numeric($_POST[$field])) {
                    die("Erro: Volume deve ser numérico.");
                }
                if (($field === 'data_coleta' || $field === 'validade') && !validateDate($_POST[$field])) {
                    die("Erro: Data inválida para $field.");
                }
                
                $data[$field] = $_POST[$field];
            }
        }
        
        $location = "../bolsas_sangue.php";
        break;

    case "dadores":
        if (isset($_POST['nome'])) { $data['nome'] = $_POST['nome']; }
        if (isset($_POST['email'])) { $data['email'] = $_POST['email']; }
        if (isset($_POST['n_utente'])) { $data['n_utente'] = $_POST['n_utente']; }
        if (isset($_POST['data_nascimento']) && validateDate($_POST['data_nascimento'])) { 
            $data['data_nascimento'] = $_POST['data_nascimento']; 
        }
        if (isset($_POST['tipo_sanguineo'])) { $data['tipo_sanguineo'] = $_POST['tipo_sanguineo']; }
        if (isset($_POST['peso'])) { $data['peso'] = $_POST['peso']; }
        if (isset($_POST['sexo'])) { $data['sexo'] = $_POST['sexo']; }
        if (isset($_POST['estado'])) { $data['estado'] = $_POST['estado']; }

        $location = "../dadores.php";
        break;

    case "doacoes":
        if (isset($_POST['dador'])) { $data['id_dador'] = $_POST['dador']; }
        if (isset($_POST['data']) && validateDate($_POST['data'])) { $data['data'] = $_POST['data']; }
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

        $location = "../doacoes.php?dataSelecionada=" . ($data['data'] ?? '');
        break;

    case "exames":
        // Implementação para exames
        $location = "../exames.php";
        break;

    case "hospitais":
        // Implementação para hospitais
        $location = "../hospitais.php";
        break;

    case "transfucoes":
        // Implementação para transfusões
        $location = "../transfusoes.php";
        break;

    default:
        die("Erro: Tabela inválida.");
}

// Remove campos vazios
$data = array_filter($data, function($value) {
    return $value !== null && $value !== '';
});

// Se não houver dados para atualizar, redireciona
if (empty($data)) {
    header("Location: $location");
    exit;
}

// Prepara a query SQL
$setParts = [];
foreach ($data as $field => $value) {
    $setParts[] = "$field = :$field";
}

$setQuery = implode(", ", $setParts);
$query = "UPDATE $table SET $setQuery WHERE id = :id";

try {
    $stmt = $pdo->prepare($query);
    
    // Vincula os parâmetros
    foreach ($data as $field => $value) {
        $paramType = is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue(":$field", $value, $paramType);
    }
    
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    
    // Executa a atualização
    if ($stmt->execute()) {
        header("Location: $location");
        exit;
    } else {
        die("Erro ao atualizar registro.");
    }
} catch (PDOException $e) {
    die("Erro ao atualizar registo: " . $e->getMessage());
}
?>