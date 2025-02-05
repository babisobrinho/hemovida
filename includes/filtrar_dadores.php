<?php

    include 'db_connection.php';
    include 'db_functions.php';

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

    $resultado = getFilteredDadores($pdo, $filtros);

    $contagens = $resultado['contagens'];
    $dadores = $resultado['dadores'];

?>
