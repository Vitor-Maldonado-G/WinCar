-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 28/08/2026 às 18:05
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

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

DROP TABLE IF EXISTS `agendamento`;
CREATE TABLE IF NOT EXISTS `agendamento` (
  `id_agendamento` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_servico` int NOT NULL,
  `placa` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `status` enum('Pendente','Confirmado','Concluido','Cancelado') COLLATE utf8mb4_general_ci DEFAULT 'Pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_agendamento`),
  KEY `fk_cliente` (`id_cliente`),
  KEY `fk_servico` (`id_servico`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamento`
--

INSERT INTO `agendamento` (`id_agendamento`, `id_cliente`, `id_servico`, `placa`, `modelo`, `data`, `hora`, `status`, `created_at`) VALUES
(5, 10, 9, 'BRU6767', 'Ford Mustang 2008', '2026-08-31', '08:00:00', 'Concluido', '2026-08-28 18:00:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_usuario` enum('admin','cliente') COLLATE utf8mb4_general_ci DEFAULT 'cliente',
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `email`, `telefone`, `senha`, `tipo_usuario`) VALUES
(10, 'Bruno Otávio', 'bruno@gmail.com', '(19) 99556-2867', '$2y$10$M0apyH1pSIU7dkAqLKcV/Os4nl7TSLRSdkDBURV8q5ukBt455BWSy', 'cliente'),
(11, 'Perfil ADM', 'adm@gmail.com', '(11) 11111-1111', '$2y$10$19GnBwnlZny.y9oFRt6uQeMMtQk9zVKyvcyxUTGsjB6tAKa05RbFm', 'admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico`
--

DROP TABLE IF EXISTS `servico`;
CREATE TABLE IF NOT EXISTS `servico` (
  `id_servico` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `preco` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `duracao` int NOT NULL,
  `categoria` enum('Simples','Intermediário','Premium') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Simples',
  `imagem` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_servico`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servico`
--

INSERT INTO `servico` (`id_servico`, `nome`, `descricao`, `preco`, `duracao`, `categoria`, `imagem`) VALUES
(1, 'Lavagem Externa', 'Lavagem da carroceria, rodas e pneus.', '35.00', 45, 'Simples', 'lavando1.jpg'),
(2, 'Lavagem + Aspiração', 'Lavagem externa e aspiração do interior.', '50.00', 60, 'Simples', 'lavando1.jpg'),
(3, 'Lavagem Completa', 'Externa + aspiração + limpeza básica do painel e vidros.', '65.00', 90, 'Simples', 'lavando2.jpg'),
(4, 'Lavagem Completa + Higienização Interna', 'Limpeza mais profunda de bancos, carpetes, painel e portas.', '110.00', 150, 'Intermediário', 'lavando2.jpg'),
(5, 'Lavagem + Enceramento', 'Lavagem completa com aplicação de cera para brilho e proteção.', '120.00', 150, 'Intermediário', 'lavando2.jpg'),
(6, 'Lavagem Técnica', 'Limpeza mais detalhada da carroceria, rodas, pneus, caixas de roda e interior.', '140.00', 180, 'Intermediário', 'lavagemmotor1.webp'),
(7, 'Polimento Técnico', 'Tratamento da pintura para recuperar brilho e reduzir riscos e marcas leves.', '220.00', 240, 'Premium', 'polimento1.jpg'),
(8, 'Polimento + Proteção de Pintura', 'Polimento técnico seguido de selante ou proteção cerâmica.', '320.00', 300, 'Premium', 'polimento1.jpg'),
(9, 'Detailing Completo', 'Limpeza e detalhamento minucioso do interior e exterior, incluindo pintura, rodas, vidros, plásticos e acabamento.', '380.00', 360, 'Premium', 'polimento1.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculo`
--

DROP TABLE IF EXISTS `veiculo`;
CREATE TABLE IF NOT EXISTS `veiculo` (
  `id_veiculo` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `placa` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_veiculo`),
  UNIQUE KEY `placa` (`placa`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculo`
--

INSERT INTO `veiculo` (`id_veiculo`, `id_cliente`, `placa`, `modelo`, `created_at`) VALUES
(3, 10, 'BRU6767', 'Ford Mustang 2008', '2026-08-28 18:00:49');

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamento`
--
ALTER TABLE `agendamento`
  ADD CONSTRAINT `fk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id_servico`);

--
-- Restrições para tabelas `veiculo`
--
ALTER TABLE `veiculo`
  ADD CONSTRAINT `veiculo_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
