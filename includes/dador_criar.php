<?php

    include '../config/config.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $n_utente = $_POST['n_utente'];
        $data_nascimento = $_POST['data_nascimento'];
        $tipo_sanguineo = $_POST['tipo_sanguineo'];
        $peso = $_POST['peso'];
        $sexo = $_POST['sexo'];
        $estado = $_POST['estado'];
        $data_inscricao = $_POST['data_inscricao'];

        if ($peso < 50) {
            die("O peso deve ser igual ou maior que 50kg.");
        }

        if (!preg_match("/^\d{9}$/", $n_utente)) {
            die("Nº Utente deve ser exatamente 9 dígitos.");
        }

        $today = new DateTime();
        $dob = new DateTime($data_nascimento);
        $age = $today->diff($dob)->y;
        if ($age < 18) {
            die("O dador deve ter pelo menos 18 anos.");
        }

        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO dadores (nome, email, n_utente, data_nascimento, tipo_sanguineo, peso, sexo, estado, data_inscricao)
                    VALUES (:nome, :email, :n_utente, :data_nascimento, :tipo_sanguineo, :peso, :sexo, :estado, :data_inscricao)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":n_utente", $n_utente);
            $stmt->bindParam(":data_nascimento", $data_nascimento);
            $stmt->bindParam(":tipo_sanguineo", $tipo_sanguineo);
            $stmt->bindParam(":peso", $peso);
            $stmt->bindParam(":sexo", $sexo);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":data_inscricao", $data_inscricao);

            $stmt->execute();

            header("Location: ../dadores.php");
            exit;

        } catch (PDOException $e) {
            die("Erro ao criar dador: " . $e->getMessage());
        }
    }

?>
