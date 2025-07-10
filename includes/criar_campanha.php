<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'titulo' => $_POST['titulo'],
        'tipo_sanguineo' => $_POST['tipo_sanguineo'] ?: null,
        'data_inicio' => $_POST['data_inicio'],
        'data_fim' => $_POST['data_fim'],
        'meta' => $_POST['meta'],
        'prioridade' => $_POST['prioridade'],
        'descricao' => $_POST['descricao'] ?: null,
        'criado_em' => date('Y-m-d H:i:s')
    ];

    try {
        $sql = "INSERT INTO campanhas (titulo, tipo_sanguineo, data_inicio, data_fim, meta, prioridade, descricao, criado_em) 
                VALUES (:titulo, :tipo_sanguineo, :data_inicio, :data_fim, :meta, :prioridade, :descricao, :criado_em)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($dados);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar campanha: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>