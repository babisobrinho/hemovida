<?php include 'includes/header.php'; ?>

<?php
    $query = "SELECT doacoes.*, dadores.nome, dadores.n_utente, dadores.data_nascimento, dadores.tipo_sanguineo, dadores.peso, dadores.sexo
            FROM doacoes
            JOIN dadores ON doacoes.id_dador = dadores.id 
            WHERE DATE(doacoes.data) = CURDATE() 
            ORDER BY doacoes.hora ASC";

    $result = $conn->query($query);
    
    $doacoes = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container p-4">
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Agenda de Doações</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-dark text-decoration-none">Páginas</a></li>
                    <li class="breadcrumb-item text-danger fw-semibold" aria-current="page">Agenda de Doações</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-2 mt-4 mb-4">
        <?php if(count($doacoes) > 0): ?>
            <?php foreach($doacoes as $index => $doacao):

                $dataNascimento = new DateTime($doacao['data_nascimento']);
                $hoje = new DateTime();
                $idade = $hoje->diff($dataNascimento)->y;

                $id_dador = $doacao['id_dador']; // ID do doador atual
                $sql_ultima_doacao = "SELECT data 
                                    FROM doacoes 
                                    WHERE id_dador = ? AND data < CURDATE() 
                                    ORDER BY data DESC 
                                    LIMIT 1";

                $stmt = $conn->prepare($sql_ultima_doacao);
                $stmt->bind_param("i", $id_dador);
                $stmt->execute();
                $result_ultima = $stmt->get_result();
                $ultima_doacao = $result_ultima->fetch_assoc();
            ?>
                <div class="row">
                    <div class="col-auto text-center flex-column d-none d-sm-flex">
                        <div class="row h-50">
                            <?php if($index === count($doacoes) - 1): ?>
                                <div class="col border-end border-dark">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php elseif($index === 0): ?>
                            <?php else: ?>
                                <div class="col border-end border-dark">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php endif; ?>
                        </div>
                        <h5 class="m-2">
                            <span class="badge bg-dark border shadow-sm">&nbsp;</span>
                        </h5>
                        <div class="row h-50">
                            <?php if($index === count($doacoes) - 1): ?>
                            <?php elseif($index === 0): ?>
                                <div class="col border-end border-dark">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php else: ?>
                                <div class="col border-end border-dark">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col py-2">
                        <div class="card shadow">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title text-dark m-0"><span><?php echo date('H:i', strtotime($doacao['hora'])); ?></span> - <?php echo htmlspecialchars($doacao['nome']); ?></h4>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn lh-1 btn-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="14" width="14" viewBox="0 0 512 512"><!-- Font Awesome icon --><path fill="#ffffff" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg>
                                    </button>
                                    <button class="btn lh-1 btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="14" width="12.25" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <p>
                                        <?php
                                        
                                            switch($doacao['estado']) {
                                                case "Concluído":
                                                    echo '<span class="float-right badge bg-success">';
                                                    echo htmlspecialchars($doacao['estado']);
                                                    echo '</span>';
                                                    break;
                                                case "Cancelado":
                                                    echo '<span class="float-right badge bg-danger">';
                                                    echo htmlspecialchars($doacao['estado']);
                                                    echo '</span>';
                                                    break;
                                                case "Agendado":
                                                default:
                                                    echo '<span class="float-right badge bg-secondary">';
                                                    echo htmlspecialchars($doacao['estado']);
                                                    echo '</span>';
                                                    break;
                                            }
                                        
                                        ?>
                                    </span></p>
                                </div>
                                <div class="row text-dark">
                                    <div class="col-3">
                                        <p class="m-0"><b>Número de Utente:</b> <?php echo htmlspecialchars($doacao['n_utente']); ?></p>
                                        <p class="m-0"><b>Tipo Sanguíneo:</b> <?php echo htmlspecialchars($doacao['tipo_sanguineo']); ?></p>
                                        <p class="m-0">
                                            <b>Última Doação:</b> <?php echo $data_ultima_doacao = $ultima_doacao ? date('d/m/Y', strtotime($ultima_doacao['data'])) : "Nenhuma doação anterior"; ?>
                                            </p>
                                    </div>
                                    <div class="col-3">
                                        <p class="m-0"><b>Idade:</b> <?php echo $idade; ?> anos</p>
                                        <p class="m-0"><b>Sexo:</b> <?php echo htmlspecialchars($doacao['sexo']); ?></p>
                                        <p class="m-0"><b>Peso:</b> <?php echo htmlspecialchars($doacao['peso']); ?>kg</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="m-0">
                                            <b>Observações:</b> <?php echo htmlspecialchars(isset($doacao['observacoes']) ? $doacao['observacoes'] : "Sem observações."); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma doação agendada para hoje.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>