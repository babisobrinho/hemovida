-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 05-Fev-2025 às 16:21
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
  `estado` enum('disponivel','reservada','utilizada','vencida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `bolsas_sangue`
--

INSERT INTO `bolsas_sangue` (`id`, `id_dador`, `volume_ml`, `data_coleta`, `validade`, `estado`) VALUES
(1, 1, 450, '2018-06-15', '2018-09-13', 'utilizada'),
(2, 1, 470, '2020-09-22', '2020-12-21', 'vencida'),
(3, 8, 460, '2017-03-10', '2017-06-08', 'utilizada'),
(4, 8, 450, '2019-12-05', '2020-03-04', 'vencida'),
(5, 9, 480, '2016-07-28', '2016-10-26', 'utilizada'),
(6, 9, 470, '2021-04-18', '2021-07-17', 'utilizada'),
(7, 10, 455, '2015-11-30', '2016-02-28', 'vencida'),
(8, 10, 460, '2023-02-14', '2025-05-15', 'disponivel'),
(9, 11, 470, '2022-08-07', '2022-11-06', 'utilizada'),
(10, 12, 450, '2019-05-21', '2019-08-19', 'cencida');

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
  `sexo` enum('masculino','feminino') NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `data_inscricao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `dadores`
--

INSERT INTO `dadores` (`id`, `nome`, `email`, `n_utente`, `data_nascimento`, `tipo_sanguineo`, `peso`, `sexo`, `estado`, `data_inscricao`) VALUES
(1, 'João Oliveira', 'joao.oliveira98@email.com', 123456789, '1998-04-15', 'O+', 72.5, 'masculino', 1, '2025-02-06'),
(8, 'Rafael Moreira', 'rafael.moreira92@email.com', 123456779, '1992-07-21', 'O+', 74.5, 'masculino', 1, '2025-02-06'),
(9, 'Beatriz Almeida', 'beatriz.almeida88@email.com', 987654321, '1988-11-03', 'A-', 62.3, 'feminino', 1, '2025-02-06'),
(10, 'Lucas Ferreira', 'lucas.ferreira95@email.com', 456123789, '1995-03-12', 'B+', 80.7, 'masculino', 1, '2025-02-06'),
(11, 'Sofia Costa', 'sofia.costa99@email.com', 741852963, '1999-09-28', 'AB-', 55.8, 'feminino', 1, '2025-02-06'),
(12, 'Miguel Nunes', 'miguel.nunes85@email.com', 369258147, '1985-05-17', 'A+', 88.2, 'masculino', 1, '2025-02-06'),
(13, 'Luana Texeira', 'Luna.texeira@gmail.com', 312314567, '1999-08-05', 'A+', 65, 'feminino', 1, '2025-02-06'),
(14, 'Junior Cavalcante', 'Junior.valc@gmail.com', 345678234, '2000-04-06', 'O-', 76, 'masculino', 1, '2025-02-06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `doacoes`
--

CREATE TABLE `doacoes` (
  `id` int(11) NOT NULL,
  `id_dador` int(11) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `estado` enum('agendado','em_atendimento','concluido','cancelado') NOT NULL,
  `observacoes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `doacoes`
--

INSERT INTO `doacoes` (`id`, `id_dador`, `data`, `hora`, `estado`, `observacoes`) VALUES
(1, 1, '2018-06-15', '10:30:00', 'concluido', NULL),
(2, 1, '2020-09-22', '14:15:00', 'concluido', NULL),
(3, 8, '2017-03-10', '09:45:00', 'concluido', NULL),
(4, 8, '2019-12-05', '16:20:00', 'concluido', NULL),
(5, 9, '2016-07-28', '11:10:00', 'concluido', NULL),
(6, 9, '2021-04-18', '13:00:00', 'concluido', NULL),
(7, 10, '2015-11-30', '08:55:00', 'concluido', NULL),
(8, 10, '2023-02-14', '15:40:00', 'concluido', NULL),
(9, 11, '2022-08-07', '12:25:00', 'concluido', NULL),
(10, 12, '2019-05-21', '17:30:00', 'concluido', NULL),
(11, 11, '2025-01-31', '15:00:00', 'agendado', NULL),
(12, 10, '2025-01-31', '10:30:00', 'agendado', NULL),
(13, 11, '2025-02-04', '19:00:00', 'concluido', NULL),
(14, 13, '2025-02-05', '12:30:00', 'concluido', NULL);

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
  `resultado` enum('em_analise','aprovado','reprovado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `exames`
--

INSERT INTO `exames` (`id`, `id_bolsa`, `data`, `hemoglobina`, `hepatite`, `hiv`, `chagas`, `sifilis`, `resultado`) VALUES
(1, 1, '2018-06-15', 14.2, 0, 0, 0, 0, 'aprovado'),
(2, 3, '2017-03-10', 13.8, 0, 0, 0, 0, 'aprovado'),
(3, 5, '2016-07-28', 14.5, 0, 0, 0, 0, 'aprovado'),
(4, 6, '2021-04-18', 13.9, 0, 0, 0, 0, 'aprovado'),
(5, 9, '2022-08-07', 14.1, 0, 0, 0, 0, 'aprovado');

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
(1, 'Hospital Sant’Ana de A-do-Barbas', 'Rua da Capela, 2405-001, Leiria, Portugal', '+351 244 123 456', 'contato@hospital-santana.pt', 'Dr. Manuel Ferreira', 1),
(2, 'Hospital Beata Aline de Luanda', 'Avenida da Esperança, 3100-285, Pombal, Portugal', '+351 236 987 654', 'geral@hospital-beataaline.pt', 'Dra. Catarina Silva', 0),
(3, 'Hospital Madre Juliana de Campo Grande', 'Largo dos Milagres, 2430-014, Marinha Grande, Portugal', '+351 244 321 789', 'geral@hospital-madrejuliana.pt', 'Dr. Ricardo Mendes', 1),
(4, 'Hospital Nossa Senhora de Lenice', 'Travessa da Saúde, 2434-020, Batalha, Portugal', '+351 244 654 321', 'contato@hospital-nslenice.pt', 'Dra. Ana Beatriz Lopes', 0),
(5, 'Hospital Irmã Rebeca de Recife', 'Estrada dos Anjos, 2480-169, Porto de Mós, Portugal', '+351 244 852 963', 'contato@hospital-irmarebeca.pt', 'Dr. João Pereira', 0),
(6, 'Hospital São João de Lisboa', 'Avenida João XXIII, 1000-100, Lisboa, Portugal', '+351 210 123 456', 'geral@hospital-saojoao.pt', 'Dr. José Almeida', 1),
(7, 'Hospital Santa Maria do Porto', 'Rua de Santa Catarina, 4000-267, Porto, Portugal', '+351 220 654 321', 'contato@hospital-santamaria.pt', 'Dra. Clara Costa', 1),
(8, 'Hospital de São Pedro de Braga', 'Rua da Ponte, 4710-434, Braga, Portugal', '+351 253 987 654', 'geral@hospital-saopedro.pt', 'Dr. Fernando Lima', 0),
(9, 'Hospital Nossa Senhora da Graça', 'Avenida da Liberdade, 5000-001, Coimbra, Portugal', '+351 239 852 963', 'contato@hospital-ssaograça.pt', 'Dr. João Pereira', 0);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_dador` (`id_dador`);

--
-- Índices para tabela `exames`
--
ALTER TABLE `exames`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bolsa` (`id_bolsa`);

--
-- Índices para tabela `hospitais`
--
ALTER TABLE `hospitais`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `transfusoes`
--
ALTER TABLE `transfusoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bolsa` (`id_bolsa`),
  ADD KEY `id_hospital` (`id_hospital`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `exames`
--
ALTER TABLE `exames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `hospitais`
--
ALTER TABLE `hospitais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `transfusoes`
--
ALTER TABLE `transfusoes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `bolsas_sangue`
--
ALTER TABLE `bolsas_sangue`
  ADD CONSTRAINT `bolsas_sangue_ibfk_1` FOREIGN KEY (`id`) REFERENCES `doacoes` (`id`);

--
-- Limitadores para a tabela `doacoes`
--
ALTER TABLE `doacoes`
  ADD CONSTRAINT `doacoes_ibfk_1` FOREIGN KEY (`id_dador`) REFERENCES `dadores` (`id`);

--
-- Limitadores para a tabela `exames`
--
ALTER TABLE `exames`
  ADD CONSTRAINT `exames_ibfk_1` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsas_sangue` (`id`);

--
-- Limitadores para a tabela `transfusoes`
--
ALTER TABLE `transfusoes`
  ADD CONSTRAINT `transfusoes_ibfk_1` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsas_sangue` (`id`),
  ADD CONSTRAINT `transfusoes_ibfk_2` FOREIGN KEY (`id_hospital`) REFERENCES `hospitais` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;