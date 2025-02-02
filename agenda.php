<?php
    include 'partials/header.php';
    include 'includes/agenda_escolher_data.php';

    $pageTitle = "Agenda de Doações";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Agenda', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>

    <div class="d-flex align-content-center justify-content-between">
        <div>
            <a href="agenda-criar.php" class="btn text-white" style="background-color: #202d3b;">
                <i class="fa-solid fa-plus"></i> Nova Doação
            </a>
        </div>
        <form action="" method="GET">
            <div class="input-group" style="width: 300px;">
                <input type="date" class="form-control" name="dataSelecionada" id="dataSelecionada" value="<?php echo $dataSelecionada; ?>" required>
                <button class="btn text-white" style="background-color: #202d3b;" type="submit">Escolher Data</button>
            </div>           
        </form>
    </div>

    <div class="container py-2 mt-4 mb-4">
        <?php if(count($doacoes) > 0): ?>
            <?php
                
                foreach($doacoes as $index => $doacao):

                include 'includes/agenda_info.php'

            ?>
                <div class="row">
                    <div class="col-auto text-center flex-column d-none d-sm-flex">
                        <div class="row h-50">
                            <?php if($index === count($doacoes) - 1 && $index > 1): ?>
                                <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php elseif($index === 0): ?>
                            <?php else: ?>
                                <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php endif; ?>
                        </div>
                        <h5 class="m-2">
                            <span class="badge shadow-sm" style="background-color: #202d3b; border-color: #202d3b;">&nbsp;</span>
                        </h5>
                        <div class="row h-50">
                            <?php if($index === count($doacoes) - 1): ?>
                            <?php elseif($index === 0): ?>
                                <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php else: ?>
                                <div class="col" style="border-right-style: solid; border-color: 202d3;">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col py-2">
                        <div class="card shadow">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title <?php echo $isPastDoacao ? 'text-muted' : 'text-dark '; ?> m-0"><span><?php echo date('H:i', strtotime($doacao['hora'])); ?></span> - <?php echo $doacao['nome']; ?></h4>
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="agenda-editar.php?id=<?php echo $doacao['id']; ?>" class="lh-1 text-decoration-none" style="color: #202d3b;">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </a>
                                    <a href="#" class="lh-1" data-bs-toggle="modal" data-bs-target="#deleteModalDoacao" data-doacao-id="<?php echo $doacao['id']; ?>" class="text-dark">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <p>
                                        <?php
                                            switch($doacao['estado']) {
                                                case "Concluído":
                                                    echo '<span class="float-right badge bg-success">';
                                                    echo $doacao['estado'];
                                                    echo '</span>';
                                                    break;
                                                case "Cancelado":
                                                    echo '<span class="float-right badge bg-danger">';
                                                    echo $doacao['estado'];
                                                    echo '</span>';
                                                    break;
                                                case "Agendado":
                                                default:
                                                    echo '<span class="float-right badge" style="background-color: #202d3b;">';
                                                    echo $doacao['estado'];
                                                    echo '</span>';
                                                    break;
                                            }
                                        ?>
                                    </p>
                                </div>
                                <div class="row <?php echo $isPastDoacao ? 'text-muted' : 'text-dark '; ?>">
                                    <div class="col-3">
                                        <p class="m-0"><b>Número de Utente:</b> <?php echo $doacao['n_utente']; ?></p>
                                        <p class="m-0"><b>Tipo Sanguíneo:</b> <?php echo $doacao['tipo_sanguineo']; ?></p>
                                        <p class="m-0">
                                            <b>Última Doação:</b> <?php echo $data_ultima_doacao = $ultima_doacao ? $ultima_doacao : "Nenhuma doação anterior"; ?>
                                        </p>
                                    </div>
                                    <div class="col-3">
                                        <p class="m-0"><b>Idade:</b> <?php echo $idade; ?> anos</p>
                                        <p class="m-0"><b>Sexo:</b> <?php echo $doacao['sexo']; ?></p>
                                        <p class="m-0"><b>Peso:</b> <?php echo $doacao['peso']; ?>kg</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="m-0">
                                            <b>Observações:</b> <?php echo isset($doacao['observacoes']) ? $doacao['observacoes'] : "Sem observações."; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma doação agendada para <?php echo date('d/m/Y', strtotime($dataSelecionada)); ?>.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Remover Doação -->
<div class="modal fade" id="deleteModalDoacao" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem a certeza de que deseja remover esta marcação?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteConfirmButtonDoacao" href="#" class="btn btn-danger">Remover</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.lh-1[data-bs-toggle="modal"]').forEach(function (deleteButton) {
        deleteButton.addEventListener('click', function () {
            const doacaoId = this.getAttribute('data-doacao-id');
            
            const deleteConfirmButton = document.getElementById('deleteConfirmButtonDoacao');
            deleteConfirmButton.setAttribute('href', 'includes/doacao_remover.php?id=' + doacaoId);
        });
    });
</script>

<?php include 'partials/footer.php'; ?>