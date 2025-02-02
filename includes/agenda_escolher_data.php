<?php

    $dataSelecionada = isset($_GET['dataSelecionada']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['dataSelecionada']) 
        ? $_GET['dataSelecionada'] 
        : date('Y-m-d');

    $query = "
        SELECT 
            doacoes.*, 
            dadores.nome, 
            dadores.n_utente, 
            dadores.data_nascimento, 
            dadores.tipo_sanguineo, 
            dadores.peso, 
            dadores.sexo
        FROM doacoes
        INNER JOIN dadores ON doacoes.id_dador = dadores.id 
        WHERE doacoes.data = ? 
        ORDER BY doacoes.hora ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $dataSelecionada);
    $stmt->execute();
    $doacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>