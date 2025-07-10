<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hemovida";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($id)) {
 
    $bolsaQuery = "SELECT id_bolsa FROM transfusoes WHERE id = ?";
    $stmt = $conn->prepare($bolsaQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transfusao = $result->fetch_assoc();
    $stmt->close();

    if ($transfusao) {
        $id_bolsa = $transfusao['id_bolsa'];

      
        $deleteTransfusaoQuery = "DELETE FROM transfusoes WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteTransfusaoQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
           
            $updateBolsaQuery = "UPDATE bolsas_sangue SET estado = 'Disponível' WHERE id = ?";
            $updateStmt = $conn->prepare($updateBolsaQuery);
            $updateStmt->bind_param("i", $id_bolsa);
            $updateStmt->execute();
            $updateStmt->close();

            header( "Location: transfusoes.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Erro ao excluir transfusão.</div>";
        }

        $deleteStmt->close();
    } else {
        echo "<div class='alert alert-danger'>Transfusão não encontrada.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID da transfusão não fornecido.</div>";
}

include 'partials/footer.php';
?>