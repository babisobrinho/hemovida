<?php
    include 'partials/header.php';

    $pageTitle = "Perguntas Frequentes";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'FAQ', 'url' => '#', 'active' => true]
    ];
?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-md-10 col-12 mb-2">
            <p style="text-align: justify; text-justify: inter-word;">
                Este FAQ foi criado para esclarecer as dúvidas mais comuns sobre a doação de sangue, desde os requisitos básicos até situações específicas que podem afetar a sua elegibilidade.
                O nosso objetivo é incentivar a doação de sangue de forma segura e consciente, garantindo a saúde tanto do dador quanto do receptor.
                Se ainda tiver dúvidas, consulte um profissional de saúde. <b>A sua doação salva vidas!</b>
            </p>
        </div>
        <div class="col-md-10 col-12">
            <div class="accordion accordion-flush" id="accordionFlushFAQ">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse1" aria-expanded="false" aria-controls="flush-collapse1">
                            <b>Posso dar sangue?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse1" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <ul>
                                <li><b>Idade e peso:</b> Maiores de 18 anos e mais de 50 kg.</li>
                                <li><b>Saúde:</b> Estilo de vida saudável e sem condições que comprometam a segurança do dador ou do receptor.</li>
                                <li><b>Intervalo entre doações:</b> 2 meses (máximo de 3 doações/ano para mulheres e 4 para homens).</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                            <b>Não sei o meu tipo sanguíneo. Posso dar sangue?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse2" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <b>Sim.</b> Após a primeira doação, o seu tipo sanguíneo será identificado.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse3" aria-expanded="false" aria-controls="flush-collapse3">
                            <b>Posso dar sangue após a vacinação contra COVID-19?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse3" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <ul>
                                <li><b>AstraZeneca/Janssen:</b> Suspensão de 7 dias, se assintomático.</li>
                                <li><b>Outras vacinas:</b> Sem suspensão, se assintomático.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse4" aria-expanded="false" aria-controls="flush-collapse4">
                            <b>Posso dar sangue se tiver tido COVID-19?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse4" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <b>Sim</b>, após 14 dias sem sintomas.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse5" aria-expanded="false" aria-controls="flush-collapse5">
                            <b>Posso dar sangue após cirurgia ou exames?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse5" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <ul>
                                <li><b>Cirurgia geral:</b> 4 meses.</li>
                                <li><b>Pequenas cirurgias:</b> 1 semana.</li>
                                <li><b>Endoscopia/colonoscopia:</b> 4 meses.</li>
                                <li><b>Tratamento dentário:</b> 7 dias (extração) ou 24 horas (limpeza).</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse6" aria-expanded="false" aria-controls="flush-collapse6">
                            <b>Posso dar sangue se tiver gripe?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse6" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <b>Não.</b> Aguarde 15 dias após a recuperação.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse7" aria-expanded="false" aria-controls="flush-collapse7">
                            <b>Posso dar sangue se tiver tatuagem ou piercing?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse7" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <b>Sim</b>, após 4 meses da realização.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse8" aria-expanded="false" aria-controls="flush-collapse8">
                            <b>Posso dar sangue se tiver diabetes ou hipertensão??</b>
                        </button>
                    </h2>
                    <div id="flush-collapse8" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <ul>
                                <li><b>Diabetes tipo 1:</b> Não pode dar sangue.</li>
                                <li><b>Diabetes tipo 2:</b> Pode, se controlada com dieta ou medicação oral.</li>
                                <li><b>Hipertensão:</b> Pode, se controlada e sem complicações.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse10" aria-expanded="false" aria-controls="flush-collapse10">
                            <b>Posso dar sangue se estiver com o período?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse10" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <b>Sim</b>, a menos que tenha dores intensas ou fluxo muito abundante.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse11" aria-expanded="false" aria-controls="flush-collapse11">
                            <b>Posso dar sangue se estiver grávida ou a amamentar?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse11" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <ul>
                                <li><b>Grávida:</b> Não pode dar sangue. Aguarde 6 meses após o parto.</li>
                                <li><b>A amamentar:</b> Aguarde 90 dias após o término.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse12" aria-expanded="false" aria-controls="flush-collapse12">
                            <b>Posso dar sangue se tiver viajado recentemente?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse12" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            Depende do destino e exposição a doenças infecciosas. <b>Consulte o serviço de sangue.</b>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse13" aria-expanded="false" aria-controls="flush-collapse13">
                            <b>Quais são os cuidados pós-doação?</b>
                        </button>
                    </h2>
                    <div id="flush-collapse13" class="accordion-collapse collapse" data-bs-parent="#accordionFlushFAQ">
                        <div class="accordion-body">
                            <ul>
                                <li>Beba líquidos e evite atividades perigosas por 12 horas.</li>
                                <li>Pilotos e controladores de tráfego aéreo devem aguardar 12-72 horas para retomar atividades.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    include 'partials/footer.php';
?>