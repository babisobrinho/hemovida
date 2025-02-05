<?php

    header('Content-Type: text/html; charset=utf-8');

    // Configuração da base de dados
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'hemovida');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8');

    // Variáveis globais
    define('BASE_URL', 'http://localhost/');

    // Definir o fuso horário
    date_default_timezone_set('Europe/Lisbon');

    // Configurações de segurança (ex. chave secreta para sessões, tokens CSRF, etc.)
    define('SECRET_KEY', '');

?>
