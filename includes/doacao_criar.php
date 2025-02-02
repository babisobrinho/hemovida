<?php

    include '../config/config.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $dador = $_POST["dador"] ?? null;
        $data = $_POST["data"] ?? null;
        $hora = $_POST["hora"] ?? null;
        $estado = $_POST["estado"] ?? null;

        if (!$dador || !$data || !$hora || !$estado) {
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

            $sql = "INSERT INTO doacoes (id_dador, data, hora, estado) VALUES (:dador, :data, :hora, :estado)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":dador", $dador, PDO::PARAM_INT);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":hora", $hora);
            $stmt->bindParam(":estado", $estado);
            $stmt->execute();

            header("Location: ../agenda.php?dataSelecionada=$data");
            exit;
        } catch (PDOException $e) {
            die("Erro ao criar doação: " . $e->getMessage());
        }
    } else {
        die("Acesso inválido.");
    }
    
?>