-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/08/2026 às 04:30
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `wincar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento`
--

CREATE TABLE `agendamento` (
  `id_agendamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `placa` varchar(8) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `status` enum('Pendente','Confirmado','Concluido','Cancelado') DEFAULT 'Pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamento`
--

INSERT INTO `agendamento` (`id_agendamento`, `id_cliente`, `id_servico`, `placa`, `modelo`, `data`, `hora`, `status`, `created_at`) VALUES
(1, 7, 1, 'NEG0244', 'Gol 2.0', '2026-08-06', '17:00:00', 'Cancelado', '2026-08-05 00:53:35'),
(2, 7, 3, 'NEG0244', 'Fiat Uno', '2026-08-08', '14:00:00', 'Cancelado', '2026-08-08 15:59:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin','cliente') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `email`, `telefone`, `senha`, `tipo_usuario`) VALUES
(7, 'Emanuel trueadam', 'emanuelteste@gmail.com', '(11) 99999-9999', '$2y$10$3IxkNX0tDIZboIZiR1suoetg2/6NDowQ8RDaqY.GKBI2vERDhvR8O', 'admin'),
(8, 'Vitor Lindo', 'vitorteste@gmail.com', '(11) 99999-9999', '$2y$10$micIjz58L6eDon.73tzN9.KqtFG6z9ylRyOt3JaoxBUxYlm2UtDem', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico`
--

CREATE TABLE `servico` (
  `id_servico` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` varchar(30) NOT NULL,
  `duracao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servico`
--

INSERT INTO `servico` (`id_servico`, `nome`, `descricao`, `preco`, `duracao`) VALUES
(1, 'Lavagem Simples', 'Lavagem externa do veículo.', '40.00', 60),
(2, 'Lavagem Completa', 'Lavagem interna e externa.', '80.00', 120),
(3, 'Polimento', 'Polimento da pintura do veículo.', '250.00', 240),
(4, 'Lavagem de Motor', 'Limpeza do compartimento do motor.', '70.00', 90);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamento`
--
ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`id_agendamento`),
  ADD UNIQUE KEY `data` (`data`,`hora`),
  ADD KEY `fk_cliente` (`id_cliente`),
  ADD KEY `fk_servico` (`id_servico`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `servico`
--
ALTER TABLE `servico`
  ADD PRIMARY KEY (`id_servico`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamento`
--
ALTER TABLE `agendamento`
  MODIFY `id_agendamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `servico`
--
ALTER TABLE `servico`
  MODIFY `id_servico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamento`
--
ALTER TABLE `agendamento`
  ADD CONSTRAINT `fk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id_servico`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
