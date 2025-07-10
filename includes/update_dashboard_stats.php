<?php
require_once 'db_connection.php';

// Obter dados atualizados
$data = [
    'doacoesHoje' => $pdo->query("SELECT COUNT(*) FROM doacoes WHERE DATE(data) = CURDATE()")->fetchColumn(),
    'dadoresAtivos' => $pdo->query("SELECT COUNT(*) FROM dadores WHERE estado = 1")->fetchColumn(),
    'totalSangueDisponivel' => $pdo->query("SELECT COALESCE(SUM(volume_ml), 0)/1000 FROM bolsas_sangue WHERE estado = 'disponivel'")->fetchColumn(),
    'transfusoesHoje' => $pdo->query("SELECT COUNT(*) FROM transfusoes WHERE DATE(data) = CURDATE()")->fetchColumn(),
    'estoqueCritico' => $pdo->query("
        SELECT d.tipo_sanguineo, COUNT(*) as total 
        FROM bolsas_sangue b
        JOIN dadores d ON b.id_dador = d.id
        WHERE b.estado = 'disponivel'
        GROUP BY d.tipo_sanguineo
        HAVING total < 5
        ORDER BY total ASC
    ")->fetchAll(PDO::FETCH_ASSOC)
];
$examesHoje = $pdo->query("SELECT COUNT(*) FROM exames WHERE DATE(data) = CURDATE()")->fetchColumn();

echo json_encode([
    'doacoesHoje' => $doacoesHoje,
    'dadoresAtivos' => $dadoresAtivos,
    'totalSangueDisponivel' => $totalSangueDisponivel,
    'transfusoesHoje' => $transfusoesHoje,
    'examesHoje' => $examesHoje
]);

header('Content-Type: application/json');
echo json_encode($data);
?>