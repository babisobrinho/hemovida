<?php
require_once 'db_connection.php';

$meses = isset($_GET['meses']) ? (int)$_GET['meses'] : 6;

// Obter dados de doações dos últimos X meses
$doacoes = $pdo->query("
    SELECT 
        d.tipo_sanguineo,
        COUNT(*) as total
    FROM doacoes do
    JOIN dadores d ON do.id_dador = d.id
    WHERE do.data >= DATE_SUB(CURDATE(), INTERVAL $meses MONTH)
    GROUP BY d.tipo_sanguineo
    ORDER BY d.tipo_sanguineo
")->fetchAll(PDO::FETCH_ASSOC);

// Obter dados de transfusões dos últimos X meses
$transfusoes = $pdo->query("
    SELECT 
        d.tipo_sanguineo,
        COUNT(*) as total
    FROM transfusoes t
    JOIN bolsas_sangue b ON t.id_bolsa = b.id
    JOIN dadores d ON b.id_dador = d.id
    WHERE t.data >= DATE_SUB(CURDATE(), INTERVAL $meses MONTH)
    GROUP BY d.tipo_sanguineo
    ORDER BY d.tipo_sanguineo
")->fetchAll(PDO::FETCH_ASSOC);

// Preparar dados no formato que o gráfico precisa
$tiposSanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$doacoesData = array_fill_keys($tiposSanguineos, 0);
$transfusoesData = array_fill_keys($tiposSanguineos, 0);

foreach ($doacoes as $item) {
    if (isset($doacoesData[$item['tipo_sanguineo']])) {
        $doacoesData[$item['tipo_sanguineo']] = (int)$item['total'];
    }
}

foreach ($transfusoes as $item) {
    if (isset($transfusoesData[$item['tipo_sanguineo']])) {
        $transfusoesData[$item['tipo_sanguineo']] = (int)$item['total'];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'labels' => $tiposSanguineos,
    'doacoes' => array_values($doacoesData),
    'transfusoes' => array_values($transfusoesData)
]);
?>