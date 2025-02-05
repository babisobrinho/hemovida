<?php
    include 'partials/header.php';
    include 'includes/index_info.php';
    //include 'includes/db_functions.php';

    //$dadoresAtivos = getActiveDadores($pdo)['dadoresAtivos'];
?>

    <div class="container p-4">
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-0 p-0 m-0">
                    <div class="card-body d-flex justify-content-center">
                        <div class="row">
                            <div class="col-xl-2 col-sm-4 col-6 rounded d-flex align-items-center justify-content-center">
                                <a class="p-4 text-decoration-none" href="/dadores.php">
                                    <div class="text-center">
                                        <i class="fa-solid fa-hospital-user" style="color: #202d3b; font-size: 80px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 mt-1 mb-0 text-dark fw-semibold">Dadores</p>
                                        <span class="fs-11 text-muted">
                                            <?php echo $dadoresAtivos; echo $dadoresAtivos === 1 ? ' Ativo' : ' Ativos' ?>
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-sm-4 col-6 rounded d-flex align-items-center justify-content-center">
                                <a class="p-4 text-decoration-none" href="/doacoes.php">
                                    <div class="text-center">
                                        <i class="fa-regular fa-calendar" style="color: #202d3b; font-size: 80px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 mt-1 mb-0 text-dark fw-semibold">Doações</p>
                                        <span class="fs-11 text-muted">
                                            <?php echo $doacoesHoje; ?> Hoje
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-sm-4 col-6 rounded d-flex align-items-center justify-content-center">
                                <a class="p-4 text-decoration-none" href="/bolsas_sangue.php">
                                    <div class="text-center">
                                        <i class="fa-solid fa-droplet" style="color: #202d3b; font-size: 80px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 mt-1 mb-0 text-dark fw-semibold">Inventário</p>
                                        <span class="fs-11 text-muted"><?php echo $totalSangueDisponivel; ?>L</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-sm-4 col-6 rounded d-flex align-items-center justify-content-center">
                                <a class="p-4 text-decoration-none" href="/exames.php">
                                    <div class="text-center">
                                        <i class="fa-solid fa-flask-vial" style="color: #202d3b; font-size: 80px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 mt-1 mb-0 text-dark fw-semibold">Exames</p>
                                        <span class="fs-11 text-muted"><?php echo $examesHoje; ?> Hoje</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-sm-4 col-6 rounded d-flex align-items-center justify-content-center">
                                <a class="p-4 text-decoration-none" href="/transfusoes.php">
                                    <div class="text-center">
                                    <i class="fa-solid fa-heart-pulse" style="color: #202d3b; font-size: 80px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 mt-1 mb-0 text-dark fw-semibold">Transfusões</p>
                                        <span class="fs-11 text-muted"><?php echo $transfusoesHoje; ?> Hoje</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-sm-4 col-6 rounded d-flex align-items-center justify-content-center">
                                <a class="p-4 text-decoration-none" href="/hospitais.php">
                                    <div class="text-center">
                                        <i class="fa-regular fa-hospital" style="color: #202d3b; font-size: 80px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 mt-1 mb-0 text-dark fw-semibold">Hospitais</p>
                                        <span class="fs-11 text-muted"><?php echo $totalHospitais; ?> Parceiros</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'partials/footer.php'; ?>