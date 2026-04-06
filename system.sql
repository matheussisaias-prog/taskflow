-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/04/2026 às 00:40
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
-- Banco de dados: `system`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('nota','email','ligacao','reuniao','visita','whatsapp') DEFAULT 'nota',
  `descricao` text NOT NULL,
  `data_atendimento` datetime DEFAULT current_timestamp(),
  `proximo_contato` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(160) NOT NULL,
  `cnpj` varchar(20) DEFAULT '',
  `email` varchar(120) DEFAULT '',
  `telefone` varchar(30) DEFAULT '',
  `endereco` varchar(250) DEFAULT '',
  `cidade` varchar(80) DEFAULT '',
  `estado` varchar(2) DEFAULT '',
  `contato` varchar(120) DEFAULT '',
  `segmento` varchar(80) DEFAULT '',
  `ativo` tinyint(1) DEFAULT 1,
  `notas` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresas`
--

INSERT INTO `empresas` (`id`, `nome`, `cnpj`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `contato`, `segmento`, `ativo`, `notas`, `created_at`) VALUES
(1, 'Raw', '00.000.000/0000-00', 'empresa@raw.com', '(11) 99999-9999', 'rua', 'cidade', 'uf', 'Matheus', 'Indústria', 1, '', '2026-04-01 21:01:21');

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro`
--

CREATE TABLE `financeiro` (
  `id` int(10) UNSIGNED NOT NULL,
  `proposta_id` int(10) UNSIGNED DEFAULT NULL,
  `os_id` int(10) UNSIGNED DEFAULT NULL,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `descricao` varchar(250) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `categoria` varchar(80) DEFAULT '',
  `valor` decimal(14,2) NOT NULL,
  `data_vencimento` date DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('pendente','pago','atrasado','cancelado') DEFAULT 'pendente',
  `forma_pagamento` enum('boleto','pix','ted','cartao','dinheiro','outro') DEFAULT 'pix',
  `observacao` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `financeiro`
--

INSERT INTO `financeiro` (`id`, `proposta_id`, `os_id`, `empresa_id`, `usuario_id`, `descricao`, `tipo`, `categoria`, `valor`, `data_vencimento`, `data_pagamento`, `status`, `forma_pagamento`, `observacao`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 1, 'PIX ENVIADO — CASSIO RAFAEL GOMES VIEIRA', 'despesa', 'Importado Itaú', 50.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(2, NULL, NULL, NULL, 1, 'PIX ENVIADO — CASSIO RAFAEL GOMES VIEIRA', 'despesa', 'Importado Itaú', 650.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(3, NULL, NULL, NULL, 1, 'PIX RECEBIDO TAMER S13/03 — TAMER SANCHO IMOVEIS', 'receita', 'Importado Itaú', 1300.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(4, NULL, NULL, NULL, 1, 'PIX RECEBIDO MUNIQUE13/03 — MUNIQUE HELEN MENDES CORREIA', 'receita', 'Importado Itaú', 250.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(5, NULL, NULL, NULL, 1, 'PIX ENVIADO — JOSE SAMUEL SOARES NETO', 'despesa', 'Importado Itaú', 165.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(6, NULL, NULL, NULL, 1, 'PIX ENVIADO — SAMUEL VICTOR PIMENTEL BARBOSA', 'despesa', 'Importado Itaú', 500.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(7, NULL, NULL, NULL, 1, 'RENDIMENTOS REND PAGO APLIC AUT MAIS', 'receita', 'Importado Itaú', 0.05, '2026-03-12', '2026-03-12', 'pago', 'outro', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(8, NULL, NULL, NULL, 1, 'PAGAMENTOS PIX QR-CODE — FREITAS VAREJO', 'despesa', 'Importado Itaú', 237.85, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(9, NULL, NULL, NULL, 1, 'PAGAMENTOS PIX QR-CODE — MARTA ABREU DA SILVA', 'despesa', 'Importado Itaú', 52.60, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(10, NULL, NULL, NULL, 1, 'PIX ENVIADO — JOSE ELIMAR DE SOUSA', 'despesa', 'Importado Itaú', 1256.67, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 13:13:10', '2026-04-01 13:13:10'),
(12, NULL, NULL, NULL, 1, 'PIX ENVIADO — CASSIO RAFAEL GOMES VIEIRA', 'despesa', 'Importado Itaú', 50.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(13, NULL, NULL, NULL, 1, 'PIX ENVIADO — CASSIO RAFAEL GOMES VIEIRA', 'despesa', 'Importado Itaú', 650.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(14, NULL, NULL, NULL, 1, 'PIX RECEBIDO TAMER S13/03 — TAMER SANCHO IMOVEIS', 'receita', 'Importado Itaú', 1300.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(15, NULL, NULL, NULL, 1, 'PIX RECEBIDO MUNIQUE13/03 — MUNIQUE HELEN MENDES CORREIA', 'receita', 'Importado Itaú', 250.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(16, NULL, NULL, NULL, 1, 'PIX ENVIADO — JOSE SAMUEL SOARES NETO', 'despesa', 'Importado Itaú', 165.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(17, NULL, NULL, NULL, 1, 'PIX ENVIADO — SAMUEL VICTOR PIMENTEL BARBOSA', 'despesa', 'Importado Itaú', 500.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(18, NULL, NULL, NULL, 1, 'RENDIMENTOS REND PAGO APLIC AUT MAIS', 'receita', 'Importado Itaú', 0.05, '2026-03-12', '2026-03-12', 'pago', 'outro', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(19, NULL, NULL, NULL, 1, 'PAGAMENTOS PIX QR-CODE — FREITAS VAREJO', 'despesa', 'Importado Itaú', 237.85, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(20, NULL, NULL, NULL, 1, 'PAGAMENTOS PIX QR-CODE — MARTA ABREU DA SILVA', 'despesa', 'Importado Itaú', 52.60, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(21, NULL, NULL, NULL, 1, 'PIX ENVIADO — JOSE ELIMAR DE SOUSA', 'despesa', 'Importado Itaú', 1256.67, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-01 18:45:11', '2026-04-01 18:45:11'),
(22, NULL, NULL, 1, 1, 'aaaaaa', 'receita', 'Serviços / OS', 50.00, NULL, '2026-04-05', 'pago', 'pix', '', '2026-04-05 19:36:53', '2026-04-05 19:37:28'),
(23, NULL, NULL, 1, 1, 'aaaaaa', 'despesa', 'Serviços / OS', 60.00, NULL, '2026-04-05', 'pago', 'pix', '', '2026-04-05 19:37:16', '2026-04-05 19:37:24'),
(24, NULL, NULL, NULL, 1, 'PIX ENVIADO — CASSIO RAFAEL GOMES VIEIRA', 'despesa', 'Importado Itaú', 50.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(25, NULL, NULL, NULL, 1, 'PIX ENVIADO — CASSIO RAFAEL GOMES VIEIRA', 'despesa', 'Importado Itaú', 650.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(26, NULL, NULL, NULL, 1, 'PIX RECEBIDO TAMER S13/03 — TAMER SANCHO IMOVEIS', 'receita', 'Importado Itaú', 1300.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(27, NULL, NULL, NULL, 1, 'PIX RECEBIDO MUNIQUE13/03 — MUNIQUE HELEN MENDES CORREIA', 'receita', 'Importado Itaú', 250.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(28, NULL, NULL, NULL, 1, 'PIX ENVIADO — JOSE SAMUEL SOARES NETO', 'despesa', 'Importado Itaú', 165.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(29, NULL, NULL, NULL, 1, 'PIX ENVIADO — SAMUEL VICTOR PIMENTEL BARBOSA', 'despesa', 'Importado Itaú', 500.00, '2026-03-13', '2026-03-13', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(30, NULL, NULL, NULL, 1, 'RENDIMENTOS REND PAGO APLIC AUT MAIS', 'receita', 'Importado Itaú', 0.05, '2026-03-12', '2026-03-12', 'pago', 'outro', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(31, NULL, NULL, NULL, 1, 'PAGAMENTOS PIX QR-CODE — FREITAS VAREJO', 'despesa', 'Importado Itaú', 237.85, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(32, NULL, NULL, NULL, 1, 'PAGAMENTOS PIX QR-CODE — MARTA ABREU DA SILVA', 'despesa', 'Importado Itaú', 52.60, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39'),
(33, NULL, NULL, NULL, 1, 'PIX ENVIADO — JOSE ELIMAR DE SOUSA', 'despesa', 'Importado Itaú', 1256.67, '2026-03-12', '2026-03-12', 'pago', 'pix', 'Importado via extrato Itaú', '2026-04-05 19:37:39', '2026-04-05 19:37:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `empresa_nome` varchar(160) DEFAULT '',
  `email` varchar(120) DEFAULT '',
  `telefone` varchar(30) DEFAULT '',
  `cargo` varchar(80) DEFAULT '',
  `origem` enum('site','indicacao','linkedin','evento','ligacao','email','outro') DEFAULT 'outro',
  `responsavel_id` int(10) UNSIGNED DEFAULT NULL,
  `etapa` enum('lead_recebido','contato_realizado','proposta_enviada','negociacao','fechado','perdido') DEFAULT 'lead_recebido',
  `temperatura` enum('frio','morno','quente') DEFAULT 'morno',
  `valor_estimado` decimal(14,2) DEFAULT 0.00,
  `data_follow_up` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `motivo_perda` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `leads`
--

INSERT INTO `leads` (`id`, `nome`, `empresa_id`, `empresa_nome`, `email`, `telefone`, `cargo`, `origem`, `responsavel_id`, `etapa`, `temperatura`, `valor_estimado`, `data_follow_up`, `notas`, `motivo_perda`, `created_at`, `updated_at`) VALUES
(1, 'sssss', NULL, 'aaaaaa', '', '', '', 'site', 1, 'proposta_enviada', 'morno', 700.00, NULL, '', '', '2026-04-05 19:36:31', '2026-04-05 19:36:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `acao` varchar(30) DEFAULT '',
  `tabela` varchar(50) DEFAULT '',
  `registro_id` int(10) UNSIGNED DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `tabela`, `registro_id`, `descricao`, `ip`, `created_at`) VALUES
(1, 1, 'CREATE', 'financeiro', 0, 'Importação Itaú: 10 lançamentos', '::1', '2026-04-01 13:13:10'),
(2, 1, 'CREATE', 'financeiro', 11, 'Lançamento despesa: aaaaaaa', '::1', '2026-04-01 13:13:42'),
(3, 1, 'CREATE', 'financeiro', 0, 'Importação Itaú: 10 lançamentos', '::1', '2026-04-01 18:45:11'),
(4, 1, 'CREATE', 'empresas', 1, 'Empresa Raw criada', '::1', '2026-04-01 21:01:21'),
(5, 1, 'CREATE', 'propostas', 1, 'Proposta PROP-2026-0001 criada', '::1', '2026-04-01 21:08:07'),
(6, 1, 'CREATE', 'propostas', 2, 'Proposta PROP-2026-0002 criada', '::1', '2026-04-01 21:10:27'),
(7, 1, 'CREATE', 'leads', 1, 'Lead sssss criado', '::1', '2026-04-05 19:36:31'),
(8, 1, 'UPDATE', 'leads', 1, 'Etapa → contato_realizado', '::1', '2026-04-05 19:36:34'),
(9, 1, 'UPDATE', 'leads', 1, 'Etapa → proposta_enviada', '::1', '2026-04-05 19:36:36'),
(10, 1, 'CREATE', 'financeiro', 22, 'Lançamento receita: aaaaaa', '::1', '2026-04-05 19:36:53'),
(11, 1, 'CREATE', 'financeiro', 23, 'Lançamento despesa: aaaaaa', '::1', '2026-04-05 19:37:16'),
(12, 1, 'UPDATE', 'financeiro', 23, 'Lançamento #23 marcado como pago', '::1', '2026-04-05 19:37:24'),
(13, 1, 'UPDATE', 'financeiro', 22, 'Lançamento #22 marcado como pago', '::1', '2026-04-05 19:37:28'),
(14, 1, 'CREATE', 'financeiro', 0, 'Importação Itaú: 10 lançamentos', '::1', '2026-04-05 19:37:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `metas`
--

CREATE TABLE `metas` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `mes` tinyint(3) UNSIGNED NOT NULL,
  `ano` smallint(5) UNSIGNED NOT NULL,
  `meta_leads` int(10) UNSIGNED DEFAULT 0,
  `meta_valor` decimal(14,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `titulo` varchar(160) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `tipo` enum('info','sucesso','alerta','erro') DEFAULT 'info',
  `lida` tinyint(1) DEFAULT 0,
  `link` varchar(250) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico`
--

CREATE TABLE `ordens_servico` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(30) NOT NULL,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `empresa_cnpj` varchar(20) DEFAULT '',
  `contato_empresa` varchar(120) DEFAULT '',
  `email_contato` varchar(120) DEFAULT '',
  `telefone_contato` varchar(30) DEFAULT '',
  `proposta_id` int(10) UNSIGNED DEFAULT NULL,
  `proposta_ref` varchar(30) DEFAULT '',
  `num_contrato` varchar(60) DEFAULT '',
  `responsavel_id` int(10) UNSIGNED DEFAULT NULL,
  `titulo` varchar(250) NOT NULL,
  `tipo_servico` varchar(120) DEFAULT '',
  `descricao` text DEFAULT NULL,
  `prioridade` enum('baixa','media','alta','urgente') DEFAULT 'media',
  `status` enum('aberta','em_andamento','aguardando','concluida','cancelada') DEFAULT 'aberta',
  `data_abertura` date DEFAULT curdate(),
  `data_prazo` date DEFAULT NULL,
  `data_conclusao` date DEFAULT NULL,
  `horas_estimadas` decimal(8,2) DEFAULT NULL,
  `horas_realizadas` decimal(8,2) DEFAULT NULL,
  `valor_hora` decimal(10,2) DEFAULT 0.00,
  `local_execucao` enum('remoto','presencial','misto') DEFAULT 'remoto',
  `endereco` varchar(250) DEFAULT '',
  `custo_mao_obra` decimal(12,2) DEFAULT 0.00,
  `custo_materiais` decimal(12,2) DEFAULT 0.00,
  `custo_deslocamento` decimal(12,2) DEFAULT 0.00,
  `custo_equipamentos` decimal(12,2) DEFAULT 0.00,
  `custo_terceiros` decimal(12,2) DEFAULT 0.00,
  `custo_software` decimal(12,2) DEFAULT 0.00,
  `custo_alimentacao` decimal(12,2) DEFAULT 0.00,
  `custo_hospedagem` decimal(12,2) DEFAULT 0.00,
  `custo_outros` decimal(12,2) DEFAULT 0.00,
  `desconto_pct` decimal(5,2) DEFAULT 0.00,
  `desconto_fixo` decimal(12,2) DEFAULT 0.00,
  `motivo_desconto` varchar(250) DEFAULT '',
  `iss_pct` decimal(5,2) DEFAULT 0.00,
  `pis_cofins_pct` decimal(5,2) DEFAULT 0.00,
  `csll_pct` decimal(5,2) DEFAULT 0.00,
  `irpj_pct` decimal(5,2) DEFAULT 0.00,
  `outros_impostos_pct` decimal(5,2) DEFAULT 0.00,
  `margem_lucro_pct` decimal(5,2) DEFAULT 0.00,
  `valor_total` decimal(14,2) GENERATED ALWAYS AS (`custo_mao_obra` + `custo_materiais` + `custo_deslocamento` + `custo_equipamentos` + `custo_terceiros` + `custo_software` + `custo_alimentacao` + `custo_hospedagem` + `custo_outros`) STORED,
  `forma_cobranca` enum('unico','mensal','etapas','hora') DEFAULT 'unico',
  `condicoes_pagamento` varchar(250) DEFAULT '',
  `data_vencimento` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `garantia` varchar(250) DEFAULT '',
  `financeiro_gerado` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `propostas`
--

CREATE TABLE `propostas` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(30) NOT NULL,
  `lead_id` int(10) UNSIGNED DEFAULT NULL,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `empresa_nome` varchar(160) DEFAULT '',
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `titulo` varchar(250) NOT NULL,
  `servico` varchar(150) DEFAULT '',
  `escopo` text DEFAULT NULL,
  `valor` decimal(14,2) DEFAULT 0.00,
  `prazo` varchar(120) DEFAULT '',
  `condicoes` text DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `status` enum('rascunho','enviada','em_negociacao','aprovada','rejeitada','cancelada') DEFAULT 'rascunho',
  `email_enviado` tinyint(1) DEFAULT 0,
  `data_envio` datetime DEFAULT NULL,
  `data_aprovacao` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `extra_data` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `propostas`
--

INSERT INTO `propostas` (`id`, `numero`, `lead_id`, `empresa_id`, `empresa_nome`, `usuario_id`, `titulo`, `servico`, `escopo`, `valor`, `prazo`, `condicoes`, `validade`, `status`, `email_enviado`, `data_envio`, `data_aprovacao`, `notas`, `extra_data`, `created_at`, `updated_at`) VALUES
(1, 'PROP-2026-0001', NULL, NULL, 'Raw', 1, 'TItulo', '', '', 0.00, '', '', '2026-04-02', 'rascunho', 0, NULL, NULL, '', '{\"cliente_nome\":\"Raw\",\"cliente_cnpj\":\"00.000.000\\/0000-00\",\"cliente_end\":\"endereco\",\"cliente_contato\":\"resp\",\"local_exec\":\"Fortaleza\\/CE\",\"data_prop\":\"2026-04-02\",\"introducao\":\"\",\"objetivo\":\"\",\"objetivos_esp\":\"\",\"metodologias\":[],\"etapas_exec\":[{\"titulo\":\"ETAPA 1 – Trabalho de Campo\",\"desc\":\"Quarenta horas de trabalho de campo, para a realização do Levantamento da Fauna Silvestre por meio de dez metodologias distintas, sendo 02 metodologias distintas para cada grupo faunístico (ictiofauna, herpetofauna, avifauna e mastofauna terrestre e alada), contendo a classificação taxonômica indicando a distribuição geográfica das espécies, casos de endemismo e status de conservação das espécies.\\n\\nTempo previsto para a realização da Etapa: 10 dias úteis, conforme termo de referência do IBAMA.\"},{\"titulo\":\"ETAPA 2 – Elaboração do Relatório\",\"desc\":\"Elaboração do relatório das atividades de campo contendo indicação dos métodos, técnicas e critérios para sua identificação, registro fotográfico, quantificação e interpretação, incluindo descrição e justificativa detalhadas das metodologias adotadas para cada grupo faunístico e seus respectivos indicadores estatísticos, com base em referências técnico-científicas.\"}],\"cronograma\":[],\"equipe_desc\":\"\",\"equipe_membros\":[{\"cargo\":\"Coordenador Técnico\",\"funcao\":\"responsável pela supervisão geral das atividades, validação de metodologias e entrega dos produtos finais\"},{\"cargo\":\"Biólogos Especialistas\",\"funcao\":\"responsáveis pelo levantamento, identificação e manejo da fauna e flora, bem como pela elaboração de relatórios\"},{\"cargo\":\"Médico Veterinário de Fauna Silvestre\",\"funcao\":\"responsável pelo manejo, atendimento clínico, avaliação sanitária e encaminhamento de espécimes\"},{\"cargo\":\"Auxiliares de Campo\",\"funcao\":\"capacitados para apoio em captura, transporte e instalação de equipamentos de monitoramento\"},{\"cargo\":\"Técnicos em Geoprocessamento\",\"funcao\":\"responsáveis pelo georreferenciamento e organização espacial dos dados obtidos\"}],\"logistica\":\"\",\"qualidade\":\"\",\"condicoes\":\"\",\"obrig_contratante\":\"\",\"observacoes\":\"\",\"grupos\":[{\"nome\":\"\",\"itens\":[{\"desc\":\"\",\"qtd\":\"1\",\"valor\":0}]}],\"fotos\":[]}', '2026-04-01 21:08:07', '2026-04-01 21:08:07');
INSERT INTO `propostas` (`id`, `numero`, `lead_id`, `empresa_id`, `empresa_nome`, `usuario_id`, `titulo`, `servico`, `escopo`, `valor`, `prazo`, `condicoes`, `validade`, `status`, `email_enviado`, `data_envio`, `data_aprovacao`, `notas`, `extra_data`, `created_at`, `updated_at`) VALUES
(2, 'PROP-2026-0002', NULL, NULL, 'aaaaaaaaaa', 1, 'Titulo', 'aaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaa', 0.00, '3 dias', 'aaaaaaaaa', '2026-04-02', 'rascunho', 0, NULL, NULL, '', '{\"cliente_nome\":\"aaaaaaaaaa\",\"cliente_cnpj\":\"00.000.000\\/0000-00\",\"cliente_end\":\"aaaaaaaaaaaa\",\"cliente_contato\":\"aaaaaaaaaaaaaaaaa\",\"local_exec\":\"Fortaleza\\/CE\",\"data_prop\":\"2026-04-02\",\"introducao\":\"aaaaaaaaaa\",\"objetivo\":\"aaaaaaaaaaaaaaaaaaaaaa\",\"objetivos_esp\":\"aaaaaaaaaaaaaaaaa\",\"metodologias\":[{\"titulo\":\"aaaaaaaaaaaaaaaaaaaaaaaa\",\"itens\":\"aaaaaaaaaaaaaaaaaaaa\\n\\naaaaaaaaaaaaaaaa\\n\\naaaaaaaaaaaaaaaaaaaaaaaa\"}],\"etapas_exec\":[{\"titulo\":\"ETAPA 1 – Trabalho de Campo\",\"desc\":\"Quarenta horas de trabalho de campo, para a realização do Levantamento da Fauna Silvestre por meio de dez metodologias distintas, sendo 02 metodologias distintas para cada grupo faunístico (ictiofauna, herpetofauna, avifauna e mastofauna terrestre e alada), contendo a classificação taxonômica indicando a distribuição geográfica das espécies, casos de endemismo e status de conservação das espécies.\\n\\nTempo previsto para a realização da Etapa: 10 dias úteis, conforme termo de referência do IBAMA.\"},{\"titulo\":\"ETAPA 2 – Elaboração do Relatório\",\"desc\":\"Elaboração do relatório das atividades de campo contendo indicação dos métodos, técnicas e critérios para sua identificação, registro fotográfico, quantificação e interpretação, incluindo descrição e justificativa detalhadas das metodologias adotadas para cada grupo faunístico e seus respectivos indicadores estatísticos, com base em referências técnico-científicas.\"}],\"cronograma\":[{\"etapa\":\"1\",\"atividade\":\"aaaaaa\",\"frequencia\":\"\",\"periodo\":\"1 a 2\"},{\"etapa\":\"2\",\"atividade\":\"aaaaaaaaa\",\"frequencia\":\"\",\"periodo\":\"2 a 3\"}],\"equipe_desc\":\"\",\"equipe_membros\":[{\"cargo\":\"Coordenador Técnico\",\"funcao\":\"responsável pela supervisão geral das atividades, validação de metodologias e entrega dos produtos finais\"},{\"cargo\":\"Biólogos Especialistas\",\"funcao\":\"responsáveis pelo levantamento, identificação e manejo da fauna e flora, bem como pela elaboração de relatórios\"},{\"cargo\":\"Médico Veterinário de Fauna Silvestre\",\"funcao\":\"responsável pelo manejo, atendimento clínico, avaliação sanitária e encaminhamento de espécimes\"},{\"cargo\":\"Auxiliares de Campo\",\"funcao\":\"capacitados para apoio em captura, transporte e instalação de equipamentos de monitoramento\"},{\"cargo\":\"Técnicos em Geoprocessamento\",\"funcao\":\"responsáveis pelo georreferenciamento e organização espacial dos dados obtidos\"}],\"logistica\":\"aaaaaa\",\"qualidade\":\"aaaaaaaa\",\"condicoes\":\"aaaaaaaaa\",\"obrig_contratante\":\"aaaaaaaa\",\"observacoes\":\"aaaaaaa\",\"grupos\":[{\"nome\":\"aaaaaaaaaaaaaaaaa\",\"itens\":[{\"desc\":\"aaaaaaa\",\"qtd\":\"1\",\"valor\":50},{\"desc\":\"aaaaa\",\"qtd\":\"3\",\"valor\":50}]},{\"nome\":\"aaaaaaa\",\"itens\":[{\"desc\":\"aaaaaaaaaa\",\"qtd\":\"1\",\"valor\":50}]}],\"fotos\":[{\"legenda\":\"\",\"base64\":\"data:image\\/jpeg;base64,\\/9j\\/4AAQSkZJRgABAQAAAQABAAD\\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\\/wgARCADEAVgDASIAAhEBAxEB\\/8QAGgABAAIDAQAAAAAAAAAAAAAAAAQFAgMGAf\\/EABcBAQEBAQAAAAAAAAAAAAAAAAABAgP\\/2gAMAwEAAhADEAAAAr0AAAAAAAAAAAAAAAAAAAAAAAAAAAAADz2EmWMJ0zNQhNzrp8b48bWTUJU1C1k6fx13m24zoAAAAAAAAAAAAYxqrcsOuA1HnseWXuzh5BuCOZ1k3LNrsp4uJdRb89AoAAAAAAAAAAHldug7yG8vFcZ3Whi4eG4aoJu2RuoxrP056CgAAAAAAAAAAAGjZV3OPh1yx8rT2bF6jGoFR03LGeMZvPvgX9ryXU8t7BmhQAAAAAAAAAADz2GmmOdcteFdZ75j0EsqUctIkscbjfUPXIWLGuS9n7RTedsFeWwQZ0AoAAAAAAAAxTVW5aumcoenRqPEiWX0GOfLQKA53osE45v0dcBXtpVbC2eS5d0o5aBQAAAAAAAGraSHFtlUy5FNYyEAoAAEeuuSUy5VTLkVtj6gFAAAAAAAAAAAAAAAAAAAAAAAAAAAA1qDO56DD3mVvs62FZ02qFWS3e2sgWdO0x82RrpZmpaY89mXasyi588qpbLdyvU1qwo5SWe+g3Fi56wq0xi0UdcqbSXDPn2pc51sKOj018MvNtTNWaJWrbVJAZY7ze0kyFL0ejfEzYuWjfqWdRb08srRIjmyxrLOIlPPr9SVlLziRzd3QVNsq3w03FPLN0THeV1lCujVUdBQC+wpwT61ZS62JVftzrZPx35uwSua6XFInk4c3j0nupQx5VyQavpsZaOP0Waa9UpLzEi9x1Oc2dF4UGV75FHMsclh0vTYnOdGyIdbe+Rys29alXX9Ilyob7yOd96D2yh0dFkU8Hp\\/ChsZvq+iUAADz0AAAAAAAAAAAAAAAAAAP\\/\\/aAAwDAQACAAMAAAAh8888888888888888888888888888887NCJnPD608888888888886iBSLBFamS888888888884gl1XhJ2u8888888888888\\/BRmwNda+8888888888887Ch4lZ89s7y7888888888pmZeW88K\\/wDXUN\\/PPPPPPPPPTPPvPPLH\\/PD\\/ADzzzzzzzzzzzzzzzzzzzzzzzzzzzhdxlJWmGX++R+OEP9FP+zzuG4EmTYq9P0pRLbXYAXgjx5vmOQPHGWcAQg6Hwq+uvTzzyDzzzzzzzzzzzzzzzzzzz\\/\\/aAAwDAQACAAMAAAAQ888888884wwwwwww888888888888880CP7his14888888888888Ct+yO+5eZP8888888888889bOVdpk2EM88888888888JdsSGthmW888888888888weMoYvU9Sy+E8888888885hLO7c8D5somE8888888889SCTc888cGOMc8888884w0448084w044040w00088LstkZfhcLAQK5VSc1PCGwRnLgqBE1M78lXqP69YulpAjkR4eLtdpFILVOAaHXXThA888g888888888888888888\\/\\/xAApEQACAgIBAgUEAwEAAAAAAAABAgADESESEzEEEBQiMCBAQVEjMjNC\\/9oACAECAQE\\/APgz9GR9kicjiL4dJ0EltaKMSvwyhdz06T06S+jHb5u+hKa+ImNRiF2ZWhduTdvIzqoD3hsQ\\/mW4z7e3y+Hp\\/wCj5GMTa+B2gGBiDcvsC6EG5j5aKy5z+JgDQmJ4i3j7R3nhrB28hL6yDmD5UUudRFCjAg7y2wKsJLHJinjsSuwOPJ05DBnp2zqemsjqVOD8WMnEprCL5M4Ubljlz51WFDFbkMzE7yxwg7x25HPxZI2J6iztmdez9xrXYb+lbGGgZ1n\\/AHOs8axm7\\/JiY+x6B4cpWgbZMFaMcKYtQ2SdCCtCcAwKS2J06wcMYKRyG9QpWPzEXk2BLK+JgrQLljDUCMqY1aKcExKkcnBjIVbBgoy+IEQ6B3FqXGWMdawNHyrXkcQ8jYVHaKnEMDE5Z13hXjWc95T\\/AKRT\\/LLv7mUhg3u7QvV+tylNcgIysyb7whCg5GYUJ7TLWrDe4SsjJ4if6AfuMSr5xF6bHAG5hVQ847VY0PKt+JzOo37nqMnYnURMFRuLdjPLeYbVByBORBzDah2RuC\\/YhsTH9dw2kDC6i2kDcdw4AxBYVXAjXIx2J1dnAiOUOp1\\/dmdVRsDcFw44IzHdT+PuP\\/\\/EACQRAAICAQMEAwEBAAAAAAAAAAECABESECFBAxMwMSAiUUAy\\/9oACAEDAQE\\/APFZ+NedmoTuGdxojEwu1zNoHbmI9nfzeo7WdALhOIoQaBSZg0S+fL1Hsba\\/5Fz3oib+d3I2GvTWzZnUStPcRuPMzBYxJ0VbMAoQi4y1opred0TuCK1+Imo7WdALiqANXUMIRRrUAxRQ8RnbBnbECKPiUBM7YnbECgf3B\\/tGavUyYGFoWYS9rgZqudwgTJiIWoRGJ9zNrgc3vMmMLkQG5nYhZgIXOwEVm50Y0JtU9kGGuZtlOoNpxEH1j+toA3JjnfGBqMGQbaWxbeKG4jBoTjALEIYD3CSSKihudGXKYD8gT8gVid4en+TAnmEbVMDwYema9zFqmFtZhQRVIMKkmBCL3hUnmMuUHTIHuYGvcKH9gU\\/0f\\/\\/EAD0QAAEDAgIFCAoBBAEFAAAAAAEAAgMEEQUSExQVITEQIDJBUVNhoSIzNEBCUmJxgZE1IyRDcjBQYHCx4f\\/aAAgBAQABPwL\\/ALnvZOrgHbm3Wv8A0ea176Fr30LX\\/o81FV6R4blspZhEN61\\/6PNa99Hmte+jzWv\\/AEea1\\/6PNbUjbI1rxa6vf32rn\\/xt\\/POp2aKLSPUshkfm5ssoibcp7i9xcetYRXZxq8h9IcD75UTaJn1Lib8zcN5VHFrL9IfVN81VTZzlb0RzZpmxDx7E95kdd3I1xjcHNNiFQVYq4b\\/GOI96e8MbmKkkMjyTzL23pgdXVGhb6vrKme2niEMfNnqRFuG9ybhlXUNEm70u1bGqvo\\/a2NVfR+1sWq+j9rDsPqKaozvcA3sHX71Uz6R1hwHMvYXKllfUSCOPr3KCJuH0lvjKLi43PHmT1fws\\/awyiNTLpX9Bvn79Vz29BvMJDRcqoqDKcreCwyiFPHrEvSI3eCmlMr79XVyyTMi4nf2KaodLu4BUlM6rnyDh1lQxNhjDGDcPfaibRN8Sibm\\/K5wY254KeoMx+lYVQ6Z+mk6A4eKxaV8MLS0ejfeted8oWvP+UJ9ZI4btyvfeeTBCzV3W6d9\\/vr3hjLlSyGR+Y8skjY25nKad0p8OxUdI6rmDR0esqNjY2BrRYBSxtljLHi4KrKV1LMWHh1Hm0lS6lnEg\\/IUMzZ4hIzgfe+CqZtI6w6I5ZZmwt38exSSuldcpjDJIGN6RVFSNpYQ0dLrPLXUgq4C34uop7HRuLHCxHNwyu1eTRv8AVu8kN496q5\\/gb+eWepEQsN7k9znuu48mE0Oij00g9M+XNxah0jTPGPTHEdvOocSfoxEeI4XWuydgWvSdgWvSdgUdW98gbb3Wom0TPEom5vyT1dvRZ+0d55MKodNJpnj0Bw8efilDoJNKz1bvLmglrgQoZRKy\\/Xy0sGRuY9I+6PeGNuVLIZX3TnhjbuU1UZNw3N5aKlNXOGDcOsqONsTAxo3DnyxNljcxw3FVdM6kmLDw6jzYZTE+6a4ObcKlgzHOeHus0WlZa9lqP1qTBtIbunK2C3vitgjvitgjviqWljpI8jB9z\\/w1dJHVx5X\\/AIK2CO+K2CO+K2CO+K2CO+K2CO+KgwrQ\\/wCUlvYmtDRYf+D5Z4ofWPDVFPHMLxuDuR8rIxd7gPuto0vehR1kEpsyVpPJJURRdORoW0aXvQo6iGXoSA8slVBF05GhbRpe9CjmjlF2PBUlVBE7K+QNPYtfpe+atfpe+ag4OaCOBT5GxtzPNgo6qGZ1o5A48klXBE7K+QArX6XvmptbTPNhK1Zha\\/Utfpe+atfpe+ao6uCV2VkgJRrqYGxlbdBwcLjgrqOqgldlZICU+RkYu9wA8VtGl70KOsglNmStJT5WRtzPNgtfpe+atfpe+aop45vVvDuZi0pmrdG34dywabR1Loj8ScbNJ7E0SYnWlpd\\/8QwFlt8zvwFXUOoljmvzXQrDsgTDpWsqOgdiGaR0lvFbBZ3zlVUb8Pka4P8AsVSy6amZJ2hYpUOp6QlvSO5UWGurWmQvsLrYLLeucos9DiWQHgbHxWMfyH4CZgYcwO03FbBHfeSjZo4ms+UWWOTf02w9u8qjkNNWRuKG8LFv5AoYEC0HTeSqcH0ELpBJmy9Swudz6SaJ3wjcqOl1uoMeay2CO+8lSYVqk4l0mbwU4vUyfdYNV5maBx3jgn+rd9lg\\/t34Ve59XiOhvuvYIYC22+d1\\/BV1BqORzX3uqiUy4I15VDhwrY3Oz5bLYI74\\/pUNDqbXelmvyzyCKBzz1BYbEamv0juA9JVzTR4npB25k54fSF462rBfbj\\/ryVFJHVACQcFiMLKfDNHHwBWB+xn\\/AG5Md9TH91hJvh8axz2Zn3WC+w\\/nkkw+CSbSuHpLGN1f+AmY4xrGt0R3DtQx2Mn1R\\/aBu26nJrcUt1XssZp8mjlb9lh0+sUbD1jcViv8gm4hShoGlHBVuI05pntY\\/M4hYSw6Cod1ZVRVQpKgvIutvR90f2qLE21kxYGW3XVOL4rY\\/MqqJ2H1+ZnC92qOYT0ukb1hYP7f+FXtfSYjprbibhDHW23w+a2nR1JDZoz+ViIY3DP6Q9DwWH4i2ijc0szXK29H3R\\/ao6sVkWcC3Ljc2WnEXW5YfXxUcbg5ji49YWI1sVY1uVhDh2rDZtNh74zxYsH9v\\/HJX1ZpIg8NvvVTUGrwkyWtvWB+yO\\/25MdP9KMeKwoWw+NY4P7Vp8Vgh\\/sz\\/tyVeJup6vQhl1i2+vH2Cjw+lMTSYhwWzqXuQq6YU9G4+FgqGojpqgyyNLuyyrMUgqqd0ejdfqWCTZZXRH4t6xb+QQwNhF9IVW4Tq1OZWvvZYVLmoZo\\/lCwuFk1WWyNBFls6l7lqipIIHZo2BpVN\\/Lj\\/AHWIUoqqYj4hvasNqtA90Em4H\\/2sH9u\\/CfXxTVWrSQX323rZlIf8QWKUENPDpI929Zi7Ad\\/UbLCKWGeF5kYHWK2dSdy1RQxwtyxtyjlrXOrMSygGwOUbkMLpLb4QnYXSlhtEAVhzjT15jdf0vRKkinoKsva3r3Fbcm7oKprJ6+zcn6CjojsnQHpEKCoqcOLm5Dv7QtuTd0FLLU4lI0ZPJQR6GBkfyhV9PrNK5g6XUqeqqKDM3Jx7Uccm7oKNs1fXCRzTx7FjG6v4dQTcZna0N0Y3eC23Ud2P0sWqTKyFtj0Q4qjwyA0rDLGC4rZdJ3IVQw0GIgsBsDcLEjnrw4X32TOg37LE\\/wCPl+ywj1NTu+FU1Q+kmL2tufstt1Hd+SoMSmqakRuZYfZU19rjj0+zkxijySadg3HjZYP7dwPBV8EtPXaZrSRe4KGOTAeqCqKyorwGZPwFPAafBQwjeqSvlo2FrGXv4LbdR3Y\\/Sw6sfVtcXttblyN42HLkbe9hdEA8VWUk1RiOXR2j7VFAyJga1o3chY08QCtBFf1bUGgcAOUxtPFoK0EQ+Bv6QaBwCyNPEBaNnyhaNvyhZGn4RylgPEBZG\\/KOQi\\/FZAOAC0bPlC0bPlCDAOACyNvwHIW34oMA4AKwPEIwRn\\/G1CNo4NARF+IWjZ8oWjZ8oQaBwFv+m\\/\\/EACwQAQACAQMDAwMEAgMAAAAAAAEAESExQWEQUXEggfFAkaGxwdHwMOFQYHD\\/2gAIAQEAAT8h\\/wCzoCuhGxQN76K\\/yl\\/l0A67b3LfrdCaoXleVuknkn7gAJkfrdZPL0rWsNCpSzxHa0249N\\/M7EXnMU+AZtyX9WWDVpFUTa+hewRtOXHOfKx6c7ytIv8AW7cdL6CsYN04Ob6pkuCMl48dd8xAU0EIWgycI+qK8Ho3thT\\/ABk5E2WZ\\/d\\/pP7v9J\\/Uv4mNAUpB9QtFz5u\\/QhMAbwU2lSt4Mw3Huxi9rqtW7Erv34NatfnAAAKD62lvOr13jlKJow7e8RBsDsjrwDqZ9oi\\/2YwZj1UaSn4VfWktcURlq9b50JRTA0O8Ws7Z3S3Xg4deqkfZilYr36Uan1oZNQRH7HHVClVLmtbYxFD2iUtlRBUCpJqc8949KG4MdwjSWf1agVwTKn73W+a2kNH8HaHqq0QGMs9566MDntMuIOk9LGzoeUQCaP1VGbPVqSdoquMpslN2OOz06Ohi2eoqY6rceiAam1F+lHBxRHWbnfiHfK6cIlFbeihleHdA9KWI6MZwyZ5elwqSVto1Om9TEf7H0jPSIge\\/4l0KJ82nVcKc9olZo6PXY2Omfugj00LpuQm1jMW4afSndUJrCPaJePtPgJ8NMv7c\\/dKF\\/w0gyaGpPhJ8NPhp8dPhJdrwSAQoP\\/DxRsNLicINKdK8XlEXXnBAJcD\\/M9O\\/C29LiFKdrmepUpPDN1K261DB2FjG5T1WYpJdHRKf7PSCLy7XEc5hdzN0IBL+xKXXZCD2tGIBVwTheZOVxKJtS4LBcqZ771iB1ANa9GuQxHMrnXbzOLVzVgKl6CbouCOwWULhGDzWD50jzQGrZVmPf8Ev\\/AFuHE4K2IDS0e00H8tysdNf4IA6V4EN+eaWqYXpObGY7pt4JSJ10QOaGhOGIBPMNh3olwGSHwuW0iqKKpm3Xm6nNgrTQrCUALzwQO\\/vmdI\\/WxhoAW0afaEZolYdxjo26MvBtqq6UIhm9ujpF3oZmshVeYAGigmm7ZPfW6NYRWStVYn3grlXTX8olrbHTafd6WW53dzBdiTcwBAg1GoEO8uUPrr8Q6mgL1+J2B\\/ejprtX6zIZpDuUQENVboHviKvOlD0IseuS4NcIsSEMWa47S3nd4ZreU1TEaY5r4hOgHZgj0A2bIo7Kw9KLHka6005c+I8A8w7+100jJrKvaOqPedAYXpVx9llaisuz6F5ZnkZcTgTce4+lIIxm48ze+MpqblLi04lz6\\/KjhoPclBVZTWGW8xgOYFZ3oh3Vg6QEnfHtFdrPX2gWWsM+MlxJxZPyEAZ+uRHmWZ2j9fOTTQ6kICFWFXrNabKMuAC+kVsdg6OkcVwgMcmHrUw3owcwbCN2oSRYjAgu37QnTjgltwx95hXLJ+hMUbjaNKjjAInLnlHbdK0EUUfYmeSCuAEDhTW1D5UK1Sz+ZNY4GG6QNbWrGKLAaovHaUFRZpPwkzrgOReRxFh6GVOL90FOotwNBqdCcQw0YXUGe0TuFISgNk0x70NY0bO0lYg7yp80g6bsY6hNLu9dfzEqGYD7QQXio6VAxAVpKn5FCaz8E0deCVKn5sCDLCjRgiy1PJPi58fGpaVxK6J2ryRfVPaVBNCztC1SvifFT4uJWJ4IFYu710A0B8xexPBHSDErfsT8GRBNAnM+Lnxc0O8PWw\\/4X\\/\\/EACoQAQACAQMCBAcBAQEAAAAAAAEAESExQVFhcRCBkaEgQLHB0fDx4VAw\\/9oACAEBAAE\\/EP8AvXL6y+ss5mstPC+pL6y+pL6nrL+erDAtb2iD1UYb9oJ09X+J0v77Tpf32lv2\\/EciXWV7TWtoOrFFNfH8T9n+J0n67ToP12lq5cfxH7J0CwdXiYyxYm8v5m4oayyU4ddD6R11ZrrKOZRzDNiDuoB1IxcrBvRNg4ly5ceHx66\\/iK5UvXQ4hKDa9LdyUuoPzDHSI17Im\\/6CsscngQ1ahrKhPkl37R7qsLW6at638F7dKy36xe3NhoIXS7sa8EByP4iuoGVpy7MNPmEzcpslpyymIrhx4iqbIWLKyy7kuiQA5wz4AeARTx8Vt1P4jlqCt5TqVKcGjri3f1Y6kRRdtA5abEAFfb5i4TQaq6ERHzUdXM\\/dPAy9CNyCVWlR2IO46u0rqjurPB2Jc9Nq+BV5aO0MohqN4hrJTFP0ilu5Z4T94lbYUAaECVKJRADT5dZcNgxOnSffwowuCDHVXEV3i0WyoC4Mi1Pu\\/eLHF+hJTAlxB2Jlj8CLzHuwp0cOwYKQoVq9WBK+asgs7LH7xybS1Xwu\\/ONK4t6sukso\\/qm0q0vM7EDa9kwcPK6gpucz+hG5ltV5fOdYhssb5s4mgCaTVKK+8vb5yucbOsVJM4bCLq7TylONoOYjUDRaB1lkahL0B5YagAAqI1Gg53IP7pLT854s02zFrbyxxShziI+0PmWPRRlZnu4Dq5mukxvPPEWVmJHfREGegDd5h4UzdQDmLG4F2lng7RdaC9kx8LkiUz5kUMQsTiHjcuHyayu5YymhRpF0K840Q0B263FQruvtLAMrg5YFJDZM\\/YYBWfCohxCuPCGQ+qfmVWHXt8Ag2azm5aHghveSTh9PwHFCJrjeEPkllVRpB+sauytXeKAnQW7VLDmu2dkVOmVdZ6HVm4uQY\\/BBCjFdIY8Uh50CI7ksWWAHldp19PCpUZGuxPpDlRQcTzHHnokpwX07xAwgqzIgUQ+SY+hvvEHWzRw4laJ6W5jS7vc90ONpVaaRalCtZwd3SEwIAK85XxHmYsNOsuLUqxpljk0ZfhgRq+e8Grba5jmBvFeNCCLnMXRYFY8oFFfJ4NhsQlpaJrCm8aGk8p\\/Lz+bgIVDcBDOQ5TzSw+NLw6JB6zTUG9Q76NJ\\/Nz+bn8XALz44wbDTaxVygYqCofKPxEv\\/AM7l\\/M0f8W5cvxuX8F\\/BcuX4XLly\\/G5cuX4dZfhcuX8VwlR52XL3Do1f3lk7EeSVp6wthdg0vT7ytay\\/XOgm\\/SF6aPeE3V7ZPSDcprLV1m1mXDU75nqVa9JW+Qs5pn9ViP8AtCykA3E1nS9miUymx81f+y4KBrXbqY795miDrBcUKrZewOYihJOrB\\/zQ6AKNnEbDCinR6wLJbHIkqaBascB4rr1O3GhMwOasuBEDTAsZHDnSl+DH3mH77NSsJ6S\\/BTWZmGoZvf8ASalU0+O3TSZaq30EozKbMK1X0ghU3FCYyoNQZ27RVhWi7Ft7QG01YheC+I5Rgb1SqdssoqsPMwNljvUw9qrm28aUFpWzVuXRg8WRHUq6GhuSCwUVWmhRpFwadQ7gyjB6aHpz\\/Ogv2nDejg5946liZSjh9yKlYlGDrtdLlqVxamqYslFtbKqdKjc13tpHEBBVNFuZg+2h0sNhDIkzL3EdYqsWqueA7SrfywdpUvvw9SDWqxcLC2vOUhEZpCU1XqUYXU7S7U0uuin2jradV7u\\/xK\\/wIu2o20qEVRcuQ94zPNHNqwPeXQOnwJeT2YzFvD5QFrqo9fBuzqGtk+8AJNQtpdn3WWvlPoeB\\/TbTqsnsRAHOftMANS72jpL3dVGFkpQZCl4CO1LKU0KlA5C1N3\\/Y4MA57CXFNyOh0O56sbq2TDQ\\/CDa6B6P8qEuAtXoQjCNmehEaU5Nlc5K7raekZKxxG7Kta+yIl2tuQagIaJLdn4B3LyZR0i4cGSZYsa\\/mRyLiBhcWRw5VJo1WNzaZj8oiVdHiJCiLTVX+Yhp6eM5bVbbo8EvEPV4jo2l1ldKq2MxUTUvoraXvx1ZsNfRl7qPorheE07R7xYVNmCnSA3o8wRruwcYI+kuL9hFcGGNmzEbITzWBT4V9CeUPKRlplWXIXeHcI8WxJqoSkbJZhDra0Tlg9LiwgBphOXPlGMBFqAjcazQtOwzXkE2DnHwsp1NBTiWU4JvVAvzqCrFaxlyAVl25lI1rRSDGDWOILs\\/ZgiKlE2beekXfhixVVec1E9HqR4QDriDyEOcpw1Fust4WhSpb2cXf3i06gaFMSL9qJklt5YR5L7TDQxKkvL6rCtBC23LzrHjhDVoa6y764Kq9m66szPd1E4fWDTdKt5dpZNnLNveWm87qJUJcUIrJi8rzzNDm82MvOVYAau2ERp6KQgRpn4QcUBO0l6PnKY7WtmXADGhoAT69JLAlToxewdpsvtUC9ig1Qa95bQLjeYsDpXb+YJRqYOP2MczQkdanSZ\\/pYhRhbqUWvlMPNsyFw4wXWpVvTrNTPclNpAhsF1loxFzateYlw0\\/TY7uOcSiwDZYXZCXjYIOMMGrjCojLU6hF5c3ABGTCorxKhbtuYvp1nWv24j4kiiL1hKlBO9lF3zAc35SmYSYZwXfMpBlVTaJLVAFxqtd4AxgrLG9zRXvPaIGaKD2ytqeAJsOJa4i57WmXJnZAaH0wVLlRqgswfYR\\/xcfTQosOOIVANDTEpvpD603BYoKRgsYIrQx0iQjFKyTiwmgWQLl6mfxsHAWiEoV68N3zKjc+7CwgU63EYTQHCXLKLzWOjWaIkz2LQXMn2M\\/gZaAzrSrhj4tD2YLB86h\\/xP\\/+AAMA\\/9k=\"},{\"legenda\":\"\",\"base64\":\"data:image\\/jpeg;base64,\\/9j\\/4AAQSkZJRgABAQAAAQABAAD\\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\\/wgARCAB+AMQDASIAAhEBAxEB\\/8QAGQABAAMBAQAAAAAAAAAAAAAAAAIDBAEF\\/8QAGAEBAQEBAQAAAAAAAAAAAAAAAAEDAgT\\/2gAMAwEAAhADEAAAAvdAAAAA4RRHRhx02z8vfZaNuAAAAAAAAEJ4+LShr8u+mu16\\/P5Vm9jrIb5BQAAAAAAQwb8ufeTdh0+fXYc9nn6zOLpGkAAAAAAAAc6kyR2s9OUdxc3l\\/NvHUh6cQoAAARJIdJIxSwrWxzo50RSQAAFACJIHPD9vxUsjryEdObYehl00Eba+F9SJPnNC51OtKZx6d5V0XQHatfnr6QFF5Mltwz1bQovFfZiiyY5TeWufRFIlfLS19mKZWAAAAAAAAAAAAAAD\\/9oADAMBAAIAAwAAACHzzzzhikPzzzzzzzzzHdMz7zzzzzzz5YLY\\/Tzzzzzzzz8pYH7zzzzzzXTDx177zTzw3pIk0XwWnUmzz210231zy2ywzzzzzzzzzzzzzzz\\/2gAMAwEAAgADAAAAEPPPPLLmkVPPPPPPPOC1fs1vPPPPPPKrNzL\\/ADzzzzzzyx8yg1bzzzzTjXjzwyz7jTzyIW110HDS6p2zw033132zx0wxzzzzzzzzzzzzzzz\\/xAAlEQABAgUEAgMBAAAAAAAAAAABAAIDBAURIRIgMDETYRAiQFH\\/2gAIAQIBAT8A2lw\\/qnXzMAa2m4UhUWx\\/o\\/B4pyY8EIvUoHzEwA4+0+HqZpKh0h0N4eHdekOuAqqQi+AdKpUVrI9iibZPSE9AvhyHCRcWKdTIJcHgWU\\/OtYzxw+1Tqc5zxEeMIbs7CvE2+QEMdbh8E44Dsvsv+X\\/\\/xAAkEQABAwMFAAIDAAAAAAAAAAABAAIRAwQxEBIgITAFEyJAcf\\/aAAgBAwEBPwDgTGVBzCtG0Kv4uEH+q7sjR7GPEK1pfbUDVcllClACa4h25P8Akg9pYW+Vg8MrAlfIMLqPWcrPSNrWAkt68uwhe1du2VZ2pqP3Pwr28aGljPCeG92J5nQDtHSdO1Og1hRwjWP0v\\/\\/EADcQAAIBAgMFBQUIAgMAAAAAAAECAwARBBIxEBMhMkEFFCJAUTNSYWKxIzA0cXJzgZFCRGCCwf\\/aAAgBAQABPwL7s3p52TVKGM+WklV9D5a4PWioYWNTRbtvhQJU3FQy7xfj5OR8i3p5Gc61GGZwFNAcKmTPGdkLZJB5PFt4guzCL4S23KvpWUenk8T7X+NmF9l5fFJcBvTZhXscp297X0Nd6W+hoeSPGpMLx8NLhnvfShpWJkypl6nZho8z36Dy0mIVdOJosXa9R4dm14ClUIth5Uisi+6KyKOg+9LBRxNqWRH5XBpnVOZgKaRU5mAoTRnR1\\/vZvo\\/fFA30P3nXazBBc7GOVSfSoo37WxLtI5EK9BUvZDRlXwjkMPU12xn7lBvOe\\/G1dscmGqHAYFZVZHuw08WzCRo2HuVB8TfU1NGMP9tELW5h60Jft938ua9TSbuJn9KlxDI8aqmYuKMzRxXlAzE2AXrWfE65Et7vWlxWeOVwOSklnlQPGqBT71LiXkOREAkHNfpQlkWQJKB4tGWmmbebuJbkak6Ct9JGw3qix4ZlpzN38ZcvIbf2KeZowoy5pG6Cs86cWRSPlqSXf4LP82x1zIV9RXZ04wU0uHn8PHgaxPakEAGX7Rj0FdsuZMHCxXKSdK7Y5MNeoMNgIpVaIjP08WzB\\/h\\/+zfU1i2vHuxzvwrlxqj1jsKxrZcK96P4qD9BrEeGWBzyhuP8AVdKRg0eLK6XqH2Ef6RUP4rEfmPpWJ54P3KRX7zOBKVJa9rVMhyASTsQSOFq\\/30\\/bP1FP4cchOhUqPzpiFFzpS8ezeAt4\\/wD3bPg4MT7VAfjUPZuFhbMsfH41iMLFiVCyi4FT4KHEBRIL5dKTsvCxuHVTcfHZ3SMaFh\\/NJBHGbqOPrUkSyizCu6x9bt+ZrdrmDdRwFModbNxFd0j+a3petygDC3BtaAyqANBQQKzMNW1pkD5b\\/wCJuKkhSTm19RS4dFbNxLepqSFJbZtR1owo0eRhcUMLGNczfAmtwm7yW8N7\\/wDDf\\/\\/EACcQAQACAgEDBAICAwAAAAAAAAEAESExYRBBUUBxkaEwgfDxYLHR\\/9oACAEBAAE\\/IfxoGI\\/2PeA7XzB\\/q9IxQ2w1BKgWRMG+peCmHm036M3faXtXtGoEQotsoHerOio7OGGT0VvaGenILrrxficeHojUu0X29PWPu6XrYddFovpwwN0yPRAFM2Ge0r0lOYKpcwjoZDA16RaMw2nNmsr4jNliYPB6UHfxP6yGh8X5b4Y5mE9hYYJL5YENpq2KApejPWcxuCLBOIfjvTrfei+hI6FsSbtDR+\\/ak9Qd0xdTInSZjAlWM8x1L453kgDIynQllRiBCC3slrREOZdp8xniUF+MX7T9aN9u8F6lmVsQYBh4ne\\/u0vxGshrZCg3emgeYCt569QqG0tUTVvstZDUmGh89Obgm77ZtS0Fyr5kuDfNYlENKlEW5I6en3dNAB7wBxx7hTAtKug5yTw\\/4E82s4Wg+2WZdoVmb\\/eNwn8DUOev6s+j\\/ANMrFQaMlH\\/IYEcAWtxwExG\\/sPIilaG2IsoWo8dWwXt3TK4dLuoqlNmYvEeSG5PZFQsUSrR5zAD5G2DLVZEwkx186qath\\/RFhW7GAYt+d1AmppQ9CKIdGS\\/hUeRz+yiojDWBIexWlupaA9gNJCVT8ufmICgNWwmVxDn\\/AA3\\/xAAoEAEAAgIBBAICAQUBAAAAAAABABEhMUFRYXGBQKEQkTAgYLHB0fH\\/2gAIAQEAAT8Q\\/hWpcQNV4GaVcEwmux1IJIL+0HvDW\\/hJEmWA9zBM8McmTqSljJduO0IKJvEIYGIQ18Bal9pQDWh1YsOOA4jge7UdEJpI2wySzocxHo\\/qZYiAR0N7+Ax6Rg3FY69JVOIa0yo8Q1EspmLb6QNv6YKK+BzGNl5Ffc1bEJIbHf5sJY9Ia+BzEjEW8PCYsa+pV5tz5gm4bJoC1Y\\/+F\\/2Z86o1FS1V8PwqxWIidSKow6j\\/ABC3oXeTEJWaywkWhr1+GM+hjn4gwmVQcxITgBwe40PdOO0PoWb5fUqgFzDXxA2ljtaj0D0mYM+EqfqGv4VqVrXa6I+qjpsRgFDUQJPuMvxE7lQAVZxcoTtLHXLcF1DLcv4qUSj+p4qUpYurq\\/zpzxb1WEwxt8AWzHvX7d0fVwkelbX7jaNKHIt0yiWqqGsVBNTIrDOrm3xKgSlNXrTfCh09BxoSzPaPu4i2ctVAYlGzhtCCMszqgDl8XAxQWeTVm4WmbUVOnC\\/Ux1iTmUsg4TUD4MGhS7KSh4uPsC2Lca3ZSeYZziu3G0No5sh4eJC26HlWn9Rp21NEaA251f8A2HTzEdHYad3RNYYKq1ZbdBFuZlcXa1uDAC0chUfqGsSqmvuRI\\/NzJ2L\\/AHdj6g4Rgp76tMRfm5bJtxAkZgUaxUuGaqNL4uLF25I6ob60jt9wZDJXthgMi0PQWX7Jl6V3KdUFo2qntiEYQwOrSe1EcqlS7vFeZmyCNeh5hjCgIDwghqNdxO7jRNRZCJeQ5OqhbYTxFKxwgvhlVHCRfLERQaTBYr2DAukyNATPcopVLY9fhLhgidGB7gIX+sgSjgiKZqv9wA9UBlEG+F7uTUzKcyurVcFpX7WMrZtCdrbmWg2ynqPEGWQAFEO3BM\\/z6dFs+oY0NCwkpkQ2NePWFH0DxWOmIWdAK9ASm4ZV2gH0EtqhS9AS\\/ti5TfnshI7OcN+HEeyZu7HcjigFXUepsM3mJj0Ofc3jZZOTa\\/uB7\\/sz\\/\\/4AAwD\\/2Q==\"},{\"legenda\":\"\",\"base64\":\"data:image\\/jpeg;base64,\\/9j\\/4AAQSkZJRgABAQAAAQABAAD\\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\\/wgARCAC0ANQDASIAAhEBAxEB\\/8QAGgABAAMBAQEAAAAAAAAAAAAAAAIEBQEDBv\\/EABcBAQEBAQAAAAAAAAAAAAAAAAABAgP\\/2gAMAwEAAhADEAAAAt7gAAAAAAAAAAAAdcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACE8S59+U7\\/AExHUpemNWWel0FbwNBnjQVuLaZ\\/Uv5tnJ1L9\\/5jcsuM9jWgz7i+jPJoIJZ\\/LfT\\/ACvXn9J6zcukfmPqfDea9356\\/ZT2cH6BPnt7G06zd7G8y9na2LZd5zp4bFL2zc\\/WzpWVZ7vzdbVqbj1AYe4syO6yyn4acJcv3urMu96lpSucM7z1epn+Gr0y7lni5Vu11Mn20B5Zu0XLsXEBNAAIyEExyMxBMQ7IefZiKQh2QikAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP\\/\\/aAAwDAQACAAMAAAAhc888888888888M888888888888888888888888888888888888888888888888888888886oV25y4ycYy8690+ognOmFhzP8e9PPu5YAnbUVDse88888Ms8c8MM888888888888888888888888888888888888888888888\\/9oADAMBAAIAAwAAABDzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzu3fXLvjf6nvjH6dUyKYQxQybG9Y26w2ExhhSyuy0\\/zzyzwzwzxyxzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz\\/xAAoEQACAgIBAwMDBQAAAAAAAAABAgADBBESExQxIUFSBSKRMkJQYGH\\/2gAIAQIBAT8A\\/o2NT1nC+0soRW4rWT\\/str2wVE0Z213xi0WsOSrsTtbviZ0n5cdes7a74mUUoanscePEycXgilR669Z213xjUuv6l8ztrviZxM+mKNs0LsTsmYmSKmPIb3L8b7erU21mXtUrqHmZWzclY9pkVNdeSDoDyZidPqnp70B5MA3Qo+RmQTZlhPb0EyOD3k2NoCY13Ws3rSqPSPazsWJmzKch6T9k7o\\/EfiLfr9ojZTleI0BHyXewOfIhyXNvV953lmiCAdmDLYKQoA3GybCFHjj4jZTswfQ3Gy2Y7Kj8SvJetiy6G49\\/IEcR+P5r\\/8QAKBEAAgIBAgYABwEAAAAAAAAAAQIAAxEEEhMUITFBUgUiMkJQYGGR\\/9oACAEDAQE\\/AP0bUXcJN3mV3Ow3FwP5Knwu53yJzFXsI19akgnrOZq9hOKm3dnpOYq9hL7mFqovmafU73bcfPSczV7CLajdQc4nM1ewm8T4ifpWKigYxNTpzavy9MSnUHcK7RgzS4ZnsPaaYAUtYR3lFi1UgYyT2E1O\\/hjfjJMJxcT6iUAV6YvKN60gIuSZfVwq8Z6sesWtUAAEwJdQlow3icsPY\\/7Gp3fcYumUNuOSYmnVEKDsZy6ivh+IdJXkEZGJyqkgsScQaZBu\\/veLpkVSvXEXTBRgEyzTq6hW8RadpByfzX\\/\\/xAA6EAABAgQDBQYEAwgDAAAAAAABAgMABBESBRMhIjFBUWEVI0JScZEUU4GhIDAyEDM0YnBygJKxwdH\\/2gAIAQEAAT8C\\/wAD3XUMtlazRIhWNNg7LSiPWO20\\/IP+0JxkKUEhg1On6vzCaCsM4sHnkNhk7R5xM4qmXfU1llVvGsJNyQaUqN35OLv3zAa8KP8AmJGYlpe5TqSpfDTdHa0p8tX+sS+IS8w8G0IVU9ImJ5iWNFq2uQjtmW5Oe0DF5dRACXKnpEzPNSqgld1TyjtmW8rntHbEv5XPaO2Zbyue0S88xMm1B2uRiZxBmVcsWFVpXSO2Zbyue0DF5Ynxj6ROOgSDjiToU6H1jCUXTtfKmsOrDk0pav0ldfpDGJMzDobQldeojtiX8rntHbMtyc9oGMSxPjH0hb7bbWapQs5x2xLcnPaO2Zbk57Q04HWkuDcoV1\\/Y+vMmHF81RLS6ESzaShNbddIym\\/lp9oDaU6pSAfSHL0vqzRt11rEpNS0yLctKHPLGU35E+0Yqu6eUPKAIl5dCJZtJQmoSK6Qe\\/naDctcTTbSZV0ltOiTwjCk1n09ATCkt\\/qWE+pg4hJX2hsq9E74xGz4vYRZsioibJbwiXb4qpGHd1JzT\\/SgjC2w5OprqEgmJqxmUdWEgG2kYW2HJ0VFQkExMuy0qiq0JrwTTfAbdxGZJSgJ9NwjEVUcRLpOwykCJGSbal0qUgFxQqSYym\\/lp9oGgpDptaWeQhAqtI5n8EzKNTSaLGvBQ4RMyTsoanVPBYjDJ5x1eS7taaKj+Jn\\/73PtWJheVLOL5JMYWi+eR\\/LrGLLskSPMQIwooZS9MOaBIpD8y\\/iLuU2k2+X\\/2JLD0SouVtO8+UTBL88uniXQRjCu+baG5KYV3OBpHFxUYKj9656CMYXbKBPmVGGJdDTy2UgrOyKndCcLmHn7plenE11htpDKAhtNEiHe9nVV8Tn\\/f4CKihhzB30qOWQpPCAzioFMz7iMrFvmfcQsTglGw2QXvGTFMW5o+0OsYm+ixwptMS2HmVacVW54poOkSOHPMzSXHALU9Yn2nHpVTbY1MYbJOSy1qdA1FBGJyz0yGw0BQVrrAw1w4dlEhLl90MSuIS1cq0VimLc0faJXDX0TTa3Ei0Gu+J2QmZiaW4lIt4axMSWdJIZBoUUpDMpiEtXKtAPWJmUdmpNtKlDOTqYZlMRlq5VoB6xTFuaPtEp8Rln4ki6ulImMKfzlKboUk13wE4qBSqfrSJUTuYfiSmynD8birLeppF+0geaCvvbOlYuVmW6bqxmd5bTjSLybrRu09YzbrLfGKxm76jarSkXkLCFcdxjPGXf1pC1UcCfrF\\/e2QFKKynTSkZum7xWiCooBKovKVJCvFCF3k9OH5a0XU13GsFFSk3GoizbuuO6kW95d0pGUKk1NSa+kZdCaKIrvjLGzTS3QRlAg8ya1izWpNTGSmh6ptgoqoKuNQKQUVUCTu3QE0WVc4yhaRrvrBRckhRqDFmoJNaQEUVdWppT+lH\\/\\/EACoQAQACAQIFAwMFAQAAAAAAAAEAESExQVFhwdHxcZGxEIGhIDBw4fBA\\/9oACAEBAAE\\/IV\\/6r\\/nXcTxnrhCCeJdosigMP3ARaEJOaLrjnNA0WK7XGXsBy0\\/ZYFwWnN\\/UZXmKBBPEoB9c2miZDODtngneX1BRj3hKobK3PAO88Y7zwDvLmyl101ALrGO54B3hoQeLNumiG+g+ZXvRujrLbm8rm7Q2BC3gKnjHeeCd4QEJ3dMPjIs4vSJOp9s8E7wcUqN1fTPNjJ6RrQjLdvPGo1zMI1AC53sKcyAz6QBsuI4cdd6xhS4O6AfYJXBZR0q9kRpvHtXWOvBGRMEFZl0J+yZHJjSOvxUU26TlV9pzgfeB\\/ZKWiKPt1lV2QQrLiVhIgmOHWXqHoC4KFbooZfogebWvxKjWAur2njUAAADQlK6q\\/iAzoA\\/o0S\\/MRXTP\\/jtLzWy3vRj6p+Z2SttWHrUtWwX7V1nFPrvSKpSF9dviDBL4PyoEq3tvom4r89RA44\\/fxOFrX5v4Jr3J+XpKU2z7GYmXBKjc9IwJbIWXLlCIbQiyWEPtGmPqDBYlMzEbU0wUMD6QN\\/4AfQCPebIQqh7YOSBbWOLZqoDJcu2sXcK4Qob9ekTKvWpnbrNsQN2OKr2gxBa7RnkIPBvmr\\/sxLFUXOgSpcVrpYVFitQKUw0TIOi7kqK1Aoj9AA2zhmkVe6GhIftBuou2wSmv612PwIoqmC3FNDRlzam024kvbJW\\/nS7mWJvg7oaFhMtpiCkHNqukARpUOW0y6OMH+5Zi2YWZ+iY\\/M5QQunf0j1MKc8b7TBrliXqw2hCqrjwiCisBNmVvBancft7mMCphEFLozc50osG0rqt6EUqhA8VVCxIzBx4ypba6OURStDiXDC0ShrSLhnUd5hQ2AgKjaxwxBt2wFenmZS5Z9x1lo5K0qGS3puFaWocv4o\\/\\/EACoQAQABAwMEAQMEAwAAAAAAAAERACExQVFhcYGRoRCxwfAwcOHxIEDR\\/9oACAEBAAE\\/EJng\\/wBoQWn99TvAJNXQN1pIBsLXtf4SCzGNZQNOf1ERglXYChITLYGsI0JakxIBAqIRDiYpSTgpSTE67fo2r7M5Kb9IR1aSvxZ0ZJcrnobtfkv3oUNAMATKjxHVKlnhliZzod34qKQgLIrAeVN72QwBi8p+DUFEnxTJKIpLVeTcaPmraxIgFQymz8RHpS0ZDrCtA4mlkyB4VAbKtJf5PFL2ohSumehSRJJAAst3p3q38EqPxSE0dUM0WVAr3knKabATEkD7+KhmdCYkxPxloG6mPUVK5HAsyfZa\\/IvtVjnRAGg8q5zmlncfo02xCLdycX6ZoUCkiGR8VBjIDbE\\/soMaQCsL+5o8S3zART00xUAIFyy+l4pukI7t\\/wBBRDsmhAbrgKeBU5hcJl4p3mJHCI3BJMqM2kgbVHstJWclbuX21QEkBI2hJ1DRGTT1gE9UpXkOgsCfbtURHZ5fY5pixQiXxLq+3pgpxCtkq5j6qKGKdoJBOIInma\\/IvtQYyACANqTCj9RU8sMXChQRAWjHzawKLTpOpw1MdCvAHndUmoNuyJ3LOeLzSkzG\\/gwUmFHok9xVniPukHsVCDAjpM\\/VBv5zFUqOWKIwcmZB4nbBzUgELhkycecvGKnZKE3PsgowoRhtKI8Gr1EJRqKHpq4AcvzQbLkL6D9QUUdEEIUd82cUPcsK2o0H02rFEwMu66vNS9l4zhQPBQABYCI+QuLA6jpQBmpb+RtNNSgglWOqTXF\\/Pii3tGGxCptlMbV\\/Y0nCASGYZJi+SaYFfjKSwXdiXjyNjLSlIFus9qCsuBAAmXoHeo\\/7X2yuHSiTEh2AMuKX8LmkpFJ3U\\/XBvaJ1b60taghLCyJBS3ZS0BgJgGOUXvW748FgeEWmxgLOhrD\\/ABUJOMxCLGl7PFSrDKODW+K\\/uaPKCxYIm3M0jM9d9ZhnadKDJyBJ7utFiYibWZGNif8AMSBZzdws+qCCCgbEA26zSmyEpZEh6pEFki7BCMc1j8DZqG1sTFEEF5EIFwdNqScoA5wBhO8VFFyTEBQ7Qz2aBkqgs2SjTM0KTMNec9JHRRwCsjAwPL6KksQCTCYzujFSCSjULDpKhy\\/MSFyB43o6TaFUweSXpsJFbQWHrDekuQ7G+MEmyXH9MLySQDcEvPWmhAgchaOKnaBhSCJZxy0oLY8UQszRyIViQFm4hcakACCEshGyxSTYoISQF56UwahnEMQmloKkSDeCDEoGtjxUszEg6BHdjwVGKDOSRRW5mxUqQ4hdZZzF2hCyxRBdH1UshTHCXcRs0BZBCIM5I1x4qVlisBCiSxlhTvSrOIAym8Z\\/N\\/2o\\/\\/4AAwD\\/2Q==\"},{\"legenda\":\"\",\"base64\":\"data:image\\/jpeg;base64,\\/9j\\/4AAQSkZJRgABAQAAAQABAAD\\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\\/wgARCAF4A5gDASIAAhEBAxEB\\/8QAGgABAAIDAQAAAAAAAAAAAAAAAAEFAwQGAv\\/EABkBAQADAQEAAAAAAAAAAAAAAAABAgMEBf\\/aAAwDAQACEAMQAAACvwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACCYw4ItuzWwtZq7IjdYsk0kAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIIxJy4a3Tr07+pjU6YSjQJETBnwJrb7nOZ7ct7ODNblkTAJAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAARFZFs1VCneFdkxtzRNxN+GmXIptfoappXjPtRJHq6o\\/dsOhnFl04CJAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEK+JxV8s\\/SCNAFjXXNufcF+AJhSXXP16cYz7wAmNu55u9txbAvzAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAInzDBSZ9eveFNwATHQ0PRX4pF+UEY+euqSnbIp1gALCvzTlfSa+aAAx5K4xKGS93uT6Y3gAARV73JF9FGL1Qi+UIvlCOwyYcwAB4rNarLzPzmQ6959AAGvo4KgvIpRc+qMdBt8rB2c8p0JtAAAAAAAAAAAAAAAAAAaW7RxprInL0gAESNi9qbbTzwtiCK2s3NPP0QruAA9eZmvRevHvTygkApLvmzQCY7DletRIAAK7nLmnIBb57LIVK2FTNqAAGptcuaoESOp2qe4ABEOZ08mOTa1bQu8O1BzGp0XOE7OtJ2SJAAAAAAAAAAAAAAAAETB45+2qKdgU6wAQILff19jXywmqJ8Qo8Mxn6gRcAAepr0Hvz608oJARyXU8iBM73S0l3EAACDnK\\/NhJy4fR2M8mOscmOrmlugAYSvoffg97u5lOfRJu9Nx3XnoDDmrzm5iUr2h6lG3EV5gpfXkbGtflqAAAAAAAAAAAAAAAAAEVVfu6WfohXcAEJjNNb31E6+WA19jQi1SMvVAAAZ8FhbK0mJ080ADR5m9oxE+U9JY4M6AAGPJpHMJCfPQlC7Ace7AVdqAgnnrTmCc+vZl\\/j0BQT68kdPzNwXYFLdc4V4GT1alN4STt6vVmnYpAAAAAAAAAAAAAAAAAAKbT3dLP0QrsAA3dK0thYSaeeAqbWjrtrjP0QAAkvKq8txehflAA56t2dYevO0nqJ8kenkenj0TT3HPFaEx1nLdajI8SenkenmSfHumKzXSHVZjj3YDj19QE7epJ2Tx7HJ9Tx4BY9DT3Jy2r0fNk2dYOza2yEYjM8eiQAAAAAAAAAAAAAAAitrLyip3yK9IRAJi8pOhvyZBfjA889fUVenwyK9eNkQxspGJk2Jrubib+eiUwA8+sBykZpMDPBhZhhZhi6\\/m+mJ5XqOXNWc8GFnGvOca7OLS50d0w8pa15gudLpT2ADByXZ8yacZxfb9PcGny\\/R0ZhjYF7vY8pHLdVXnOM8Gx0nJ9IRg2cI3tPbJAAAAAAAAAAAAAAABFB0GhXWpFPRACGToKa604AtgAiYCRCRCRCQAAAAAAAAAAAAAABEgiQAAAAAAAAAAiQiQAAAAAAAAAAAAAAAAA8+hR63Q0dO3EKdQI3bXnF+fo3OSp0TnR0Uc8Oic6Oic5mmt9619ieYJAAAIUxZ+tfSLpiyiJrzdxaec3FLdiJgjHV7ptzz3QkgjHqYiy90NsbET5Jw1e+bHumsDaBGPxVFzkpds3wIaBveqK8JB4jDSnQe9HdPQPPqiuz0CGGtLHNrVR0Dz6Hn1VFnNFekeqi2JAAAAAAAAAAAxZUKPV6XRjqqWXFTrCJBIA9zXxO9YW59Cy9rcgTUAAADSwe\\/JZoEgihvaE6AwlL0FHeAgoLDRgy3OPKAUu1o3BU3PsK2yqDY39XaNTU8WJsArcPnYNez0bUEHitjMaF\\/wA7fmQChu6YvADyUV\\/RXoBQ3dFfk0l1QlzmiRUW1QNvboDZuKe4AAAAAAAAAAAAAR5wbKLVuG4RpSLuE0+eyTXVz+yiJTAAAAAAHimvBV7G4IkFbZCqi2Hn0Dx7FbtbAr7AAKq1ABjyCnyWg0d4AKuzkVFuDFlGjvBpe9oAYNffADDmGhvgBqaduKrd2ABGjviNTcGlugAAAAAAAAAAAAAAACAASAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAB\\/\\/9oADAMBAAIAAwAAACHzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzjCzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyhq0U3zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzwE0QP8k8NW9XzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzjAvQapOOS7\\/AH5488888888888888888888888888888888888888888888oLmm+iSdQ+6tmA8880w88848884888w408844w88888888888888888888+ov\\/ircBM+++eg884LU884wRPPJ884Ljc881ht1o888888888888888888xb\\/\\/AO5vCcfvvo6\\/PLArPPLMe4QEHPBI03PIoV9B1VPPPPPPPPPPPPPPPPNDfvvx1\\/Gg\\/vvrK\\/PKwAPPLHywQJPLP1i\\/PFKkxCzHPPPPPPPPPPPPPPPPAZfvvxVPARvvvkKPKI1nPPKMy1CFOBGUK1FNA0UyyPPPPPPPPPPPPPPPPPKHvvvrNPD6vvmFgPKA1lMPPAkeMNMPxHD0VAQ2JQ2OPPPPPPPPPPPPPPPPCdMP\\/nHPPDPsydHPOAFbPDAJRKOLDKNPJGPJEXDBHABPPPPPPPPPPPPPPPAFwff6fPBAPKMFPPPHPPPDDPPPLDPHPPPLHDHDPHHDDPPPPPPPPPPPPPPPPKAWu7Cw0wjS3PPPHJHEHGPMNPALOCEOLKFDLKPOFBKIBFEPBPPPPPPPPPPKE0CP3soS03PPPPIHNLNLLHMPBBKMIONFFPGPBFIBOOMNCEHPPPPPPPPPPPPAcgt4UkPPPPPPHPPDODPNHPJPKFLPPLGJHPPHHBPHDHDPDPPPPPPPPPPPPPLPDTTLPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP\\/aAAwDAQACAAMAAAAQ88888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888okMQ088888888888888888888888888888888888888888888888888888wV22488888888888888888888888888888888888888888888888888xoWryVq0Pv188888888888888888888888888888888888888888888888fR6A7TTaTRw488888888888888888888888888888888888888888888qNZZzQGhO+uA+e884888888088808848088008w08888888888888888886hTDa1IbM+uOuU881BP08sjWNNMd8kM4U882yii9Q88888888888888884VPDDVD8hM+++Jy88R4rc8o6lmz0U88Lr08eytn8iI8888888888888888WSee7qp83A+++3u8o4or88sUd8t48EIwFj0iOkPFP088888888888888887ze+\\/7J8ra+6yuS88QuH884wEp1g8nDrFj8DzeYE\\/c8888888888888888UA++qYo8BU+qguc8sI4Q80xWOY8831xp0C0KBZaVU0888888888888888shss3uvk8BoOD9Tc8ovWd4YoiDl0EX3conf8jaw5X0UE888888888888888+38V\\/xU8N9CSE888sMIAM88IAAUMM8c8Mc84g8sM8cc888888888888888sYVkEO5xWFYqs88oQ8c4o08AQEoY88YskEUcE4wI0g8g8c4o888888888888616CiOtlkA888osMcEkQ4koUkY848s04skgoAccIscgc84o888888888888xwVFhxQ88888swA0AEw4YksI88owsgUkowsAUwIsgQ0gAQ888888888888McM998c888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888\\/8QAKhEAAQMDAgUEAgMAAAAAAAAAAQACEQMQICExBBJAQVATFCJRMmEwcID\\/2gAIAQIBAT8A\\/rqfNF4C9Vq9RvlIRICfXA2RrF2hQ0KJlSm1CEyuZ1TTInyL3Bokp1Uu2UJjZMFCiyEaLE6kwBaqYVGrrBW+3jgnu5RKqVS4ptqAlyNq7obfbZUKk6HxxMKtUJ0W2liYXDDvfiDJixC\\/SpO5XaKZGAGIUKAiMQERgAoUKOkqGAi6XQjYqiIYL1j8sGbofjgNkcBaVOIRv3QwPRBcSdQLlNHZMENF6hl2DBLkBpcWOA\\/iNghgejrTzXCp6vQFn6CUT8pOFJpldrhSjgBlGBsLTc9HX\\/LDhxLr1j8YRuNVw7e+ATrlBBHAYmwuOlrjWV2QRXDDuu9qwJ0CFIlek76RpO+k2mZgqm3lGEonGVOM3m8qbz0lWnzNQMaFHW3DjS8BReB5CrSjVTqiZKpVoEEL3InZe6H0vcj6Xuf0hWLtkJ7+RIndVKIiQixw3CkowuVBrvpMozumsA8nKLQV6IXohCi1BoCBH+iP\\/8QAMhEAAQMCBAQEBQMFAAAAAAAAAQACEQMEBRASMQYgIVETQFBhFBZBQlIVIjJicHGAkf\\/aAAgBAwEBPwD++cSqdnXqfwahg97voVTDrlm7EWEbj1OZ2TGOqHS0SrPh+pUGqqYCtcLtqIGkdU0AGSp6SF0jWFXtaFYRVEq+4fgF9D\\/iex1M6Xjr6jaWdS6fpYFh+E0rUTuVsOivq4t6Dqv1Cdj927Yr5gvO6wzGLmtcCm7YqZ3UweixjC2XDPFb\\/JOa5riD6fa2769QMarCwpWrAAF9ZUdFxDV0WunucguHaWu41dsgJR\\/EriGyFOoKjB0K9\\/TYlYJYChSD3DqVt0W6B3XE1XqymP8AOfDFOA5\\/fOVi9AVrVwP05dIRHIAFCgKAoCOYCI5IUKFCjyeE2vxFwAdggzSNKKCIjosdq67ojtngdLRat9+Su0Gk4KpvmMjyDbKVPMeQZSgj5KesLhuhpY5\\/fkqGGyr6p4lw93vkxup0KxZot2t9uS4Omk5x7J5l+YyPIMtKLY5BmcwjmfJHdYCItByXtUU6DnHsnGTOVhT8S4Y33QAAgZwsaq+FaFHecxkeQDKVOcKDCjI5BDZTkUfJ4IZtQMwsdraLV3ujlgNLxLseyaMgonquI7uXiiCvbMI8gyPIMpU5FFDIoZHyULhytqpFnZbGChlxNUhjGD3OfDr6VN5fUdCGIW5BOoL9Qt\\/yRxC3H3BXWKUG0SWuVesa1QvcvrOcqeSVPKCpzlTkFOUqfJk\\/RYNefDV9J2cmHV+4oZcQVdVxp7Zg9lJ2lSe61ELUVHpwMGVg2Ktqt8KqeoTZU9Ve4Cbmrr17r5XI+9fLB\\/NfLEfevln+tV8Bp0Wan1FUAa\\/S0yPUWOLDqCw\\/H3NhlZUbqlXbLCgY2XTJ9enS6vKvcfpsMUuquryrcmXn1OQqdZ9MywqnjF237l+v3aqYxdP3eqlxUqGXulSPWI\\/1Q\\/\\/EAD4QAAECAwMJBgUEAAUFAAAAAAECAwAEEQUSExAUICExQVFSkRUiMDJTYSNCUHGBM0BgYgYkJZKgNENEcrH\\/2gAIAQEAAT8C\\/wCBESBvhcy2jaoQq0mhHaieUx2qOQwLURvSYTaLJ3wl5texQgH+UuzTTXmVDlpKV+mIW+655lxTSGrWNUNzrze+ohm0EL1L7p94Cq\\/yV19DQqTD9oLc1N6hG3WTU6FPAYm1sHimGX0vJqn+RGJmeDfdRrVC1qcNVnQAKlUECz3SNsdmu8Y7Nd4x2c7zR2a7xh1hbB72i06plV5MS0yl9Ff5ATSJueqcNs\\/nSs5N6Y+wimjah8qdJl1TC7w2b4adDqAofx46onpypw2z9zGwaVlJ86vfStBV6apwGnIP3HMM7D\\/Hp+buDDT5jpmLPRdlgeOidkPqvzCjp1obw3RLO4rKT\\/HJp8MtE74KitRUdvgS6brCRovKuNKPtG8n38CzHKEtnwHXQy2Vq2CO3mOCukdvMcqukdvMcqukSk0ibavo2aZNBC7cl0rIoo09o7eY5VdI7eY5VdI7eY5VdI7eY5VdI7eY5VdI7eY5VdI7eY5VdI7eY4K6Qy6HmkuDYdJ1xLTZWrYINvMV2K6R28xyq6RLWszMuhtNQT7ac1NtyiLzh1GDbsvuCukdvM8iukdvt+mY7fR6RgW+1vbMJtyWJ13h+Ian5d3yuD8wCDs+jHVE49jPf1HgNJvPJTCfKNGfXdllRu8CTVcmR7+BbLlyRNN5yk6jFlt4cin305xeHKOq9oHHj4G8DiYl03JdtPAaVsTd9eboP3ysLwpppzgYSapB0rdXVbbehr5VdIrG2LoiXn5iWVUKKk8DEpOIm276Nu8fRZ97CZ1bT4Mim9NfbStRXwwn38FGpxB94Ts07fX+kjLS9RPGGE3WED+o07YXdkT7mkDJuiVsdt+XS4tSgTHYLPOqOwWOdUdgsc6o7BY51QixGEOBV5RppT80JWXKvm3RUqJUdpymJB3Gk0K0rWXftBXsMtmt4s+kHYBGEjlEOyEu6O82K8Yn5Eya9Wts5bOeLE6ih7qzQwNn0SedxJim4eDZae+tWlaSqzCR4I8yfvDfkGnbK789d5RllE35xof28C3nP02\\/zl2094YTcYQPbwlGgqYtCazqZNP00bNGwnatLb4HRJoKxMKvzTqv7ZbDRV9xfDLbAGYH2MDdkR+s3TmhPkH2+hurw2yrhFaqJ8GzE0l\\/zpTRvTS\\/B+ZP3hvyDTmnMWcdV70y2Om\\/aFeA8C2F354DlGWURiTjSfeBs8K2JzCbwUHvK2xSG2y86hpPzRasqJbBujVTXlslzDn6blDRmV3JZw\\/1gGuvjlsJFJQr5jkMW3MC4lkbduWQaLs+2BsGs\\/RLRXRj7xu8AxKJuy6PtorNEQo1cJ8FAq6ge8J2aT6rjC1cBB2qPE5bBR+qv30zE0vEnHVe+VC1IXfQaERn0360Z9N+sYz6b9Yxn036xix5h951eIu8BovvJYaK1boddL7ynVb42RY0ndSX1+Y7Itlu9JXt4OVpeG8hfBUIVeQlXEaFsOXJBXuYGrIrymLORhySBBWkbVCJy1WWUkIN5yHHFPOFxe05P\\/sWRJlhvFWO+r6JaivKnwUi84ke8IFE00ZxV2XWfCk035tPtp2q5hyC\\/fVAybosZF2RB5tN9eGypXARtUo++UJUod1JMYT3pqjCe9JUYT3pKjCe9JUWIyttlalpoVHRtibxnsBB7qduSSljNzIR8o2wlNxISNgiZbxJZaTwilKjhkOtMWa9jSSTw1aFvL7jbfHQE1MjUH1gQpx1fncUYoPxCUqWaJTU8Iasuad+S4P7RKWS1Lm8rvq+i2mfjp+3gyib00nStNdGLvHwrLRUqXp26ujCEcTlPliURhyrafbTtVy5Z7vuNCxmv8kFEebXF0cBF0cBF0cBF0cBFMtYtKbEtLHmVsjWdu05JCfl5NqhSorO00jtyX5V9I7cl+VfSHSFvLUnYo5bCc87WhbK704E8oyyrWPNJbOwxNWO0lgqaHeEbteRtwsuhwbol3kvsJWnf9GtP\\/qE\\/bwbMRV1R0rTV30p8H2iTawmQNO213ppKeAyspxJhtHEwkUSBp28v4CEcTlMSSMKTaRwHgKUEJJOoCJ2ZM3MlXyjUMms7En8CKK5F9IorkX\\/ALYorkX\\/ALY+4I++WzHMK0E8FaE4vEnXD+MtiovTpVyiDrEWjLmXmzyq1jLY03huGXUe6dYgfRbTHxUHwbLT8Iq46JieXemz7eDKNYswOAjdp2gvEtB05bMSFWggnYnXGIjmHWMRHMOsYiOYdYxEcw6xiI5h1gZLcXemkI4DK2m88ge8JcQAO+nrGIjnHWMRHOOsYiOcdYxEc6esBQV5TXLbM5RObo2nzZEpU4sITtMSsohhhKLoJjCRyCMJHIIwkcoi2WAZO8kaxlCrjiVcCIZXiNJXxGR1VxpSuAitVKP9jlsFHw3F8TktWWziVqPMjWIByAlKgobRriSmBMyyV79+UzDYO2Aq95TA+gWon4YV7+AfKYkUXJdOis0TDqr7qzxPgyDGE1UjWdNZuoJhSr7i1cTl1g6jFTzGKnmMVPMYqeYwCsrSAo1JhoUaSPbJaLmJaLv9TTQ18Y\\/MfmPzH5iwUnDcUTvyTT4lmFOGFuKdcU4vzKyWNJ\\/+Ssf+ujNIDkstJ4QNQpk3RZLuJIp9tWS013JFz3EDZlshNyz0e+QiuqLQls2nVD5VaxlsqazeZuKPcXkmiVKQ0Pm2xiJQpaQq7TyjjDd5p5vVTF2p4H6DON4jCh4FKkDjDQo0ke2i\\/XCNNsZu9yGM3e5DGA9yGM3e5DGA9yGM3e5DGbvchjN3uQxKSilO3nE0pA1atOcC1SjgR5qRmUzT9BUZnNeirpGZzXoq6RmU16CukZnNeirpGZzXoqjM5r0VRmc16KukSsjMGbQVtkJBjdCvKaQ7JzRecOCo1MZlNegqMzmvRVGZzXoq6RmU16KozKa9FUZnNeirpGZzXoqjM5r0VRZbBl5NKValb8lromX3whDZLYjM5r0FdIl7OmHXkhbZSnfCEBCAlOwaJ1ikTUhMJmV3WypJOqkZnNegrpGZTXoK6RYrTzV9LiKDJbLbjsrdaTU1jMpr0VRmU16CukZjNK1YJEMIw2EJ4DLasnnTHd86dkZlNeiYzOa9BXSMym9zKqxJKcVKoxU0XSJltSgFI8ydcfDNSsEKO6kNJW47iL2J8tYH0AisTLeFMEacum9MoEJ2aVBFIpFBFBFIpFIp+9pFIplp+2pFIujh9DtBjEbvDaNOzk1ma8P5YoVETkuWV1HlOlZykpUqp1xiJ4xio5oxU8YxU8YxU8YxUcYxUcYxUcYxUcYxU8YxE80BQVsPiOTTLRotwCEPtu+RYOip1DfnUBCZ2WWaJeSTFYGVSwjzGkZyz6ghKwod010CaCpgzDQ+cQl1C9igdFc4w2aKdSIQ+275Fg6JmWhtcEJebVsWNIKCth0C4hJoVUjGRzCMZvnEVrlUtKdppolQSKkxn0vWmMmEqChVJroFQSKmAq9rGzIVhPmNP27rQcRdIiYl1S6\\/6xt0fycnWOsdcv5jrH5MMyzjxFK0iXYDKdXhE0FYdmnZ+ZzeWN1A2qhqzJdCdabx4mJ6zFNkOydQquyJe\\/gJxPNTXltGezcBtvW6qJezb\\/xJolazuh6zZdxNLt07iIs+Ycl5wybxrwOhbaquMtA+aEWdL4SbyNdOMPA2faaUtKN1W6AdWW2HLkgriYs6RZXJNqcRVRHGLTlEyaA\\/Lkpp7xJOl6UQs7chICanZGO9acwW2TdZTtMN2bLtppcqeJifkc1GcS3dptESMznUslzfvyza8OVcV7RY0o29LKW6L1TFpSDTMuXmaoKfeLJfU\\/JJUvbs0LSmFNoDTWtxcWKstzDzDh72g9IMPrvOJqfvE3LNi1GmG6hJ264FlSvIesJQEJCRsGW1VYlpMNg\\/eEiiQMszMJlmS4owww9aSsV9RDW5MdnS1y7hQ6F2TNpUlRLKt0IVeSDxy22pWC22napVIsqYXLzBlXvxXJaJxp6XYrrrWBs\\/butB1NFCJiSWyap1p8VKSo9wExL2drvO9ISgJFAPDtN4syK1b4sRm5J3969eiYk\\/87a63FbEZXe9b4u6E+sKtluuxuF2rLp1Iqpe4UhmTem5wTcwLoGwaFvL7jSOKoanpdmXSmp1DcIfxbWdCEoUlkbyKQ02GmkoG7JbLxZkyBtVqiyWQzJJ4q25LRNJFyvCLBBzRR3Vy2y5ckFDjEhOy8tJNpWSDThEwt+1BhMoutb1GJaXTLMJbTldcS02Vq2CLPQZh9U6v7JELObW8DuVoy\\/x7ecVuQdFPx\\/8QHgnQtdZenWZYeXfDSAhpKRuGT\\/EBGE2N96JXVLIrwyzxxLVlmuBrFryZIEy150RZs2JuWB+YajCPjW8pXpp\\/ckViYs9K9adRhyWda2pr4KGluHuoMNWYVfqH8Q1LoaFEjxbZSVSCqbosV8LkgjenVkrlWKpIiyTg2g82vUonJMTCJdkuLMWUwt6aXNr\\/GhJJx7YeWdYEWmzmU2iabGrfDDgfZS4N+hP\\/GthpraITLMj\\/tp6aFvIKpUKG4xZ7iXJNBTwyWxMYt2Va1qO2JOXzaWQ3wGW3V1wGxzROyKXpK6lPfSNUWNN4rWCrzoy1ifcM5MplGz3fnMJspCU0S88PsqLUlBKKadSpSte0mGF4rKV8crputqPtFhi+689xOgs0STFjJxJx573poT\\/AMK2GnFeWsJNQDBUEip2Q9\\/qdqpCP0m4SKADKz8a3XT6YhSQoUOyO9ZNpH0VRZPxJiYf3KOr93SsOSbLm1IrC7LT8qjBs10bFCOz3vaOz3\\/6wLMd5hCLKT8yyYRIso+UQEgbvHWgLSUq2GOy5iVexJVYpwgOWmru4bafeJWXdbJW66VqOhO2bjOh5hVx0QHLTSKFpKv7R2e9NOhc453dyBCEBCQlIoBlXW6abYsyRXKl0uGpUYmmBMsKbNNcWXLPyqC26QU7tBuQc7UMyul3doutpdbUhQqDDclOSKjm6wpvlME2k+m7dS17xKWciXN9Rvunao6E1IuzFoNu1GGnJ2c61aOcMkBG8RuyPXy0rDPe3RZ8nm6VKcNXFGpOS0ZQzctcQdcSDTjEqltw1IyzSFOS6kI2mLLlFSbBSvaToTCVLYWhG0iLLk1SjSgvzE6E7JonG7p1HcYbRaMoLguujdC2J6b1OrS2jgIlZVuVbuoH5yq2GkSMmtl551dKrOS0JMTjF3YrcYs2VVKS1xZ11\\/4tv\\/\\/EAC4QAAIBAgUDAwQDAAMBAAAAAAABESExECBBUWFxgfEwkaFAUGCxwdHwcJCg4f\\/aAAgBAQABPyH\\/AKz5\\/wCYZJJ\\/MLoiEwrO2+w9Nx5I0QLvHsLJe7iH+TsmghozsTihW7GDk6FGk4XxiRJXG45pxmR7uEKWU5\\/IpJJHmEMmrmG28vA6lMh4Rzh0I72ReZ2wX4+7jQT\\/APQDa62NxRYqC5bF6hHDOGcY4ZXSVkdySNroga+qFf8AHpFrLGs6gRMttud8qXt2BQyQKlGtcyzZWbjoaNYST+NtCRroAUoe++aFdBldiM0Zq1B3TYTpT8cdipe49jqXySNB3mZbx7LZ5aw0iuJr9kmv3dsmBwK1TZtR1pycFrKhjpIblrVn6HeIvQtXEsq0d3nlh5AVVVRVRmkUxuyEJkIdR5wefHlB5QeUHlB5QcN3FgFlZmqwqWIsk5dR58KwtSecnvJkKFIk3K7h6XuBqs\\/sytB6A+5FvfSxrqgQyya4J+yvBv3JhJ6BbI8NTkxiQjF4dbKBW59Rjc0Czwe5CFRKmmETuCMtVqzScAuO3O5J3w7ndHdHfC690I45VlZGUyq5CSSSweoe6QRqpzL2yrwlmnI3D+QQ5T5RucDua7oQNUwRWUdkX2R7U+nn1H\\/JE94rZeuhpGdj9EjSuegl3ls0TJGzI1QcH\\/ozx0lhRE1MQ9OWkf50f40f40f40TvzTWCIWRlF8qK9WNi5dLeRvNFc0K0WsJoIX5cbI6EzKS2VJPJqjZFRPkp4QAqR\\/Y9MBKZbNcl7E0y8Ioss7sUdAVP4z8AQaYbPwbEohZ+5YIqOwtQvYU9JDrCqVulkc8E5xqHtZGTexDHmrI1OSdtCwY+18A0pjUm9QPivsTFNtEj5e5IzajoVTdnldjpp+ilKOIkK4z\\/6AgWxeCyaPz6nPusaH0vFhVssqxdsO0maISJQL4ct7FiFDqNSfgjp1C75Zp0b9Fa\\/I0G7cVJ6u394UCrqXcsK6CFJb2BKEl9jk10Cs9CzqRSK3ZGTb4Gb2zXO7nSQULm55GNOsa\\/k5LEtHQk+xFMzQV6nRih6DHIyRETeUQsj3oSUJnJenCLL2Ilfpoe1FKXsTKTP4HLNBvoI0RDxZQmghIJYN7ECd\\/U+4joOrEptJIcHPxYTVJVaw8QtHZfZK27X0eV1IvZkZ0cFb0GcYQWVnICsWDcMyn9annkMJmdWwqPkTah4o8QeIOS7DVShqcjsclZlqzpdHKNfgUgwiBCpd\\/0Sm3eDkrIuTvf9GSNLVL7YuzlQJ1H1EmfP6yKyCA86yXH9U94LvfbIiLCt9jofL0YPpU0y9VYFZehc9k1mZvol4zbIWs0zpJrAhWwdhSVJ1kKP+I8MeGPHEFbHosRs6UBXGqS2XmDTbkwUkTTrFZzUxSWwhTnaqFgz\\/Tk4rX5rMdOSJ6inkVGsL87zG4xZNJXqKpH2wkqycG5WTbeue2ETRd0ORlOZm17sW8PnEyzwpc5xthWo3aXo4LAtQzibpqReqWshrfJPMCD+wNNdug1jPG6EYs69\\/DF+htQkiepG6\\/eY8mi\\/ZkXEj0N0R7hltIhokDvnsjvaEoVLTMyoFE4WNEXUZTGMakZpWFRqSHOE0xEJEUrGRrWvGC3MNnjFu\\/SREU9yuI9Kg+kBVxqeFPCnhyGtNmgnKnB6u7XyITb2E\\/7g+VsnvjC4rEDJQ4kU7miehcOQtOijnBuJbcIhM2+EJVORDc\\/XsliWErCcveQQ6L1yyDwVEa5anS+hCQK7zzm7IcwvipyGJ2kev7+K7KESSUhz66WfYkkE0gFnGmGpOpyXuJe4l7iXuK1CjHtg+GyoO6bZPQsm7QXoDdEYsmqIFci6vg6t0Iu3XCk0w4ENRg3CkqlQ6n74IRsqNEUKKYf1g\\/8AogzQolSnoI4Vp3WxV0KfIMX2DoEbrVUE6Zly9BxRlWG3DQWtL0PBnijwZ4s8GeDPFiZE2rcWjOWHLUISYO9eHlpQFPFHihxEVpU6ionBPfRQvNDMXPAHihFPBHgsKeKHF\\/EJpkqKMRcmaK7whIVks1oKShECtkSTcoG\\/OBkxgBm6dVTgnuxXQobXAwCfvWrRPKrUxbOutyNGfmMIaiSVVBhsVUmXdC6zFDEW5WNbC8tqgl3X7CQkM2RdUyK5uDHIkLlZxENiOxxHER2IbENiBHoRXGCCCCCCCCCCMIIIIIwWEZXBBBZGaCCCK4NSQQQJCwgggg6jVyNjlo+xFeBKPsNMzvmltoX5WliepGaNrQ6WyxqyZxzgHDOGcM4JwTgnBOGcYno30F6OpMakse7JleScCc4PBRKXk4ygVWdNxt8HrURSlOStZJMlOCrWKXMoMal8RgnKxnkmCeRRPSSecGTF2MqEhU4zvi2TH8lunFxbTgyNJuRNU\\/MMhKiacp2Fg5phOYc4MkYEkuRpuBCigr4vDYSFJInuJqRUMrTqKv0zvKTGiZ5EqDWuSK7dCv8A9Cu79zv7iu\\/uK7+4ru\\/cru\\/c7vch7+4dFX3idq63NyFSkVvRUxrKrH5uiXqRZmqxkQySVdywo+ZgxipYpbEZV4VURC29FhlldYWGo92JtZa1GUSwy5bFDQriSXArYUO4oIe2mm2wwutVC9KVcHp0IpY2m0i9SaN0x1Gy7lmWh2+dWDsKYaBidqMtnGgTFnowsGJKs0leBlNpvvIsGSSdQI7sOZBVzeYpmLApnBjcSmx8nH6SwYusoouSoJ+uNlNHVj\\/Rw2dhKuyTgxhy1bHwNDabC9hSd1UR0KIfTsijQ5Ibavgo1HwaGmeuC\\/0kGYkiVBF0lgvRU7RApTreZXhTsSqmZLGl3J1+BY19uo4qIG6JRcWhoJTpiup3B230olJxmltSLUKxg0mGwGhGs2C3bAo5dTjvJSe52yTISaMsBK7xe7CJZZ5bbTsrClMX\\/wBipjNJKprQZefrr4xYxS6juIShIWHXcEepYostJKVt2NUSx0kTjBfv6aMIF0Mbvt45rJuiVMW6nc7kMgqdyeSHdQRDp7CHBEEem\\/FnUibX7xM2ITEk4cmITUhNSSKkpJCh4TpMWDcJsUt2RA+ncKNhULJGLq2ENjFNKe4SSUYpsd5jTJSgSJk59uiFarUe+DGPqqyGytdErCqfXBmgYli5V+hMUFoLiE41UCUWicarxDDWexiyTrJDHejYnRC20yORKyqakeKCakqiZV6MQtslGDHVwoL3GNctRoqFarhEb7C931bS5Ey+UGqol4nUaynbBq6+ggU7lOfJRkET6qvJXA2ibdK05GTSRJojXBjtwZuKTmqUMU4qObARyskLBWXFMqSI6SagUVQpO4n6axxdiSjAskAJ1klOVSZop0ukmwxAZZAlCNCrOsFVB2EiqauGqTDg7KJXKkSMTiVgxmiSoqNuTmUyP4SiKRm6cqhrgy3F7BSsbnMwcuFLUTuq3Cwk9ZFBjLTKFJkqFDUHSZMxfUPPBH0UEEZ4WWMIyQssEEelBGaCMkELJGMZIr\\/4\\/f\\/EAC0QAQACAgEDAgUEAwEBAQAAAAEAESExQRBRYXGBIJGhwfGx0eHwQFBgMJCg\\/9oACAEBAAE\\/EP8A5krLly5cvouX\\/wBbUqulf9c7zPC5b+sPL6TLMzPiX4l\\/9VdTMUHdj7Q7NzFekr9oT6a19p+FTBfV2\\/tKksvdftPAAYTIiPm5f\\/RrTGfrLBq5TK9Rqh4lyxz+kPEwNHg49sSiui5Wa3EFkjVZINBEDJZMk0cIDwWP2I\\/YeO5Apg6TU3z7nQ3\\/AM+5BGhbKheYLNVgvL6TzyDv9I0aNrmBjz3JabPyiJGr8kQt0xKrDLExcscOYCUU16Tvt6Qxx7RGxwe8PKw2m3zUOOXvkRSohaz\\/AM+qGIAbrG4cIbHj3faN0euA9pQA3zDtHc1wURSTOax0v8GTFX0SYtp7QlkKLlS\\/EHM9y9+IHeC8KQk9c3ZMe7t\\/zistTFQV1iXkKIUpdBxLWAVrlmbKc+YmCVKILq4HrcLA15lHASgcSo2mMlq3fFSpRKGIWeO0S7Tb9Iq1Jf6Y+kIq2jOordQVl1LZ\\/wCZsqbIKNxzGSEEDjxEwVZtXKXi6j04holkGVQO+ldFSe0S87D1D4UsgGQ9fHmX9Qva9pcVXAqeP+ZqiVM9S3vFQcO\\/rMlm\\/rKhZxOJhjviVPQl5mTBm5x1dN4nae8\\/THwJmFzmVVlYV4hlc0PVzLKh3+K5zx0fQ\\/8AG86YX8bvicfEuLJahjPRcxG7Piuc8TMuX6QXxHof6aiKEYUPMQmzar\\/flDCn1gVMTNxSrhWHjE1LdRDyx3wMMplYjjcsjgJZDb1aerfwU+3QIppLO0dpgdne2UoVxMnxPw3qJnU2z8E\\/sn2luA\\/X9iUjN2UrGncPhpqL\\/QW+kEMKxuGniNm8\\/txP6F9oJ\\/c+k\\/pn2n9M+0\\/pn2n9M+0yAk7\\/AMUH1D3lNOdfDZB7pQDilBP2p\\/QvtGqFSQPuwozbn4mnQs0voQ4ou5faA2P6dp9YQPtO+Hv+0R9GF9oMJebgGXTwT+Sy01cqyVWs5g2WS\\/8AR2qgGV4ir+tWlNxHAhnqqIalbXoqPXWHZ6ZhdkOoupzAyMsPeBsefj3lGdgon6R2DH4XcdBXttaYU1CgS3uxqwa2qNVFnz5hr4UCQUGmlnmoCdWuXluVTaLeAl+WKdnoks\\/imP4pjsmO584l9GunlqXIZ+ifBzEG47vpTzwfJlXFHNc8y\\/LHIAftpg5QB9Fw18DLluaXqfxBEMAnaeRiqgt4AWspEB5tPtA2n1WTOUjVstFUGlqApFClo8WyrgWRyppn\\/R3AEW1HtpgIAt4u3lZdkOrAgUGeEye0H1WQ0TqxMwSzkMeIOjRVPx5G6jiLYH1lr7k2nwu4t1pj9ESZyG4rvM\\/CR9WGVio+tIa+FKF7MyzSVfCNw0SgAqZFMLFCHUaRagoz6T+r+yf0\\/wBk\\/v8A7J\\/d\\/ZDiBkwWN9oAgKCV1sajjsStiCjnkbzLbu08S3uwqDbY2Q7MXWnhr7fAxUJRCPvAW5RY87gnZvtK7sSuasgZAFaqA9BpDTdm6LrUoUoSuYhm2NEGqJ4Vb+0QBpL\\/ANExatHa7Wh6ssod1FBbDodDdxNPeC72D7LDTqxeIVzta9yXXff41lMv2\\/Xi8oIb+F3BW33Yl4Ri7EejDKo4AqGvhc4YRW932ai8EBtD45ILSj9F0qUeZR5hQTD8C9UavSO5UB4ff6y3fFQSpxOJWW+THrDbsnS+Kt+BYhL6R+kca6bPAwPthgPKGSyQ9xl5HvFSecSptRV5pnkQgXSArAFPlL5d\\/Y\\/0SpI5m2HazeL8wFLzMfBx0vJ1HZBdx4Zx1eIgvBdd0BfoSy\\/j5CNcN\\/fniIdDqmYtFdo1qoLLySCvQS9BqKFCd4vDLEv2hr4XQ94YmRn1Ueg8SsQgttaZRSFBM9yZ7keMzdDsmmW+trVviC4fnFAXdM8EBZobp5iVlSzhdKy4kjYOACvvAwYrOO4xzDvwsLxaghrq5agYMUfW1RzuJ+aVpFBUVBQXI80KuiaIneu8KcGJwHf5zWpg3sSrQf02Z4kA\\/wBA9GlLldtX+uYB4lHfPw8MMtwKjnAPMVGlCPNQ18C88JaOnK9wC\\/b41hHILVq94aHY6HV1Uy3Wc9CcsfNaSVVeENPS\\/aF2b1JHB8VBTYQhaEi8BT0ou82cEVNkKpUfo3+8\\/IP7z8g\\/vEBSg5t\\/eEu4zeFLNspWMdXUCS425ax9Y\\/iwC8mIqwg5xHcxBNm\\/0lzn99rZ+kKtkQYIPnKO9V27BzBI3aPUGXmul8JHuKfa1qCFoKlgq6imBwl6sxeMsv1RwlnZIEpY1h9dRaNuxhKLx85bJooGbYvUuHvsQwf6E3KmFcvpZr5dCc9WDuOFnJ9LgnaFQ110Jm+kdTnduWHxqm4xKty+tYmBRBx8CoY6xoK57jA3O6JfeXHxiG9Rn2lPjBdQzBbAz6vQRdIDtiYIbIhRhz0T8Kn4VE8\\/Lv3gsPjsqoa62aYlJoamNgfMl2GkqRqpW+SHMAASzNZDzaoIPSWekHMFVhFxLzfo+0O3RiosOXqGO4NN78RtAeRuAKNAao9pyM75oWRO6OGytt6QAc4Q4e0BNx6Cvc5hkEWcdpkM3\\/oGVWZ5jL9fePjZdtGZinAT7agYFsNdXceoHkQV6ET4Fg3xLJdTGJZhQpg7WMdwMfBSs8ysU+RSycdACMuDzLwqF13z8ZHJdyc4nY0lypQVvDFxkMBrEDSz2z8cn4xH+EwBQAOAqBRRGXyvkrvF0Sp855JZbiGby3FoaqcXAzyu\\/X6zNf8AX7xaCBK\\/KLnaRpLbqZwQ+cexiEGZhru7WW0vS1iSvESzfYTnMfn4jvEYewlxnjqCuBfeXHMZ7BhZhcHEQTFfAHEKMXB1TJ84BXWCerPf\\/R8T6v8AqQ+JlCXMufiA+tzTAx1xJSWlc+k2xcfBiUQ+8WDsL1g1mbPq5hkGePgq74ihO2PNjP26EAuv6IBHCfT4XpdqsPopgmBwroEDsD3aloFIj6wyfE6hTwWmvVizUsW4rl+ZKoXm91KjalWfqCFwE8fxSzP9j0hmf0PSUtO7fqkeG7voqQJr5qj9YVHoe9uqPmJnmG4yy\\/cLSQaUgpIZ18XyVT5Ryx1nMCsrV4O092UQT\\/RPRj1uL+6yaD0+Jlgo8XAoy1L9FlZhrqqtF3SvS2TbU+\\/wMq7i6s8RS1eXZiyHi2BCp5+ClXMpE2TWCMxzdeIqRWCw8QH9t+8\\/Hp+PfvPxb94ZzctaRLOOHvGUjshfNpcApDt6AkDZL2blGUA0\\/eH8b\\/efjf7z8D\\/ef0L7wJdOaH9II024i6Y+I\\/IfZ9bgLDNQAbQnZdLCZUL5NMwPXyM\\/EY0P6WZhdEU1ghENPRsEtA6ALEasD5yMNBQ9\\/SO6t94UpJ7x1EHN5XFVKO5XjMqbe\\/0lEygarIwwnwluYcJvDYRbC4OYpmWJfDDqdO9VCTIxkR94UTvDr1lndeTHH+a6lxSirV29iGAHZMPOYWam+evEDZoLES2FPrzDmGuoLcCw90Kj3MdAvCWSyW9pzqNHMqisngHLDqAZNkripTcprXwAIA7bFByflaTTXac4UfEUj2h4iW0LH5qH8xHGDXm4CtQDuIaoae9LiKu8bhBBY1+8r5xLO8Ko6TT2l63ezcKc\\/Pn5Ofn4a7PVlnahnAIZzB5ihfvxEGBV\\/wBYlITTJe\\/mU29jiTS+806upUcitPzIINMY7PTDpyqVEuecgBGYSIZtqOJYfffr0J1gIewIH1V9It429pagoRsjOlgLr7RxQ8rEFPcmPbEW42LClcBqMK1+wlpIn6AD0X3OIlUruzFn2mTeTx\\/mvRLrwyjS0SetRwIV+igYcyqzDnqF4jbRwIXZg+nwl3AQAuYm4veIufnYPqP8ln5p0Tl+9jb97GoLycveBUMBv40EvhbWXa3gbmfm0eL56H8qnf8Anp+bz8\\/gU3+6OZapTTAE7Kgrc+nmpl2Klp5RtHSd8\\/N5\\/L0\\/K5+Vz86n5vLKi9XNO\\/zu7YKWIvrCI8KyNsn51BHMrgRszA2DivSoK6nUtQGxfKOKmwQO2J+dQXANPKmjxyTN9E+NBbWZUGrAw4hU+agnNaugescmAFO8OlEquLY7S4sZzRYrFT86jgbYl8JAwQCNrKg04FQGUuabGEP1DMxiaCraaekwwqpy8\\/5rHtAimhHcZoRWBpp7yxxD4FFbAfoSgar4QKXBFafKL7EB0J4E8CeHKWov4QJsIZfGi1TL5DWJR2JR2JSUlOx8p7JpxKuXZMCp6syldFOx8pR4lJSUlOx8oh3XylLvmGhfliZjOVmZSeEqZGruGDq5ldFJyrbAlsX0Ci8St8V0UlBV3Ahlvo21hlbOCUjeCFe7HiUtfVAyY5QsGbFNFQbPL\\/mupzBvpS3Gv2gtlw8nmBTcOrqXcWKHrMAnENdWkzKCXiKHwN3qD56Bn\\/malEohFCCmLHIt4KGaEYWJB335m4MSxqr8wSIGr9IOZ9e5+Rn5+fl5+XjyfPj\\/ADMP5OP8nLf3pb91mkD3TIzKO0o+J1iW4HE7wuDtxhlhHcYWbiLZX1vphLefpKjBm1xMjqqi7+UrVyNDTEbsX2OiaFDtUKsGLUQosOFlkk0qaAo1DJHUBCDauIg38mZgGvKHcenQXvfeUBVh3dShI8JhYN5TC2lG+mmJs4MvpKMh2WJp7QO5d45Qu866VYEF1FLLZtdpYoqSjqIsoU6Ku8tDvCiYPW4CWiGdwqmQjmJVvoIGXVu4ADBz0VEtlxUJg82pdmRTlw\\/KebgDE4bv5dFpneQZYCWtCGalb47TEhVWbRotP8V5W0YGgFah4Emx6CAEDSyuYvmoNmJ7zEI5OVVKPN6qD\\/LTP8yV\\/Iyv5GZa+elW++lP8qfkkWQB3VEYDlVQ8MtgkZXM1X\\/4OoiIEp4n2vLoOJa2zPleeZl3s5MjMtpqXys9MM7qX9uD3bA\\/WIUYNsV4xUd0wyVrvuWLCqc8UfWeno3zYYL9oiMJbVNgX7oudmNNXfeAWl2nYjqOF6FDTsgdh2F+sMZEFI\\/N8TSm\\/ViOoSuwfEb7j9DQzgWZK55lwvUmo94GIKAnQgi5oVL+kyonTYrjMZDJZFu+WL9bLO8NSxvu9M0Dcep22NF+TcOj45Xdl18pZTL8umwCk9E3B+jKx4aQy43OMGrW35yuEqF3+syVAHUuVL4LXZuUQtZzwdKJTzC1GAu3BLduqGqDvvTN0PHN63cs4nE\\/1iE3ijv00xubmR1e0rHUt5ujGfWDtwgFszezL7QgAoMf4ubjGkg2kwH0DL4BPAeW44w7QHLrqbmZmBTMtHMBlwd1QG1NWDRESYZA69YaEuCcQUf+DqO3To90PvDurexU8fALpoFxh6w7wlh+kNYjgi3gKjtGldumc6lIFwSqrFwRKGpaLrA9oFtjLoF1Z7wKApRx4hroe0NHxiVCStlfoblJikj7EITQF+Urp7Odoq4cCaJtz0awKOfMuELfpo6JZEKaU89kBxStot47wzS9JZPAmYSwiYNvL84G+l6108EY04TRaa8lRgAIkbyxqAK9ej35iBdhcCcsL+5+8CmuOjFKm1PqAgvXbpwmasObbSvowlFAehXTUVwDmqYc2abv06OyPkoh7bJekYbs7F+kFWCJ3Zi\\/eoJuT3qxBu\\/8W3eB5gr3jtBaRGIkJyKPciqovjfNRqWU8CpYcJh19EU\\/MEG8+sr+mKG6esod3oXcsyDzwQT5cU9YBDHIZYEMbmDcqGD\\/AMHUVBZfcLIGwUovOG6ithhzOEWcXKWeejAbSvWO0eLK1iRpiuHILl9COrWgco6fpPR0Ji8ZhNLYssuio4uEBgyEekAq4dpNr5gUV0q2DAlmn9oBOl4LuCQAHBAorowkWpxiCmAR2ZWWqjgzO4\\/KYrJKsC6aRi+bzGolwSClfWZLo1qknMDEwzMMhF+kA4BY4zy8jAxMA0DviobmqXUUym2j6E5hOEfoppZc4zw3cMOfboqIKik5geYEvefPtM9FYw7\\/ANmOEitgwTslxkQFqaCOxqhy8vHzmNkQ6NLZWpCp5pr5w3ofQQUIh0GUs+Swh7RTsFvvBsuq\\/wAh30HTBMoJxMnpLJb2xUUoPnK5qr+zMa0ejKGt5yuVCh2vEEAXIgIMDtqUDRFX\\/wCjqG0sAxeAby5+RC\\/MA\\/fFx07XZwY1AE4uvp0Ji9XBQ5dMfohr6yWPlzM7IDfaifeCkCgMS1Z6LwgFpL8kq2q8xSKzwgw\\/OciiHYrnfjra1VcrzuADZv8AeZ5z8AhlgCIS6i2j5czaBBApzVXFWcmQ32PEA9+mm6ixqle7vMpydVUc0xVtpi9RqHaFzdr3lAVJmofaU0lLh1RKzjEvqc741BdDVn36e6V24G32loYpGHEpd9+jxER0S24EkLRjmZVXQWVxDCmXXlQmqlDQ4DqV4zxz3buAXEPNFOgIpdZauJjcG91jv6TZlgzFhOotc1BBMmuzUdmqOP8AItc3GuZ6QI1EH8SkodLrcKmJz\\/601AEpdyiUfLog7lEQYpslECuiDuIcsogDXweCV8FKqtSkpa95RKNdEslFVKJR0QSmBLo3AipzK5lEo6VEJSSij2lF30QSmU3NWNSi7607SiUSko6UKPaUy5lEolEANf5D09vg9pUZUfSHSv8A8an\\/\\/gADAP\\/Z\"}]}', '2026-04-01 21:10:27', '2026-04-01 21:10:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `templates`
--

CREATE TABLE `templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `categoria` varchar(80) DEFAULT '',
  `servico` varchar(150) DEFAULT '',
  `objetivo` text DEFAULT NULL,
  `objetivos_esp` text DEFAULT NULL,
  `prazo` varchar(120) DEFAULT '',
  `condicoes` text DEFAULT NULL,
  `obrig_contratante` text DEFAULT NULL,
  `obrig_contratada` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `grupos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`grupos`)),
  `valor_sugerido` decimal(14,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','gerente','comercial','tecnico','financeiro') DEFAULT 'tecnico',
  `telefone` varchar(30) DEFAULT '',
  `cargo` varchar(80) DEFAULT '',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `perfil`, `telefone`, `cargo`, `ativo`, `created_at`) VALUES
(1, 'Administrador', 'admin@terrasystem.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '', 'Administrador', 1, '2026-03-31 16:46:23');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `financeiro`
--
ALTER TABLE `financeiro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposta_id` (`proposta_id`),
  ADD KEY `os_id` (`os_id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `metas`
--
ALTER TABLE `metas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `proposta_id` (`proposta_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices de tabela `propostas`
--
ALTER TABLE `propostas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `financeiro`
--
ALTER TABLE `financeiro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `metas`
--
ALTER TABLE `metas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `propostas`
--
ALTER TABLE `propostas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `atendimentos_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atendimentos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `financeiro`
--
ALTER TABLE `financeiro`
  ADD CONSTRAINT `financeiro_ibfk_1` FOREIGN KEY (`proposta_id`) REFERENCES `propostas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financeiro_ibfk_2` FOREIGN KEY (`os_id`) REFERENCES `ordens_servico` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financeiro_ibfk_3` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financeiro_ibfk_4` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_ibfk_2` FOREIGN KEY (`responsavel_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `metas`
--
ALTER TABLE `metas`
  ADD CONSTRAINT `metas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD CONSTRAINT `ordens_servico_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordens_servico_ibfk_2` FOREIGN KEY (`proposta_id`) REFERENCES `propostas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordens_servico_ibfk_3` FOREIGN KEY (`responsavel_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `propostas`
--
ALTER TABLE `propostas`
  ADD CONSTRAINT `propostas_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `propostas_ibfk_2` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `propostas_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
