<?php

    include '../config/config.php';

    if (isset($_GET['id'])) {

        $doacaoId = $_GET['id'];

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "DELETE FROM doacoes WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $doacaoId, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: ../agenda.php?success=doacao_deleted');
            exit;
        } catch (PDOException $e) {
            die('Erro ao excluir doação: ' . $e->getMessage());
        }
    } else {
        header('Location: ../agenda.php?error=no_id');
        exit;
    }

?>