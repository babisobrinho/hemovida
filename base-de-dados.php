<?php
    include 'partials/header.php';

    $pageTitle = "Base de Dados";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Sobre', 'url' => 'sobre.php', 'active' => false],
        ['title' => 'Base de Dados', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4 pb-0">

    <?php include 'partials/page-header.php'; ?>

    <div class="row">
        <div class="col-12">
            <p>A base de dados do projeto <b>HemoVida</b> foi projetada para gerir o processo de doação e transfusão de sangue de forma eficiente. Ela é composta por diversas tabelas interligadas que armazenam e organizam informações sobre doadores, doações, bolsas de sangue, transfusões e exames médicos.</p>
        </div>
        <div class="col-12">
            <h3>Principais Entidades</h3>
            <ul>
                <li><code>dadores</code> contém as informações pessoais dos dodores de sangue, como nome, e-mail, número único de utente, tipo sanguíneo, peso, sexo, estado e data de inscrição;</li>
                <li><code>doacoes</code> controla as marcações feitas para doação de sangue, associando cada doação a um dador. Regista a data, hora, o estado da doação e observações adicionais;</li>
                <li><code>bolsas_sangue</code> controla o inventário de bolsas de sangue coletadas, associando cada uma a um dador. Regista o volume de sangue, a data de coleta, a data de validade e o estado;</li>
                <li><code>exames</code> contém os exames médicos realizados nas bolsas de sangue. Regista os níveis de hemoglobina e os resultados de testes para doenças como hepatite, HIV, chagas e sífilis. Por fim, também registado o resultado do exame;</li>
                <li><code>hospitais</code> contém as informações sobre os hospitais parceiros. Regista o nome, endereço, contato e o nome do responsável do hospital;</li>
                <li><code>transfusoes</code> relaciona as bolsas de sangue a pacientes que recebem transfusões nos hospitais. Regista o ID da bolsa de sangue, o número único de utente do receptor, a data da transfusão e o hospital onde foi realizada.</li>
            </ul>
        </div>
        <div class="col-12">
            <h3>Relacionamentos</h3>
            <ul>
                <li><code>dadores</code> ↔ <code>doacoes</code> um doador pode realizar várias doações;</li>
                <li><code>doacoes</code> ↔ <code>bolsas_sangue</code> cada doação pode gerar uma ou mais bolsas de sangue;</li>
                <li><code>bolsas_sangue</code> ↔ <code>exames</code> todas as bolsas são submetidas a exames antes de serem utilizadas;</li>
                <li><code>bolsas_sangue</code> ↔ <code>transfusoes</code> apenas as bolsas aprovadas podem ser utilizadas para transfusões;</li>
                <li><code>transfusoes</code> ↔ <code>hospitais</code> as transfusões são realizadas em hospitais registados como parceiros.</li>
            </ul>
            </p>
        </div>
    </div>
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-lg-8 col-12 text-center d-flex flex-column align-items-center justify-content-center">
            <img class="w-100" src="assets/images/database.png" alt="Base de Dados">
            <small class="mt-2 fw-semibold">Modelo E-R da Base de Dados</small>
        </div>
    </div>
</div>

<?php
    include 'partials/footer.php';
?>