<?php

    include 'partials/header.php';

    $pageTitle = "Novo Dador";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Dadores', 'url' => 'dadores.php', 'active' => false],
        ['title' => 'Novo Dador', 'url' => '#', 'active' => true]
    ];
    
?>

    <div class="container p-4">
        <?php include 'partials/page-header.php'; ?>
        <div class="row d-flex align-items-center justify-content-center py-4">
            <div class="col-xl-6 col-md-10 col-12">
                <div class="card shadow">
                    <form action="includes/store.php" method="POST">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="nome">Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="email">E-mail</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-12 mb-3">
                                    <label class="form-label" for="n_utente">Nº Utente</label>
                                    <input type="text" class="form-control" name="n_utente" id="n_utente" pattern="^\d{9}$" title="Nº Utente deve ter exatamente 9 dígitos" required>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 mb-3">
                                    <label class="form-label" for="data_nascimento">Data de Nascimento</label>
                                    <input type="date" class="form-control" name="data_nascimento" id="data_nascimento" required>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 mb-3">
                                    <label class="form-label" for="tipo_sanguineo">Tipo Sanguíneo</label>
                                    <select class="form-select" name="tipo_sanguineo" id="tipo_sanguineo" required>
                                        <option value="" disabled>Escolha um tipo sanguíneo</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 mb-3">
                                    <label class="form-label" for="peso">Peso</label>
                                    <input type="number" class="form-control" name="peso" id="peso" min="50" step="any" required>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <select class="form-select" name="sexo" id="sexo" data-live-search="true" required>
                                        <option value="" disabled>Escolha um sexo</option>
                                        <option value="Feminino">Feminino</option>
                                        <option value="Feminino">Feminino</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <select class="form-select" name="estado" id="estado" data-live-search="true" required>
                                        <option value="" disabled>Escolha um estado</option>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                                <input type="hidden" type="date" name="data_inscricao" id="data_inscricao" value="<?php echo date('Y-m-d'); ?>">
                                <input type="hidden" name="table" value="dadores">
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-end gap-2 border-0 bg-white">
                            <a href="dadores.php" class="btn btn-light border">
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
            let today = new Date();
            let eighteenYearsAgo = new Date(today.setFullYear(today.getFullYear() - 18)).toISOString().split("T")[0];
            document.getElementById("data_nascimento").setAttribute("max", eighteenYearsAgo);

            const form = document.querySelector('form');
            const nUtenteField = document.getElementById('n_utente');

            form.addEventListener('submit', function(event) {
                const isValid = nUtenteField.checkValidity();
                
                if (!isValid) {
                    alert("O Nº Utente deve ter exatamente 9 dígitos.");
                    event.preventDefault();
                }
            });
        });
    </script>

<?php include 'partials/footer.php'; ?>