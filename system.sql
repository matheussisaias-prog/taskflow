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

-- ── Usuário administrador padrão ─────────────────────────
-- Senha padrão: password  (altere após o primeiro login)
INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `perfil`, `telefone`, `cargo`, `ativo`, `created_at`) VALUES
(1, 'Administrador', 'admin@terrasystem.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '', 'Administrador', 1, NOW());

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
