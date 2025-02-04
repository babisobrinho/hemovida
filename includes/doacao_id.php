<?php

    $doacaoId = $_GET['id'] ?? null;
    
    if (!$doacaoId) {
        die("Erro: ID da doação não fornecido.");
    }

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM doacoes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $doacaoId, PDO::PARAM_INT);
        $stmt->execute();
        $doacao = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doacao) {
            die("Erro: Doação não encontrada.");
        }
    } catch (PDOException $e) {
        die("Erro ao buscar doação: " . $e->getMessage());
    }

?>