<?php

    include 'partials/header.php';
    include 'includes/obter_registo.php';

    $doacao = $record;
    $dadoresAtivos = getActiveDadores($pdo)['dadoresAtivos'];

    $pageTitle = "Editar Doação";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Agenda', 'url' => 'doacoes.php', 'active' => false],
        ['title' => 'Editar Doação', 'url' => '#', 'active' => true]
    ];
    
?>

    <div class="container p-4">
        <?php include 'partials/page-header.php'; ?>

        <div class="row d-flex align-items-center justify-content-center py-4">
            <div class="col-lg-6 col-md-9 col-12">
                <div class="card">
                    <form action="includes/update.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $doacao['id']; ?>">
                        <input type="hidden" name="table" value="doacoes">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="dador">Dador</label>
                                    <select class="form-select" name="dador" id="dador" required>
                                        <option value="" disabled>Escolha um dador</option>
                                        <?php foreach ($dadoresAtivos as $dador): ?>
                                            <option value="<?php echo $dador['id']; ?>" <?php echo $dador['id'] == $doacao['id_dador'] ? 'selected' : ''; ?>>
                                                <?php echo $dador['nome']; ?> (<?php echo $dador['n_utente']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12 mb-3">
                                    <label class="form-label" for="data">Dia</label>
                                    <input type="date" class="form-control" name="data" id="data" value="<?php echo $doacao['data']; ?>" required>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <label class="form-label" for="hora">Hora</label>
                                    <input type="time" class="form-control" name="hora" id="hora" value="<?php echo $doacao['hora']; ?>" required>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <select class="form-select" name="estado" id="estado" data-live-search="true" required>
                                        <option value="agendado" <?php echo $doacao['estado'] == 'Agendado' ? 'selected' : ''; ?>>Agendado</option>
                                        <option value="em_atendimento" <?php echo $doacao['estado'] == 'Agendado' ? 'selected' : ''; ?>>Em Atendimento</option>
                                        <option value="concluido" <?php echo $doacao['estado'] == 'Concluído' ? 'selected' : ''; ?>>Concluído</option>
                                        <option value="cancelado" <?php echo $doacao['estado'] == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-end gap-2 border-0 bg-white">
                            <a href="doacoes.php" class="btn btn-light border">Cancelar</a>
                            <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include 'partials/footer.php'; ?>