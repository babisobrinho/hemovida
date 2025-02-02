<?php 

    $query = "
        SELECT 
            (SELECT COUNT(*) FROM doacoes WHERE DATE(doacoes.data) = CURDATE()) AS doacoesHoje,
            (SELECT COUNT(*) FROM dadores WHERE dadores.estado = 1) AS dadoresAtivos,
            (SELECT COALESCE(SUM(volume_ml), 0) FROM bolsas_sangue WHERE bolsas_sangue.estado = 'Disponível') AS totalSangueDisponivel_ml,
            (SELECT COUNT(*) FROM exames WHERE DATE(exames.data) = CURDATE()) AS examesHoje,
            (SELECT COUNT(*) FROM transfusoes WHERE DATE(transfusoes.data) = CURDATE()) AS transfusoesHoje,
            (SELECT COUNT(*) FROM hospitais) AS totalHospitais
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $doacoesHoje = $data['doacoesHoje'];
    $dadoresAtivos = $data['dadoresAtivos'];
    $totalSangueDisponivel = $data['totalSangueDisponivel_ml'] / 1000;
    $examesHoje = $data['examesHoje'];
    $transfusoesHoje = $data['transfusoesHoje'];
    $totalHospitais = $data['totalHospitais'];

?>