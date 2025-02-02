<?php 

    $query_dadores_ativos = "SELECT dadores.id, dadores.nome, dadores.n_utente FROM dadores WHERE dadores.estado = 1 ORDER BY dadores.nome ASC";

    $stmt = $conn->prepare($query_dadores_ativos);
    $stmt->execute();
    $dadoresAtivos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>