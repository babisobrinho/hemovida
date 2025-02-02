<?php

    include '../config/config.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = $_POST["id"] ?? null;
        $dador = $_POST["dador"] ?? null;
        $data = $_POST["data"] ?? null;
        $hora = $_POST["hora"] ?? null;
        $estado = $_POST["estado"] ?? null;

        if (!$id || !$dador || !$data || !$hora || !$estado) {
            die("Erro: Todos os campos são obrigatórios.");
        }

        $today = date("Y-m-d");

        if ($data < $today) {
            die("Erro: A data não pode ser no passado.");
        }

        switch($estado) {
            case "concluido":
                $estado = "Concluído";
                break;
            case "cancelado":
                $estado = "Cancelado";
                break;
            case "agendado":
            default:
                $estado = "Agendado";
                break;
        }

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE doacoes SET id_dador = :dador, data = :data, hora = :hora, estado = :estado WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":dador", $dador, PDO::PARAM_INT);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":hora", $hora);
            $stmt->bindParam(":estado", $estado);
            $stmt->execute();

            header("Location: ../agenda.php?dataSelecionada=$data");
            exit;
        } catch (PDOException $e) {
            die("Erro ao atualizar doação: " . $e->getMessage());
        }
    } else {
        die("Acesso inválido.");
    }

?>