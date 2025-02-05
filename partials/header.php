<?php
    include 'config/config.php';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoVida</title>

    <!-- FAVICON -->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    
    <!-- FONTAWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/regular.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/solid.min.css"/>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

</head>
<body>

    <nav class="navbar bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="assets/images/logo_horizontal.png" alt="HemoVida Logo" height="24">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">HemoVida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/sobre.php">Sobre</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Páginas
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="dadores.php">
                                        <i class="fa-solid fa-hospital-user"></i> Dadores
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="doacoes.php">
                                        <i class="fa-regular fa-calendar"></i> Doações
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="bolsas_sangue.php">
                                        <i class="fa-solid fa-droplet"></i> Inventário
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="exames.php">
                                        <i class="fa-solid fa-flask-vial"></i> Exames
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="transfusoes.php">
                                        <i class="fa-solid fa-heart-pulse"></i> Transfusões
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="hospitais.php">
                                        <i class="fa-regular fa-hospital"></i> Hospitais
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="position-relative mt-5">