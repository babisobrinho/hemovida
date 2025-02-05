<?php

    include 'partials/header.php';
    include 'includes/db_functions.php';

    $dadoresAtivos = getActiveDadores($pdo)['dadoresAtivos'];

    $pageTitle = "Nova Doação";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Doações', 'url' => 'doacoes.php', 'active' => false],
        ['title' => 'Nova Doação', 'url' => '#', 'active' => true]
    ];
    
?>

    <div class="container p-4">
        <?php include 'partials/page-header.php'; ?>

        <div class="row d-flex align-items-center justify-content-center py-4">
            <div class="col-lg-6 col-md-9 col-12">
                <div class="card">
                    <form action="includes/store.php?table=doacoes" method="POST">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="dador">Dador</label>
                                    <select class="form-select" name="dador" id="dador" required>
                                        <option value="" disabled>Escolha um dador</option>
                                        <?php  foreach($dadoresAtivos as $dador): ?>
                                            <option value="<?php echo $dador['id']; ?>"><?php echo $dador['nome']; ?> (<?php echo$dador['n_utente']; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12 mb-3">
                                    <label class="form-label" for="data">Dia</label>
                                    <input type="date" class="form-control" name="data" id="data" required>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <label class="form-label" for="hora">Hora</label>
                                    <input type="time" class="form-control" name="hora" id="hora" required>
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <select class="form-select" name="estado" id="estado" data-live-search="true" required>
                                        <option value="" disabled>Escolha um estado</option>
                                        <option value="agendado">Agendado</option>
                                        <option value="concluido">Concluído</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-end gap-2 border-0 bg-white">
                            <a href="doacoes.php" class="btn btn-light border">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                Criar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date().toISOString().split("T")[0];
            document.getElementById("data").setAttribute("min", today);
        });
    </script>

<?php include 'partials/footer.php'; ?>