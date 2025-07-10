<?php
require_once 'includes/db_connection.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: exames.php?error=ID não fornecido");
    exit();
}

try {
    $sql = "DELETE FROM exames WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header("Location: exames.php?deleted=" . $id);
    } else {
        header("Location: exames.php?error=Erro ao excluir exame");
    }
} catch (PDOException $e) {
    header("Location: exames.php?error=" . urlencode($e->getMessage()));
}
exit();