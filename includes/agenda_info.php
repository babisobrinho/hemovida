<?php

    $idade = date_diff(date_create($doacao['data_nascimento']), date_create($dataSelecionada))->y;

    $sql_ultima_doacao = "
        SELECT COALESCE(MAX(data), NULL) AS ultima_data 
        FROM doacoes 
        WHERE id_dador = ? AND data < ?
    ";

    $stmt = $conn->prepare($sql_ultima_doacao);
    $stmt->bind_param("is", $doacao['id_dador'], $dataSelecionada);
    $stmt->execute();
    $ultima_doacao = $stmt->get_result()->fetch_column();

    $doacaoDateTime = $doacao['data'] . ' ' . $doacao['hora'];
    $doacaoDateTimeObj = new DateTime($doacaoDateTime);
    $currentDateTime = new DateTime();

    $isPastDoacao = $doacaoDateTimeObj < $currentDateTime;

?>