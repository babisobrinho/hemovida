<?php

    include '../config/config.php';

    if (isset($_GET['id'])) {

        $dadorId = $_GET['id'];

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "DELETE FROM dadores WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $dadorId, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: ../dadores.php?success=dador_deleted');
            exit;
        } catch (PDOException $e) {
            die('Erro ao excluir doação: ' . $e->getMessage());
        }
    } else {
        header('Location: ../dadores.php?error=no_id');
        exit;
    }

?>