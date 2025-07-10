<?php 
require_once __DIR__ . '/db_connection.php';

$query = "
    SELECT 
        (SELECT COUNT(*) FROM doacoes WHERE DATE(doacoes.data) = CURDATE()) AS doacoesHoje,
        (SELECT COUNT(*) FROM dadores WHERE dadores.estado = 1) AS dadoresAtivos,
        (SELECT COALESCE(SUM(volume_ml), 0) FROM bolsas_sangue WHERE bolsas_sangue.estado = 'disponivel') AS totalSangueDisponivel_ml,
        (SELECT COUNT(*) FROM exames WHERE DATE(exames.data) = CURDATE()) AS examesHoje,
        (SELECT COUNT(*) FROM transfusoes WHERE DATE(transfusoes.data) = CURDATE()) AS transfusoesHoje,
        (SELECT COUNT(*) FROM hospitais) AS totalHospitais
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    $doacoesHoje = $data['doacoesHoje'];
    $dadoresAtivos = $data['dadoresAtivos'];
    $totalSangueDisponivel = $data['totalSangueDisponivel_ml'] / 1000;
    $examesHoje = $pdo->query("SELECT COUNT(*) FROM exames WHERE DATE(data) = CURDATE()")->fetchColumn();
    $transfusoesHoje = $data['transfusoesHoje'];
    $totalHospitais = $data['totalHospitais'];

} catch (PDOException $e) {
    die("Erro ao executar a consulta: " . $e->getMessage());
}

// Consulta para estoque crítico
$estoqueCritico = $pdo->query("
    SELECT 
        d.tipo_sanguineo, 
        COUNT(*) as total 
    FROM bolsas_sangue b
    JOIN dadores d ON b.id_dador = d.id
    WHERE b.estado = 'disponivel'
    GROUP BY d.tipo_sanguineo
    HAVING total < 5
    ORDER BY total ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>