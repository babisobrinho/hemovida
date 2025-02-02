# HemoVida - Centro de Doações de Sangue

<p align="center">
  <img src="https://raw.githubusercontent.com/babisobrinho/hemovida/refs/heads/main/assets/images/logo_square.png">
</p>

Este projeto é um sistema de gestão de doações de sangue desenvolvido com as tecnologias PHP e Bootstrap, mas com ênfase em SQL.

O SQL foi a tecnologia central deste projeto, sendo utilizado para estruturar e organizar a base de dados, garantindo a integridade e a acessibilidade das informações. Através de consultas SQL, foi possível criar páginas dinâmicas, realizar atualizações em tempo real e fazer a gestão das interações entre as diversas funcionalidades da plataforma. O SQL foi fundamental para a construção das tabelas, a definição de relacionamentos e a execução de operações de consulta e manipulação de dados.

## 📌 Funcionalidades

- Acompanhamento dos dadores de sangue;
- Gestão da agenda de doações;
- Controlo das bolsas de sangue;
- Supervisão do estado dos exames clínicos efetuados;
- Gestão de transfusões de sangue realizadas;
- Gestão das parcerias com os hospitais.

## 💻 Instalação

1. Clone o repositório:
```
git clone https://github.com/babisobrinho/hemovida.git
cd hemovida
```

2. Configure a Base de Dados

O projeto depende de uma base de dados para armazenar informações sobre os dadores, doações, bolsas de sangue, transfusões, etc.
- Crie uma base de dados no MySQL com o nome hemovida (ou outro nome, se preferir)
- Importe o script de criação de tabelas contido no ficheiro `sql/setup.sql` na sua nova base de dados

3. Iniciar o Servidor Web

Caso esteja a usar o XAMPP ou MAMP, inicie os servidores Apache e MySQL:
- No XAMPP, abra o painel de controlo e clique em "Start" para o Apache e MySQL
- No MAMP, clique em "Iniciar Servidores"

4. Aceda à plataforma:
```
http://localhost/hemovida/
```

## 🛠 Estrutura do Projeto
```
/hemovida
├── assets/                 # Ficheiros estáticos como CSS, JS, imagens
│   ├── css/                # Ficheiros CSS
│   ├── js/                 # Ficheiros JavaScript
│   └── images/             # Imagens usadas no site
│
├── includes/               # Ficheiros PHP que são incluídos nas páginas (funções, conexões com a BD)
│   └── db_connection.php   # Conexão com a base de dados
│
├── partials/               # Ficheiros PHP que são parte do layout das páginas
│   ├── footer.php          # Rodapé comum para todas as páginas
│   └── header.php          # Cabeçalho comum para todas as páginas
│   └── page-header.php     # Título e breadcrumb comum para algumas páginas
│   └── scripts.php         # Scripts comuns para todas as páginas
│
├── sql/                    # Scripts SQL
│   └── setup.sql           # Script para a criação da base de dados e das tabelas
│
├── config/                 # Configurações gerais do projeto
│   └── config.php          # Configurações do sistema (ex. credenciais da base de dados)
│
├── index.php               # Página inicial
├── dadores.php             # Página de gestão de doadores
├── agenda.php              # Página de agenda de doações
├── inventario.php          # Página de inventário de bolsas de sangue
├── exames.php              # Página de exames realizados ao sangue
├── transfusoes.php         # Página de transfusões de sangue realizadas
├── hospitais.php           # Página de hospitais parceiros
│
├── README.md               # Ficheiro com as informações sobre o projeto
│
└── .gitignore              # Ficheiro para ignorar os ficheiros que não devem ser versionados

```

## ⚙ Tecnologias Utilizadas

- PHP 8+
- MySQL 8+
- HTML5
- Bootstrap 5+

## 📄 Licença

Este projeto está sob a licença MIT.

## 👩🏻‍💻 Créditos

Desenvolvido por [Babi Sobrinho](https://github.com/babisobrinho), [Lenice Soares](https://github.com/lenicesoaares), [Rebeca Santos](https://github.com/RebecaSantosb), [Juliana Alves](https://github.com/JulyDuds) e [Aline Armando](https://github.com/kiamy6) no âmbito da UFCD "5085 - Criação de estrutura de base de dados em SQL" do CET Tecnologias e Programação de Sistemas Informáticos.