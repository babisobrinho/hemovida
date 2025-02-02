<?php 

    $query_contagens = "
        SELECT 
            SUM(tipo_sanguineo = 'A+') AS countAPositivo,
            SUM(tipo_sanguineo = 'A-') AS countANegativo,
            SUM(tipo_sanguineo = 'B+') AS countBPositivo,
            SUM(tipo_sanguineo = 'B-') AS countBNegativo,
            SUM(tipo_sanguineo = 'AB+') AS countABPositivo,
            SUM(tipo_sanguineo = 'AB-') AS countABNegativo,
            SUM(tipo_sanguineo = 'O+') AS countOPositivo,
            SUM(tipo_sanguineo = 'O-') AS countONegativo,
            SUM(sexo = 'Feminino') AS countFeminino,
            SUM(sexo = 'Masculino') AS countMasculino,
            SUM(estado = 1) AS countAtivo,
            SUM(estado = 0) AS countInativo
        FROM dadores
    ";

    $stmt = $conn->prepare($query_contagens);
    $stmt->execute();
    $result = $stmt->get_result();
    $contagens = $result->fetch_assoc();

    $countAPositivo = $contagens['countAPositivo'];
    $countANegativo = $contagens['countANegativo'];
    $countBPositivo = $contagens['countBPositivo'];
    $countBNegativo = $contagens['countBNegativo'];
    $countABPositivo = $contagens['countABPositivo'];
    $countABNegativo = $contagens['countABNegativo'];
    $countOPositivo = $contagens['countOPositivo'];
    $countONegativo = $contagens['countONegativo'];
    $countFeminino = $contagens['countFeminino'];
    $countMasculino = $contagens['countMasculino'];
    $countAtivo = $contagens['countAtivo'];
    $countInativo = $contagens['countInativo'];

    $filtros = [
        'tipo_sanguineo' => [],
        'sexo' => [],
        'estado' => []
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        foreach (['a-positivo' => 'A+', 'a-negativo' => 'A-', 'b-positivo' => 'B+', 'b-negativo' => 'B-', 
                'ab-positivo' => 'AB+', 'ab-negativo' => 'AB-', 'o-positivo' => 'O+', 'o-negativo' => 'O-'] as $key => $value) {
            if (isset($_GET[$key])) $filtros['tipo_sanguineo'][] = $value;
        }

        foreach (['feminino' => 'Feminino', 'masculino' => 'Masculino'] as $key => $value) {
            if (isset($_GET[$key])) $filtros['sexo'][] = $value;
        }

        foreach (['ativo' => 1, 'inativo' => 0] as $key => $value) {
            if (isset($_GET[$key])) $filtros['estado'][] = $value;
        }
    }

    $query_filtro_dadores = "SELECT * FROM dadores WHERE 1=1";

    if (!empty($filtros['tipo_sanguineo'])) {
        $placeholders = implode(',', array_fill(0, count($filtros['tipo_sanguineo']), '?'));
        $query_filtro_dadores .= " AND tipo_sanguineo IN ($placeholders)";
    }

    if (!empty($filtros['sexo'])) {
        $placeholders = implode(',', array_fill(0, count($filtros['sexo']), '?'));
        $query_filtro_dadores .= " AND sexo IN ($placeholders)";
    }

    if (!empty($filtros['estado'])) {
        $placeholders = implode(',', array_fill(0, count($filtros['estado']), '?'));
        $query_filtro_dadores .= " AND estado IN ($placeholders)";
    }

    $query_filtro_dadores .= " ORDER BY nome ASC";

    $stmt = $conn->prepare($query_filtro_dadores);
    $types = str_repeat('s', count($filtros['tipo_sanguineo'])) . str_repeat('s', count($filtros['sexo'])) . str_repeat('i', count($filtros['estado']));
    $params = array_merge($filtros['tipo_sanguineo'], $filtros['sexo'], $filtros['estado']);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $resultFiltroDadores = $stmt->get_result();
    $dadores = $resultFiltroDadores->fetch_all(MYSQLI_ASSOC);

?>