<?php
require_once 'includes/db_connection.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID da campanha não fornecido");
}

try {
    // Verificar se a campanha existe
    $checkQuery = "SELECT id FROM campanhas WHERE id = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$id]);
    
    if (!$stmt->fetch()) {
        die("Campanha não encontrada");
    }

    // Excluir a campanha
    $deleteQuery = "DELETE FROM campanhas WHERE id = ?";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute([$id]);
    
    header("Location: campanhas.php?deleted=1");
    exit();
    
} catch (PDOException $e) {
    die("Erro ao excluir campanha: " . $e->getMessage());
}