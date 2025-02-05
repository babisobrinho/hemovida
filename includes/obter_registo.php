<?php

    include 'db_functions.php';

    $table = $_GET['table'] ?? null;
    $id = $_GET['id'] ?? null;

    if (!$table || !$id || !is_numeric($id)) {
        die("Erro: ID ou nome da tabela inválidos.");
    }

    $record = getRecordById($pdo, $table, (int)$id);

    if (!$record) {
        die("Erro: Registo não encontrado.");
    }

?>