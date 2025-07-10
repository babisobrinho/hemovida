<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $dados = [
        'titulo' => $_POST['titulo'],
        'data_inicio' => $_POST['data_inicio'],
        'data_fim' => $_POST['data_fim'],
        'meta' => $_POST['meta'],
        'prioridade' => $_POST['prioridade'],
        'descricao' => $_POST['descricao'] ?: null,
        'criado_em' => date('Y-m-d H:i:s')
    ];
    
    $tipos_selecionados = $_POST['tipos_sanguineos'] ?? [];

    try {
        $pdo->beginTransaction();

        // Insert main campaign
        $sqlCampanha = "INSERT INTO campanhas 
                        (titulo, data_inicio, data_fim, meta, prioridade, descricao, criado_em) 
                        VALUES 
                        (:titulo, :data_inicio, :data_fim, :meta, :prioridade, :descricao, :criado_em)";
        
        $stmtCampanha = $pdo->prepare($sqlCampanha);
        $stmtCampanha->execute($dados);
        $campanhaId = $pdo->lastInsertId();

        // Insert blood types
        if (!empty($tipos_selecionados)) {
            $sqlTipos = "INSERT INTO campanhas_tipos_sanguineos 
                        (id_campanha, tipo_sanguineo) 
                        VALUES 
                        (:id_campanha, :tipo_sanguineo)";
            
            $stmtTipos = $pdo->prepare($sqlTipos);
            $stmtTipos->bindParam(':id_campanha', $campanhaId, PDO::PARAM_INT);
            
            foreach ($tipos_selecionados as $tipo) {
                $stmtTipos->bindValue(':tipo_sanguineo', $tipo);
                $stmtTipos->execute();
            }
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'id' => $campanhaId]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erro ao criar campanha: ' . $e->getMessage()]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>