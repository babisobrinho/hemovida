<?php

    include 'db_connection.php';

    $dataSelecionada = isset($_GET['dataSelecionada']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['dataSelecionada']) 
        ? $_GET['dataSelecionada'] 
        : date('Y-m-d');

    try {

        $query = "
            SELECT doacoes.*, dadores.nome, dadores.n_utente, dadores.data_nascimento, dadores.tipo_sanguineo, dadores.peso, dadores.sexo
            FROM doacoes
            INNER JOIN dadores ON doacoes.id_dador = dadores.id 
            WHERE doacoes.data = :dataSelecionada
            ORDER BY doacoes.hora ASC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':dataSelecionada', $dataSelecionada, PDO::PARAM_STR);
        $stmt->execute();

        $doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erro ao buscar doações: " . $e->getMessage());
        $doacoes = [];
    }

?>