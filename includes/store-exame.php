<?php
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_bolsa = $_POST['id_bolsa'] ?? null;
    $data = $_POST['data'] ?? null;
    $hemoglobina = $_POST['hemoglobina'] ?? null;
    $hepatite = $_POST['hepatite'] ?? null;
    $hiv = $_POST['hiv'] ?? null;
    $chagas = $_POST['chagas'] ?? null;
    $sifilis = $_POST['sifilis'] ?? null;
    $resultado = $_POST['resultado'] ?? null;

    try {
        $sql = "INSERT INTO exames (id_bolsa, data, hemoglobina, hepatite, hiv, chagas, sifilis, resultado) 
                VALUES (:id_bolsa, :data, :hemoglobina, :hepatite, :hiv, :chagas, :sifilis, :resultado)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_bolsa", $id_bolsa, PDO::PARAM_INT);
        $stmt->bindParam(":data", $data);
        $stmt->bindParam(":hemoglobina", $hemoglobina);
        $stmt->bindParam(":hepatite", $hepatite, PDO::PARAM_INT);
        $stmt->bindParam(":hiv", $hiv, PDO::PARAM_INT);
        $stmt->bindParam(":chagas", $chagas, PDO::PARAM_INT);
        $stmt->bindParam(":sifilis", $sifilis, PDO::PARAM_INT);
        $stmt->bindParam(":resultado", $resultado);
        
        $stmt->execute();
        
        $new_id = $pdo->lastInsertId();
        header("Location: ../exames.php?success=1&new_id=" . $new_id);
    } catch (PDOException $e) {
        header("Location: ../exames.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../exames.php");
}
exit();