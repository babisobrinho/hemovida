<?php

    $dadorId = $_GET['id'] ?? null;
    
    if (!$dadorId) {
        die("Erro: ID da doação não fornecido.");
    }

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM dadores WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $dadorId, PDO::PARAM_INT);
        $stmt->execute();
        $dador = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dador) {
            die("Erro: Dador não encontrado.");
        }
    } catch (PDOException $e) {
        die("Erro ao buscar dador: " . $e->getMessage());
    }

?>