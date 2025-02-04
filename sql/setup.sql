-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 04-Fev-2025 às 15:35
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `hemovida_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `bolsas_sangue`
--

CREATE TABLE `bolsas_sangue` (
  `id` int(11) NOT NULL,
  `id_dador` int(11) NOT NULL,
  `volume_ml` double NOT NULL,
  `data_coleta` date NOT NULL,
  `validade` date NOT NULL,
  `estado` enum('Disponível','Reservada','Utilizada','Vencida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `bolsas_sangue`
--

INSERT INTO `bolsas_sangue` (`id`, `id_dador`, `volume_ml`, `data_coleta`, `validade`, `estado`) VALUES
(1, 1, 450, '2018-06-15', '2018-09-13', 'Utilizada'),
(2, 1, 470, '2020-09-22', '2020-12-21', 'Vencida'),
(3, 8, 460, '2017-03-10', '2017-06-08', 'Utilizada'),
(4, 8, 450, '2019-12-05', '2020-03-04', 'Vencida'),
(5, 9, 480, '2016-07-28', '2016-10-26', 'Utilizada'),
(6, 9, 470, '2021-04-18', '2021-07-17', 'Utilizada'),
(7, 10, 455, '2015-11-30', '2016-02-28', 'Vencida'),
(8, 10, 460, '2023-02-14', '2025-05-15', 'Disponível'),
(9, 11, 470, '2022-08-07', '2022-11-06', 'Utilizada'),
(10, 12, 450, '2019-05-21', '2019-08-19', 'Vencida');

-- --------------------------------------------------------

--
-- Estrutura da tabela `dadores`
--

CREATE TABLE `dadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `n_utente` int(11) NOT NULL,
  `data_nascimento` date NOT NULL,
  `tipo_sanguineo` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `peso` float NOT NULL,
  `sexo` enum('Masculino','Feminino') NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `data_inscricao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `dadores`
--

INSERT INTO `dadores` (`id`, `nome`, `email`, `n_utente`, `data_nascimento`, `tipo_sanguineo`, `peso`, `sexo`, `estado`, `data_inscricao`) VALUES
(1, 'João Oliveira', 'joao.oliveira98@email.com', 123456789, '1998-04-15', 'O+', 72.5, 'Masculino', 1, '0000-00-00'),
(8, 'Rafael Moreira', 'rafael.moreira92@email.com', 123456779, '1992-07-21', 'O+', 74.5, 'Masculino', 1, '0000-00-00'),
(9, 'Beatriz Almeida', 'beatriz.almeida88@email.com', 987654321, '1988-11-03', 'A-', 62.3, 'Feminino', 1, '0000-00-00'),
(10, 'Lucas Ferreira', 'lucas.ferreira95@email.com', 456123789, '1995-03-12', 'B+', 80.7, 'Masculino', 1, '0000-00-00'),
(11, 'Sofia Costa', 'sofia.costa99@email.com', 741852963, '1999-09-28', 'AB-', 55.8, 'Feminino', 1, '0000-00-00'),
(12, 'Miguel Nunes', 'miguel.nunes85@email.com', 369258147, '1985-05-17', 'A+', 88.2, 'Masculino', 0, '0000-00-00'),
(13, 'Luana Texeira', 'Luna.texeira@gmail.com', 312314567, '1999-08-05', 'A+', 65, 'Feminino', 0, '0000-00-00'),
(14, 'Junior Cavalcante', 'Junior.valc@gmail.com', 345678234, '2000-04-06', 'O-', 76, 'Masculino', 1, '0000-00-00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `doacoes`
--

CREATE TABLE `doacoes` (
  `id` int(11) NOT NULL,
  `id_dador` int(11) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `estado` enum('Agendado','Concluído','Cancelado') NOT NULL,
  `observacoes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `doacoes`
--

INSERT INTO `doacoes` (`id`, `id_dador`, `data`, `hora`, `estado`, `observacoes`) VALUES
(1, 1, '2018-06-15', '10:30:00', 'Concluído', NULL),
(2, 1, '2020-09-22', '14:15:00', 'Concluído', NULL),
(3, 8, '2017-03-10', '09:45:00', 'Concluído', NULL),
(4, 8, '2019-12-05', '16:20:00', 'Concluído', NULL),
(5, 9, '2016-07-28', '11:10:00', 'Concluído', NULL),
(6, 9, '2021-04-18', '13:00:00', 'Concluído', NULL),
(7, 10, '2015-11-30', '08:55:00', 'Concluído', NULL),
(8, 10, '2023-02-14', '15:40:00', 'Concluído', NULL),
(9, 11, '2022-08-07', '12:25:00', 'Concluído', NULL),
(10, 12, '2019-05-21', '17:30:00', 'Concluído', NULL),
(11, 11, '2025-01-31', '15:00:00', 'Agendado', NULL),
(12, 10, '2025-01-31', '10:30:00', 'Agendado', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `exames`
--

CREATE TABLE `exames` (
  `id` int(11) NOT NULL,
  `id_bolsa` int(11) NOT NULL,
  `data` date NOT NULL,
  `hemoglobina` float NOT NULL,
  `hepatite` tinyint(1) NOT NULL,
  `hiv` tinyint(1) NOT NULL,
  `chagas` tinyint(1) NOT NULL,
  `sifilis` tinyint(1) NOT NULL,
  `resultado` enum('Em Análise','Aprovado','Reprovado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `exames`
--

INSERT INTO `exames` (`id`, `id_bolsa`, `data`, `hemoglobina`, `hepatite`, `hiv`, `chagas`, `sifilis`, `resultado`) VALUES
(1, 1, '2018-06-15', 14.2, 0, 0, 0, 0, 'Aprovado'),
(2, 3, '2017-03-10', 13.8, 0, 0, 0, 0, 'Aprovado'),
(3, 5, '2016-07-28', 14.5, 0, 0, 0, 0, 'Aprovado'),
(4, 6, '2021-04-18', 13.9, 0, 0, 0, 0, 'Aprovado'),
(5, 9, '2022-08-07', 14.1, 0, 0, 0, 0, 'Aprovado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `hospitais`
--

CREATE TABLE `hospitais` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `endereco` text NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nome_responsavel` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `hospitais`
--

INSERT INTO `hospitais` (`id`, `nome`, `endereco`, `telefone`, `email`, `nome_responsavel`, `estado`) VALUES
(1, 'Hospital Sant’Ana de A-do-Barbas', 'Rua da Capela, 2405-001, Leiria, Portugal', '+351 244 123 456', 'contato@hospital-santana.pt', 'Dr. Manuel Ferreira', 0),
(2, 'Hospital Beata Aline de Luanda', 'Avenida da Esperança, 3100-285, Pombal, Portugal', '+351 236 987 654', 'geral@hospital-beataaline.pt', 'Dra. Catarina Silva', 0),
(3, 'Hospital Madre Juliana de Campo Grande', 'Largo dos Milagres, 2430-014, Marinha Grande, Portugal', '+351 244 321 789', 'geral@hospital-madrejuliana.pt', 'Dr. Ricardo Mendes', 0),
(4, 'Hospital Nossa Senhora de Lenice', 'Travessa da Saúde, 2434-020, Batalha, Portugal', '+351 244 654 321', 'contato@hospital-nslenice.pt', 'Dra. Ana Beatriz Lopes', 0),
(5, 'Hospital Irmã Rebeca de Recife', 'Estrada dos Anjos, 2480-169, Porto de Mós, Portugal', '+351 244 852 963', 'contato@hospital-irmarebeca.pt', 'Dr. João Pereira', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `transfusoes`
--

CREATE TABLE `transfusoes` (
  `id` int(10) NOT NULL,
  `id_bolsa` int(10) NOT NULL,
  `n_utente` int(9) NOT NULL,
  `data` date NOT NULL,
  `id_hospital` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `transfusoes`
--

INSERT INTO `transfusoes` (`id`, `id_bolsa`, `n_utente`, `data`, `id_hospital`) VALUES
(1, 1, 234567890, '2018-07-01', 1),
(2, 3, 345678901, '2017-03-15', 3),
(3, 5, 456789012, '2016-08-02', 4),
(4, 6, 567890123, '2021-05-02', 2),
(5, 5, 678901234, '2022-08-15', 5);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `bolsas_sangue`
--
ALTER TABLE `bolsas_sangue`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `dadores`
--
ALTER TABLE `dadores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `doacoes`
--
ALTER TABLE `doacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `exames`
--
ALTER TABLE `exames`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `hospitais`
--
ALTER TABLE `hospitais`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `transfusoes`
--
ALTER TABLE `transfusoes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bolsas_sangue`
--
ALTER TABLE `bolsas_sangue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `dadores`
--
ALTER TABLE `dadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `doacoes`
--
ALTER TABLE `doacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `exames`
--
ALTER TABLE `exames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `hospitais`
--
ALTER TABLE `hospitais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `transfusoes`
--
ALTER TABLE `transfusoes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
