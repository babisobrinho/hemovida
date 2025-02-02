-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Server version: 8.0.30
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `hemovida`
--

-- --------------------------------------------------------

--
-- Table structure for table `bolsas_sangue`
--

CREATE TABLE `bolsas_sangue` (
    `id` int NOT NULL,
    `id_dador` int NOT NULL,
    `volume_ml` double NOT NULL,
    `data_coleta` date NOT NULL,
    `validade` date NOT NULL,
    `estado` enum('Disponível','Reservada','Utilizada','Vencida') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bolsas_sangue`
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
-- Table structure for table `dadores`
--

CREATE TABLE `dadores` (
    `id` int NOT NULL,
    `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `n_utente` int NOT NULL,
    `data_nascimento` date NOT NULL,
    `tipo_sanguineo` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') COLLATE utf8mb4_general_ci NOT NULL,
    `peso` float NOT NULL,
    `sexo` enum('Masculino','Feminino') COLLATE utf8mb4_general_ci NOT NULL,
    `estado` tinyint(1) DEFAULT NULL,
    `data_inscricao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dadores`
--

INSERT INTO `dadores` (`id`, `nome`, `email`, `n_utente`, `data_nascimento`, `tipo_sanguineo`, `peso`, `sexo`, `estado`, `data_inscricao`) VALUES
(1, 'João Oliveira', 'joao.oliveira98@email.com', 123456789, '1998-04-15', 'O+', 72.5, 'Masculino', 1, '1900-01-01'),
(8, 'Rafael Moreira', 'rafael.moreira92@email.com', 123456779, '1992-07-21', 'O+', 74.5, 'Masculino', 1, '1900-01-01'),
(9, 'Beatriz Almeida', 'beatriz.almeida88@email.com', 987654321, '1988-11-03', 'A-', 62.3, 'Feminino', 1, '1900-01-01'),
(10, 'Lucas Ferreira', 'lucas.ferreira95@email.com', 456123789, '1995-03-12', 'B+', 80.7, 'Masculino', 0, '1900-01-01'),
(11, 'Sofia Costa', 'sofia.costa99@email.com', 741852963, '1999-09-28', 'AB-', 55.8, 'Feminino', 1, '1900-01-01'),
(12, 'Miguel Nunes', 'miguel.nunes85@email.com', 369258147, '1985-05-17', 'A+', 88.2, 'Masculino', 1, '1900-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `doacoes`
--

CREATE TABLE `doacoes` (
    `id` int NOT NULL,
    `id_dador` int NOT NULL,
    `data` date NOT NULL,
    `hora` time NOT NULL,
    `estado` enum('Agendado','Concluído','Cancelado') COLLATE utf8mb4_general_ci NOT NULL,
    `observacoes` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doacoes`
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
(10, 12, '2025-01-31', '09:30:00', 'Cancelado', NULL),
(11, 11, '2025-01-31', '15:00:00', 'Agendado', NULL),
(12, 10, '2025-02-01', '10:30:00', 'Agendado', 'Dador tem medo de agulhas'),
(13, 8, '2025-02-01', '11:30:00', 'Concluído', NULL),
(16, 11, '2025-02-04', '18:30:00', 'Cancelado', NULL),
(19, 8, '2025-02-11', '07:30:00', 'Agendado', NULL),
(21, 1, '2025-02-11', '10:00:00', 'Cancelado', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exames`
--

CREATE TABLE `exames` (
    `id` int NOT NULL,
    `id_bolsa` int NOT NULL,
    `data` date NOT NULL,
    `hemoglobina` float NOT NULL,
    `hepatite` tinyint(1) NOT NULL,
    `hiv` tinyint(1) NOT NULL,
    `chagas` tinyint(1) NOT NULL,
    `sifilis` tinyint(1) NOT NULL,
    `resultado` enum('Em Análise','Aprovado','Reprovado') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exames`
--

INSERT INTO `exames` (`id`, `id_bolsa`, `data`, `hemoglobina`, `hepatite`, `hiv`, `chagas`, `sifilis`, `resultado`) VALUES
(1, 1, '2018-06-15', 14.2, 0, 0, 0, 0, 'Aprovado'),
(2, 3, '2017-03-10', 13.8, 0, 0, 0, 0, 'Aprovado'),
(3, 5, '2016-07-28', 14.5, 0, 0, 0, 0, 'Aprovado'),
(4, 6, '2021-04-18', 13.9, 0, 0, 0, 0, 'Aprovado'),
(5, 9, '2022-08-07', 14.1, 0, 0, 0, 0, 'Aprovado');

-- --------------------------------------------------------

--
-- Table structure for table `hospitais`
--

CREATE TABLE `hospitais` (
    `id` int NOT NULL,
    `nome` varchar(150) NOT NULL,
    `endereco` text NOT NULL,
    `telefone` varchar(20) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `nome_responsavel` varchar(100) DEFAULT NULL,
    `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hospitais`
--

INSERT INTO `hospitais` (`id`, `nome`, `endereco`, `telefone`, `email`, `nome_responsavel`, `estado`) VALUES
(1, 'Hospital Sant\'Ana de A-do-Barbas', 'Rua da Capela, 2405-001, Leiria, Portugal', '+351 244 123 456', 'contacto@hospital-santana.pt', 'Dr. Manuel Ferreira', 1),
(2, 'Hospital Beata Aline de Luanda', 'Avenida da Esperança, 3100-285, Pombal, Portugal', '+351 236 987 654', 'geral@hospital-beataaline.pt', 'Dra. Catarina Silva', 1),
(3, 'Hospital Madre Juliana de Campo Grande', 'Largo dos Milagres, 2430-014, Marinha Grande, Portugal', '+351 244 321 789', 'geral@hospital-madrejuliana.pt', 'Dr. Ricardo Mendes', 1),
(4, 'Hospital Nossa Senhora de Lenice', 'Travessa da Saúde, 2430-020, Batalha, Portugal', '+351 244 654 321', 'contacto@hospital-nslenice.pt', 'Dra. Ana Beatriz Lopes', 1),
(5, 'Hospital Irmã Rebeca de Recife', 'Estrada dos Anjos, 2480-169, Porto de Mós, Portugal', '+351 244 852 963', 'contacto@hospital-irmarebeca.pt', 'Dr. João Pereira', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transfusoes`
--

CREATE TABLE `transfusoes` (
    `id` int NOT NULL,
    `id_bolsa` int NOT NULL,
    `n_utente` int NOT NULL,
    `data` date NOT NULL,
    `id_hospital` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transfusoes`
--

INSERT INTO `transfusoes` (`id`, `id_bolsa`, `n_utente`, `data`, `id_hospital`) VALUES
(1, 1, 234567890, '2018-07-01', 1),
(2, 3, 345678901, '2017-03-15', 3),
(3, 5, 456789012, '2016-08-02', 4),
(4, 6, 567890123, '2021-05-02', 2),
(5, 9, 678901234, '2022-08-15', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bolsas_sangue`
--
ALTER TABLE `bolsas_sangue`
    ADD PRIMARY KEY (`id`),
    ADD KEY `id_dador` (`id_dador`);

--
-- Indexes for table `dadores`
--
ALTER TABLE `dadores`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `n_utente` (`n_utente`);

--
-- Indexes for table `doacoes`
--
ALTER TABLE `doacoes`
    ADD PRIMARY KEY (`id`),
    ADD KEY `id_dador` (`id_dador`);

--
-- Indexes for table `exames`
--
ALTER TABLE `exames`
    ADD PRIMARY KEY (`id`),
    ADD KEY `id_bolsa` (`id_bolsa`);

--
-- Indexes for table `hospitais`
--
ALTER TABLE `hospitais`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfusoes`
--
ALTER TABLE `transfusoes`
    ADD PRIMARY KEY (`id`),
    ADD KEY `id_bolsa` (`id_bolsa`),
    ADD KEY `id_hospital` (`id_hospital`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bolsas_sangue`
--
ALTER TABLE `bolsas_sangue`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dadores`
--
ALTER TABLE `dadores`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `doacoes`
--
ALTER TABLE `doacoes`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `exames`
--
ALTER TABLE `exames`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hospitais`
--
ALTER TABLE `hospitais`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transfusoes`
--
ALTER TABLE `transfusoes`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bolsas_sangue`
--
ALTER TABLE `bolsas_sangue`
    ADD CONSTRAINT `bolsas_sangue_ibfk_1` FOREIGN KEY (`id_dador`) REFERENCES `dadores` (`id`);

--
-- Constraints for table `doacoes`
--
ALTER TABLE `doacoes`
    ADD CONSTRAINT `doacoes_ibfk_1` FOREIGN KEY (`id_dador`) REFERENCES `dadores` (`id`);

--
-- Constraints for table `exames`
--
ALTER TABLE `exames`
    ADD CONSTRAINT `exames_ibfk_1` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsas_sangue` (`id`);

--
-- Constraints for table `transfusoes`
--
ALTER TABLE `transfusoes`
    ADD CONSTRAINT `transfusoes_ibfk_1` FOREIGN KEY (`id_bolsa`) REFERENCES `bolsas_sangue` (`id`),
    ADD CONSTRAINT `transfusoes_ibfk_2` FOREIGN KEY (`id_hospital`) REFERENCES `hospitais` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;