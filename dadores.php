<?php

    include 'partials/header.php';
    include 'includes/filtrar_dadores.php';

    $pageTitle = "Lista de Dadores";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Dadores', 'url' => '#', 'active' => true]
    ];

?>

    <div class="container p-4">
        <?php include 'partials/page-header.php'; ?>
        <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
            <a href="dador-criar.php" class="btn text-white" style="background-color: #202d3b;">
                <i class="fa-solid fa-plus"></i> Novo
            </a>
            <a href="" data-bs-toggle="modal" data-bs-target="#filterModalDador" data-dador-id="<?php echo $dador['id']; ?>" class="btn text-white d-none d-md-block" style="background-color: #202d3b;">
                <i class="fa-solid fa-filter"></i> Filtrar
            </a>
            <a href="dadores.php" class="btn btn-danger">
                <i class="fa-solid fa-filter-circle-xmark"></i> Remover Filtros 
            </a>
        </div>
        <div class="accordion py-2 d-block d-md-none mb-2" id="accordionFiltro">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button text-dark collapsed" style="background-color: #f1f1f1;" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltro" aria-expanded="false" aria-controls="collapseFiltro">
                        Aplicar Filtros
                    </button>
                </h2>
                <div id="collapseFiltro" class="accordion-collapse collapse" data-bs-parent="#accordionFiltro">
                    <div class="accordion-body">
                        <form action="">
                            <div class="card border-0 p-0 m-0">
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <p class="fw-semibold mb-0 text-muted">Tipo Sanguíneo</p>
                                            <div class="py-3 pb-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" name="a-positivo" id="a-positivo" <?php echo (isset($_GET['a-positivo']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label form-checked-danger" for="a-positivo">
                                                        A +
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAPositivo']; ?></span>
                                                </div>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="a-negativo" id="a-negativo" <?php echo (isset($_GET['a-negativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="a-negativo">
                                                    A -
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countANegativo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="b-positivo" id="b-positivo" <?php echo (isset($_GET['b-positivo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="b-positivo">
                                                    B +
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBPositivo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="b-negativo" id="b-negativo" <?php echo (isset($_GET['b-negativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="b-negativo">
                                                    B -
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBNegativo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="ab-positivo" id="ab-positivo" <?php echo (isset($_GET['ab-positivo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="ab-positivo">
                                                    AB +
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABPositivo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="ab-negativo" id="ab-negativo" <?php echo (isset($_GET['ab-negativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="ab-negativo">
                                                    AB -
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABNegativo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="o-positivo" id="o-positivo" <?php echo (isset($_GET['o-positivo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="o-positivo">
                                                    O +
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countOPositivo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="o-negativo" id="o-negativo" <?php echo (isset($_GET['o-negativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="o-negativo">
                                                    O -
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countONegativo']; ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <p class="fw-semibold mb-0 text-muted">Sexo</p>
                                            <div class="py-3 pb-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" name="feminino" id="feminino" <?php echo (isset($_GET['feminino']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="feminino">
                                                        Feminino
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end"><?php echo $contagens['countFeminino']; ?></span>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" name="masculino" id="masculino" <?php echo (isset($_GET['masculino']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="masculino">
                                                        Masculino
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end"><?php echo $contagens['countMasculino']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <p class="fw-semibold mb-0 text-muted">Estado</p>
                                            <div class="py-3 pb-0">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" name="ativo" id="ativo" <?php echo (isset($_GET['ativo']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="ativo">
                                                        Ativo
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAtivo']; ?></span>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" name="inativo" id="inativo" <?php echo (isset($_GET['inativo']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="inativo">
                                                        Inativo
                                                    </label>
                                                    <span class="badge bg-light text-muted float-end"><?php echo $contagens['countInativo']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-end mt-3 gap-2">
                                    <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                                        <i class="fa-solid fa-filter"></i> Filtrar 
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php if($filtros['tipo_sanguineo'] || $filtros['sexo'] || $filtros['estado']): ?>
            <div class="d-flex align-items-center justify-content-start gap-1">
                <p class="m-0 p-0">Filtros:</p>
                <?php foreach($filtros['tipo_sanguineo'] as $tipo_sanguineo): ?>
                    <div class="badge bg-light text-dark border">
                        <?php echo $tipo_sanguineo; ?>
                    </div>
                <?php endforeach; ?>
                <?php foreach($filtros['sexo'] as $sexo): ?>
                    <div class="badge bg-light text-dark border">
                        <?php echo $sexo; ?>
                    </div>
                <?php endforeach; ?>
                <?php foreach($filtros['estado'] as $estado): ?>
                    <div class="badge bg-light text-dark border">
                        <?php echo $estado == 1 ? 'Ativo' : 'Inativo'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="table-responsive py-2">
            <table class="table text-nowrap table-hover">
                <caption>Total de Dadores: <?php echo count($dadores); ?></caption>
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Nº Utente</th>
                        <th scope="col">Data de Nascimento</th>
                        <th scope="col">Tipo Sanguíneo</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Peso</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Data da Inscrição</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php foreach($dadores as $dador): ?>
                        <tr>
                            <th scope="row"><?php echo $dador['nome']; ?></th>
                            <td><?php echo $dador['email']; ?></td>
                            <td><?php echo $dador['n_utente']; ?></td>
                            <td><?php echo $dador['data_nascimento']; ?></td>
                            <td><?php echo $dador['tipo_sanguineo']; ?></td>
                            <td><?php echo $dador['sexo']; ?></td>
                            <td><?php echo $dador['peso']; ?>kg</td>
                            <td>
                                <span class="badge <?php echo $dador['estado'] === 0 ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo $dador['estado'] === 0 ? 'Inativo' : 'Ativo'; ?>
                                </span>
                            </td>
                            <td><?php echo $dador['data_inscricao']; ?></td>
                            <td >
                                <div class="d-flex gap-2">
                                    <a href="dador-editar.php?table=dadores&id=<?php echo $dador['id']; ?>" class="text-decoration-none" style="color: #202d3b;">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </a>
                                    <a href="" data-bs-toggle="modal" data-bs-target="#deleteModalDador" data-dador-id="<?php echo $dador['id']; ?>" class="text-dark">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Filtrar Dador -->
    <div class="modal fade" id="filterModalDador" tabindex="-1" aria-labelledby="filterModalLabelDador" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabelDador">Filtrar Dadores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="card border-0 p-0 m-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Tipo Sanguíneo</p>
                                        <div class="py-3 pb-0">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="a-positivo" id="a-positivo-modal" <?php echo (isset($_GET['a-positivo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label form-checked-danger" for="a-positivo-modal">
                                                    A +
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAPositivo']; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="a-negativo" id="a-negativo-modal" <?php echo (isset($_GET['a-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="a-negativo-modal">
                                                A -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countANegativo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="b-positivo" id="b-positivo-modal" <?php echo (isset($_GET['b-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="b-positivo-modal">
                                                B +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBPositivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="b-negativo" id="b-negativo-modal" <?php echo (isset($_GET['b-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="b-negativo-modal">
                                                B -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countBNegativo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="ab-positivo" id="ab-positivo-modal" <?php echo (isset($_GET['ab-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ab-positivo-modal">
                                                AB +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABPositivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="ab-negativo" id="ab-negativo-modal" <?php echo (isset($_GET['ab-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="ab-negativo-modal">
                                                AB -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countABNegativo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="o-positivo" id="o-positivo-modal" <?php echo (isset($_GET['o-positivo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="o-positivo-modal">
                                                O +
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countOPositivo']; ?></span>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" name="o-negativo" id="o-negativo-modal" <?php echo (isset($_GET['o-negativo']) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="o-negativo-modal">
                                                O -
                                            </label>
                                            <span class="badge bg-light text-muted float-end"><?php echo $contagens['countONegativo']; ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Sexo</p>
                                        <div class="py-3 pb-0">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="feminino" id="feminino-modal" <?php echo (isset($_GET['feminino']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="feminino-modal">
                                                    Feminino
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countFeminino']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="masculino" id="masculino-modal" <?php echo (isset($_GET['masculino']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="masculino-modal">
                                                    Masculino
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countMasculino']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <p class="fw-semibold mb-0 text-muted">Estado</p>
                                        <div class="py-3 pb-0">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="ativo" id="ativo-modal" <?php echo (isset($_GET['ativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="ativo-modal">
                                                    Ativo
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countAtivo']; ?></span>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="" name="inativo" id="inativo-modal" <?php echo (isset($_GET['inativo']) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="inativo-modal">
                                                    Inativo
                                                </label>
                                                <span class="badge bg-light text-muted float-end"><?php echo $contagens['countInativo']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex align-items-center justify-content-end mt-3 gap-2">
                                <button type="button" class="btn btn-light text-dark border" data-bs-dismiss="modal">Fechar</button>
                                <button class="btn text-white" style="background-color: #202d3b;" type="submit">
                                    <i class="fa-solid fa-filter"></i> Filtrar 
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Remover Dador -->
    <div class="modal fade" id="deleteModalDador" tabindex="-1" aria-labelledby="deleteModalLabelDador" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabelDador">Confirmar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem a certeza de que deseja remover este dador?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <a id="deleteConfirmButtonDador" href="" class="btn btn-danger">Remover</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var deleteModal = document.getElementById("deleteModalDador");
            deleteModal.addEventListener("show.bs.modal", function(event) {
                var button = event.relatedTarget; 
                var dadorId = button.getAttribute("data-dador-id"); 
                var confirmButton = document.getElementById("deleteConfirmButtonDador");
                confirmButton.href = "includes/destroy.php?table=dadores&id=" + dadorId;
            });
        });
    </script>

<?php include 'partials/footer.php'; ?>