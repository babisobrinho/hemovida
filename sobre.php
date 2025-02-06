<?php
    include 'partials/header.php';

    $pageTitle = "Sobre";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Sobre', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4">
    
    <?php include 'partials/page-header.php'; ?>

    <div class="row">
        <div class="col-lg-7 col-12">
            <p>O projeto <b>HemoVida</b> foi desenvolvido no âmbito da UFCD 5085 <em>Criação de estrutura de base de dados em SQL</em> do CET <em>Tecnologias e Programação de Sistemas Informáticos</em>, com o objetivo de criar uma plataforma para a gestão de doações de sangue, aproveitando ao máximo os conceitos e ferramentas da linguagem SQL. A aplicação foi construída para permitir:</p>
            <ul>
                <li>O acompanhamento eficiente dos dadores de sangue;</li>
                <li>A gestão da agenda de doações;</li>
                <li>O controlo do inventário das bolsas de sangue;</li>
                <li>A supervisão dos exames clínicos realizados;</li>
                <li>A gestão de transfusões de sangue efetuadas;</li>
                <li>A gestão das parcerias com os hospitais.</li>
            </ul>
        </div>
        <div class="col-lg-5 col-12 text-center d-flex flex-column align-items-center">
            <img class="w-100" src="assets/images/logo_square.png" alt="Logo HemoVida">
            <small class="my-2 fw-semibold">Logótipo do Centro de Doações</small>
        </div>
        <div class="col-12">
            <p>O SQL foi a tecnologia central deste projeto, sendo utilizado para estruturar e organizar a base de dados, garantindo a integridade e a acessibilidade das informações. Através de consultas SQL, foi possível criar páginas dinâmicas, realizar atualizações em tempo real e fazer a gestão das interações entre as diversas funcionalidades da plataforma. A aplicação foi desenvolvida com as tecnologias PHP e Bootstrap, mas o SQL foi fundamental para a construção das tabelas, a definição de relacionamentos e a execução de operações de consulta e manipulação de dados.</p>
            <p>Este projeto representou a aplicação prática dos conhecimentos adquiridos em SQL; mostrou como a linguagem pode ser utilizada para criar soluções robustas e eficientes em um contexto real de gestão de dados. Se quiser saber mais detalhes sobre a nossa base de dados, veja a nossa página <a href="base-de-dados.php" class="text-danger">Base de Dados</a></p>
            </p>
        </div>
    </div>
    <div class="container py-4 text-center">
        <h3 class="fw-semibold mb-2">A Nossa Equipa</h3>
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <p class="text-dark fs-15 mb-5 fw-normal">A nossa equipa é formada por desenvolvedoras altamente<br>qualificadas que trabalham duro para elevar os padrões da empresa.</p>
            </div>
        </div>
        <div class="row d-flex align-items-center justify-content-center">
            <div class="col-xxl-2 col-xl-4 col-md-6 col-12">
                <div class="card border-0 text-center">
                    <div class="card-body p-0">
                        <a href="https://github.com/kiamy6" class="text-decoration-none" target="_blank">
                            <span>
                                <img class="rounded-circle shadow-lg" style="width: 150px;" src="https://avatars.githubusercontent.com/u/195438725?v=4" alt="Aline Armando">
                            </span>
                            <p class="fw-semibold fs-17 mt-3 mb-0 text-danger">Aline Armando</p>
                            <span class="text-dark fs-14 text-primary fw-semibold">Mobile</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-4 col-md-6 col-12">
                <div class="card border-0 text-center">
                    <div class="card-body p-0">
                        <a href="https://github.com/babisobrinho" class="text-decoration-none" target="_blank">
                            <span>
                                <img class="rounded-circle shadow-lg" style="width: 150px;" src="https://avatars.githubusercontent.com/u/79843387?v=4" alt="Babi Sobrinho">
                            </span>
                            <p class="fw-semibold fs-17 mt-3 mb-0 text-danger">Ana "Babi" Sobrinho</p>
                            <span class="text-dark fs-14 text-primary fw-semibold">Full-Stack</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-4 col-md-6 col-12">
                <div class="card border-0 text-center">
                    <div class="card-body p-0">
                        <a href="https://github.com/JulyDuds" class="text-decoration-none" target="_blank">
                            <span>
                                <img class="rounded-circle shadow-lg" style="width: 150px;" src="https://avatars.githubusercontent.com/u/197295278?v=4" alt="Juliana Alves">
                            </span>
                            <p class="fw-semibold fs-17 mt-3 mb-0 text-danger">Juliana Alves</p>
                            <span class="text-dark fs-14 text-primary fw-semibold">Mobile</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-4 col-md-6 col-12">
                <div class="card border-0 text-center">
                    <div class="card-body p-0">
                        <a href="hhttps://github.com/lenicesoaares" class="text-decoration-none" target="_blank">
                            <span>
                                <img class="rounded-circle shadow-lg" style="width: 150px;" src="https://avatars.githubusercontent.com/u/168729483?v=4" alt="Lenice Soares">
                            </span>
                            <p class="fw-semibold fs-17 mt-3 mb-0 text-danger">Lenice Soares</p>
                            <span class="text-dark fs-14 text-primary fw-semibold">Back-End</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-4 col-md-6 col-12">
                <div class="card border-0 text-center">
                    <div class="card-body p-0">
                        <a href="https://github.com/RebecaSantosb" class="text-decoration-none" target="_blank">
                            <span>
                                <img class="rounded-circle shadow-lg" style="width: 150px;" src="https://avatars.githubusercontent.com/u/195432659?v=4" alt="Rebeca Santos">
                            </span>
                            <p class="fw-semibold fs-17 mt-3 mb-0 text-danger">Rebeca Santos</p>
                            <span class="text-dark fs-14 text-primary fw-semibold">Front-End</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    include 'partials/footer.php';
?>