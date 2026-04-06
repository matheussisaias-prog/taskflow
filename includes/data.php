<?php
// ============================================================
// DATA.PHP — Dados mockados do sistema TopoGest
// Substitua este arquivo por consultas ao banco de dados futuramente
// ============================================================

// -----------------------------------------------------------
// CLIENTES
// -----------------------------------------------------------
$clientes = [
    [
        'id' => 1,
        'nome' => 'Construtora Horizonte Ltda',
        'cnpj' => '12.345.678/0001-90',
        'contato' => 'Ricardo Almeida',
        'telefone' => '(85) 99812-3456',
        'email' => 'Ricardo@horizonteltda.com.br',
        'endereco' => 'Av. Washington Soares, 1321 – Edson Queiroz, Fortaleza/CE',
        'criado_em' => '2023-08-15',
    ],
    [
        'id' => 2,
        'nome' => 'Incorporadora Atlântico S.A.',
        'cnpj' => '98.765.432/0001-11',
        'contato' => 'Fernanda Costa',
        'telefone' => '(85) 3244-8800',
        'email' => 'fernanda.costa@atlantico.com.br',
        'endereco' => 'Rua Monsenhor Bruno, 480 – Aldeota, Fortaleza/CE',
        'criado_em' => '2023-10-02',
    ],
    [
        'id' => 3,
        'nome' => 'Prefeitura Municipal de Caucaia',
        'cnpj' => '07.954.529/0001-37',
        'contato' => 'Marcos Oliveira',
        'telefone' => '(85) 3477-2000',
        'email' => 'obras@caucaia.ce.gov.br',
        'endereco' => 'Rua Floriano Peixoto, s/n – Centro, Caucaia/CE',
        'criado_em' => '2024-01-10',
    ],
    [
        'id' => 4,
        'nome' => 'MRV Engenharia e Participações S.A.',
        'cnpj' => '08.343.492/0001-20',
        'contato' => 'Juliana Ferreira',
        'telefone' => '(85) 4020-9090',
        'email' => 'juliana.ferreira@mrv.com.br',
        'endereco' => 'Av. Dom Luís, 500 – Meireles, Fortaleza/CE',
        'criado_em' => '2024-02-20',
    ],
    [
        'id' => 5,
        'nome' => 'Cagece – Cia. de Água e Esgoto do Ceará',
        'cnpj' => '07.040.108/0001-24',
        'contato' => 'Antônio Neto',
        'telefone' => '(85) 3101-1000',
        'email' => 'a.neto@cagece.com.br',
        'endereco' => 'Av. Sargento Hermínio, 1850 – Presidente Kennedy, Fortaleza/CE',
        'criado_em' => '2024-03-05',
    ],
];

// -----------------------------------------------------------
// PROPOSTAS
// -----------------------------------------------------------
$propostas = [
    [
        'id' => 1,
        'cliente_id' => 1,
        'codigo' => 'PROP-2024-001',
        'descricao' => 'Levantamento topográfico planialtimétrico de área de 12.000 m² para implantação de condomínio residencial.',
        'valor' => 18500.00,
        'data' => '2024-01-08',
        'validade' => '2024-02-08',
        'status' => 'aprovada',
        'aprovada_em' => '2024-01-12',
    ],
    [
        'id' => 2,
        'cliente_id' => 2,
        'codigo' => 'PROP-2024-002',
        'descricao' => 'Georreferenciamento de imóvel rural com área de 450 ha para fins de regularização fundiária.',
        'valor' => 42000.00,
        'data' => '2024-01-20',
        'validade' => '2024-02-20',
        'status' => 'aprovada',
        'aprovada_em' => '2024-01-25',
    ],
    [
        'id' => 3,
        'cliente_id' => 3,
        'codigo' => 'PROP-2024-003',
        'descricao' => 'Projeto de locação e nivelamento de rede de drenagem pluvial urbana — 3,2 km de extensão.',
        'valor' => 28000.00,
        'data' => '2024-02-01',
        'validade' => '2024-03-01',
        'status' => 'aprovada',
        'aprovada_em' => '2024-02-10',
    ],
    [
        'id' => 4,
        'cliente_id' => 4,
        'codigo' => 'PROP-2024-004',
        'descricao' => 'Levantamento cadastral de infraestrutura viária e mapeamento de utilidades em área urbana de 80 ha.',
        'valor' => 35000.00,
        'data' => '2024-02-15',
        'validade' => '2024-03-15',
        'status' => 'pendente',
        'aprovada_em' => null,
    ],
    [
        'id' => 5,
        'cliente_id' => 5,
        'codigo' => 'PROP-2024-005',
        'descricao' => 'Levantamento batimétrico e topográfico de bacia de contenção e canal de desvio — sistema de saneamento.',
        'valor' => 22000.00,
        'data' => '2024-03-01',
        'validade' => '2024-04-01',
        'status' => 'recusada',
        'aprovada_em' => null,
    ],
    [
        'id' => 6,
        'cliente_id' => 1,
        'codigo' => 'PROP-2024-006',
        'descricao' => 'Elaboração de planta de situação, implantação e corte longitudinal para aprovação municipal de empreendimento.',
        'valor' => 9800.00,
        'data' => '2024-03-10',
        'validade' => '2024-04-10',
        'status' => 'pendente',
        'aprovada_em' => null,
    ],
    [
        'id' => 7,
        'cliente_id' => 3,
        'codigo' => 'PROP-2024-007',
        'descricao' => 'Demarcação e implantação de marcos de limites para loteamento municipal de 200 lotes.',
        'valor' => 31500.00,
        'data' => '2024-03-18',
        'validade' => '2024-04-18',
        'status' => 'aprovada',
        'aprovada_em' => '2024-03-22',
    ],
    [
        'id' => 8,
        'cliente_id' => 2,
        'codigo' => 'PROP-2024-008',
        'descricao' => 'Monitoramento geodésico de recalques em estrutura de concreto de edifício residencial 18 pavimentos.',
        'valor' => 14200.00,
        'data' => '2024-03-25',
        'validade' => '2024-04-25',
        'status' => 'pendente',
        'aprovada_em' => null,
    ],
];

// -----------------------------------------------------------
// ORDENS DE SERVIÇO
// -----------------------------------------------------------
$ordens_servico = [
    [
        'id' => 1,
        'codigo' => 'OS-2024-001',
        'cliente_id' => 1,
        'proposta_id' => 1,
        'descricao' => 'Levantamento topográfico planialtimétrico – Condomínio Horizonte Beira-Mar',
        'tipo_servico' => 'Levantamento Planialtimétrico',
        'status' => 'finalizada',
        'responsavel' => 'Eng. Carlos Drummond',
        'data_abertura' => '2024-01-15',
        'data_inicio' => '2024-01-18',
        'data_previsao' => '2024-02-10',
        'data_conclusao' => '2024-02-05',
        'observacoes' => 'Serviço concluído antes do prazo. Condições de campo favoráveis.',
    ],
    [
        'id' => 2,
        'codigo' => 'OS-2024-002',
        'cliente_id' => 2,
        'proposta_id' => 2,
        'descricao' => 'Georreferenciamento – Fazenda Santa Clara 450 ha',
        'tipo_servico' => 'Georreferenciamento Rural',
        'status' => 'em execucao',
        'responsavel' => 'Eng. Carlos Drummond',
        'data_abertura' => '2024-01-28',
        'data_inicio' => '2024-02-01',
        'data_previsao' => '2024-04-15',
        'data_conclusao' => null,
        'observacoes' => 'Área de difícil acesso. Necessário veículo 4x4. Acompanhamento INCRA.',
    ],
    [
        'id' => 3,
        'codigo' => 'OS-2024-003',
        'cliente_id' => 3,
        'proposta_id' => 3,
        'descricao' => 'Projeto de drenagem pluvial – Rua das Flores 3,2 km',
        'tipo_servico' => 'Projeto de Drenagem',
        'status' => 'em revisao',
        'responsavel' => 'Eng. Beatriz Santos',
        'data_abertura' => '2024-02-12',
        'data_inicio' => '2024-02-15',
        'data_previsao' => '2024-04-01',
        'data_conclusao' => null,
        'observacoes' => 'Revisão solicitada pela Secretaria de Obras. Ajustes nas cotas do trecho 2.',
    ],
    [
        'id' => 4,
        'codigo' => 'OS-2024-004',
        'cliente_id' => 3,
        'proposta_id' => 7,
        'descricao' => 'Demarcação e implantação de marcos – Loteamento Morada do Sol',
        'tipo_servico' => 'Demarcação de Lotes',
        'status' => 'aberta',
        'responsavel' => 'Eng. Beatriz Santos',
        'data_abertura' => '2024-03-25',
        'data_inicio' => null,
        'data_previsao' => '2024-05-30',
        'data_conclusao' => null,
        'observacoes' => 'Aguardando liberação de acesso ao terreno pela prefeitura.',
    ],
    [
        'id' => 5,
        'codigo' => 'OS-2024-005',
        'cliente_id' => 1,
        'proposta_id' => 1,
        'descricao' => 'Implantação de eixo viário e nivelamento – Acesso Condomínio Horizonte',
        'tipo_servico' => 'Locação e Implantação',
        'status' => 'faturada',
        'responsavel' => 'Eng. Carlos Drummond',
        'data_abertura' => '2023-10-01',
        'data_inicio' => '2023-10-05',
        'data_previsao' => '2023-11-30',
        'data_conclusao' => '2023-11-20',
        'observacoes' => 'OS encerrada. Nota fiscal emitida. Pagamento confirmado.',
    ],
    [
        'id' => 6,
        'codigo' => 'OS-2024-006',
        'cliente_id' => 2,
        'proposta_id' => 2,
        'descricao' => 'Monitoramento de marcos geodésicos – Área Residencial Atlântico Norte',
        'tipo_servico' => 'Monitoramento Geodésico',
        'status' => 'aberta',
        'responsavel' => 'Eng. Carlos Drummond',
        'data_abertura' => '2024-03-28',
        'data_inicio' => null,
        'data_previsao' => '2024-06-30',
        'data_conclusao' => null,
        'observacoes' => 'Início previsto para abril após mobilização de equipamentos.',
    ],
];

// -----------------------------------------------------------
// EQUIPE POR OS
// -----------------------------------------------------------
$equipe_os = [
    // OS 1
    ['id' => 1, 'os_id' => 1, 'nome' => 'Carlos Drummond', 'funcao' => 'Engenheiro Responsável', 'horas' => 16, 'custo_hora' => 150.00],
    ['id' => 2, 'os_id' => 1, 'nome' => 'Pedro Gomes', 'funcao' => 'Técnico Topógrafo', 'horas' => 48, 'custo_hora' => 65.00],
    ['id' => 3, 'os_id' => 1, 'nome' => 'Lucas Vieira', 'funcao' => 'Auxiliar de Campo', 'horas' => 56, 'custo_hora' => 35.00],
    // OS 2
    ['id' => 4, 'os_id' => 2, 'nome' => 'Carlos Drummond', 'funcao' => 'Engenheiro Responsável', 'horas' => 30, 'custo_hora' => 150.00],
    ['id' => 5, 'os_id' => 2, 'nome' => 'Ana Lima', 'funcao' => 'Técnica Topógrafa', 'horas' => 80, 'custo_hora' => 65.00],
    ['id' => 6, 'os_id' => 2, 'nome' => 'Jonas Pereira', 'funcao' => 'Auxiliar de Campo', 'horas' => 80, 'custo_hora' => 35.00],
    ['id' => 7, 'os_id' => 2, 'nome' => 'Bruno Maia', 'funcao' => 'Auxiliar de Campo', 'horas' => 80, 'custo_hora' => 35.00],
    // OS 3
    ['id' => 8, 'os_id' => 3, 'nome' => 'Beatriz Santos', 'funcao' => 'Engenheira Responsável', 'horas' => 40, 'custo_hora' => 150.00],
    ['id' => 9, 'os_id' => 3, 'nome' => 'Pedro Gomes', 'funcao' => 'Técnico Topógrafo', 'horas' => 60, 'custo_hora' => 65.00],
    // OS 5
    ['id' => 10, 'os_id' => 5, 'nome' => 'Carlos Drummond', 'funcao' => 'Engenheiro Responsável', 'horas' => 12, 'custo_hora' => 150.00],
    ['id' => 11, 'os_id' => 5, 'nome' => 'Lucas Vieira', 'funcao' => 'Auxiliar de Campo', 'horas' => 40, 'custo_hora' => 35.00],
];

// -----------------------------------------------------------
// CUSTOS POR OS
// -----------------------------------------------------------
$custos_os = [
    // OS 1
    ['id' => 1, 'os_id' => 1, 'tipo' => 'combustivel', 'descricao' => 'Gasolina — deslocamento campo (3 dias)', 'valor' => 420.00, 'data' => '2024-01-20'],
    ['id' => 2, 'os_id' => 1, 'tipo' => 'alimentacao', 'descricao' => 'Refeições equipe campo', 'valor' => 280.00, 'data' => '2024-01-22'],
    ['id' => 3, 'os_id' => 1, 'tipo' => 'equipamento', 'descricao' => 'Aluguel de estação total (2 dias)', 'valor' => 600.00, 'data' => '2024-01-23'],
    // OS 2
    ['id' => 4, 'os_id' => 2, 'tipo' => 'combustivel', 'descricao' => 'Diesel — veículo 4x4 caminhonete', 'valor' => 1800.00, 'data' => '2024-02-05'],
    ['id' => 5, 'os_id' => 2, 'tipo' => 'alimentacao', 'descricao' => 'Diárias e alimentação equipe (8 dias)', 'valor' => 1600.00, 'data' => '2024-02-08'],
    ['id' => 6, 'os_id' => 2, 'tipo' => 'equipamento', 'descricao' => 'GNSS RTK Leica GS18 — aluguel', 'valor' => 2400.00, 'data' => '2024-02-10'],
    ['id' => 7, 'os_id' => 2, 'tipo' => 'outros', 'descricao' => 'Averbação INCRA + taxas cartório', 'valor' => 850.00, 'data' => '2024-02-12'],
    // OS 3
    ['id' => 8, 'os_id' => 3, 'tipo' => 'combustivel', 'descricao' => 'Combustível — percurso urbano', 'valor' => 380.00, 'data' => '2024-02-18'],
    ['id' => 9, 'os_id' => 3, 'tipo' => 'equipamento', 'descricao' => 'Nível óptico + régua de mira', 'valor' => 250.00, 'data' => '2024-02-20'],
    // OS 5
    ['id' => 10, 'os_id' => 5, 'tipo' => 'combustivel', 'descricao' => 'Gasolina — deslocamento 2 dias', 'valor' => 280.00, 'data' => '2023-10-08'],
    ['id' => 11, 'os_id' => 5, 'tipo' => 'alimentacao', 'descricao' => 'Alimentação equipe', 'valor' => 180.00, 'data' => '2023-10-09'],
];

// -----------------------------------------------------------
// FINANCEIRO
// -----------------------------------------------------------
$financeiro = [
    ['id' => 1, 'os_id' => 1, 'tipo' => 'receita', 'descricao' => 'Pagamento proposta PROP-2024-001', 'valor' => 18500.00, 'data' => '2024-02-10'],
    ['id' => 2, 'os_id' => 5, 'tipo' => 'receita', 'descricao' => 'Faturamento OS-2024-005', 'valor' => 18500.00, 'data' => '2023-11-25'],
    ['id' => 3, 'os_id' => 2, 'tipo' => 'despesa', 'descricao' => 'Adiantamento viagem campo equipe', 'valor' => 3000.00, 'data' => '2024-02-01'],
    ['id' => 4, 'os_id' => 3, 'tipo' => 'despesa', 'descricao' => 'Impressão plantas e plotagem', 'valor' => 420.00, 'data' => '2024-03-10'],
];

// -----------------------------------------------------------
// ANEXOS POR OS
// -----------------------------------------------------------
$anexos_os = [
    ['id' => 1, 'os_id' => 1, 'nome' => 'levantamento_planialt_horizonte.dwg', 'tipo' => 'dwg', 'tamanho' => '4.2 MB', 'data' => '2024-02-03'],
    ['id' => 2, 'os_id' => 1, 'nome' => 'planta_situacao_A1.pdf', 'tipo' => 'pdf', 'tamanho' => '1.8 MB', 'data' => '2024-02-04'],
    ['id' => 3, 'os_id' => 1, 'nome' => 'foto_campo_01.jpg', 'tipo' => 'imagem', 'tamanho' => '3.1 MB', 'data' => '2024-01-20'],
    ['id' => 4, 'os_id' => 1, 'nome' => 'foto_campo_02.jpg', 'tipo' => 'imagem', 'tamanho' => '2.9 MB', 'data' => '2024-01-21'],
    ['id' => 5, 'os_id' => 1, 'nome' => 'art_carlos_drummond.pdf', 'tipo' => 'pdf', 'tamanho' => '0.4 MB', 'data' => '2024-02-05'],
    ['id' => 6, 'os_id' => 2, 'nome' => 'georref_fazenda_santaclara_parcial.dwg', 'tipo' => 'dwg', 'tamanho' => '6.7 MB', 'data' => '2024-03-01'],
    ['id' => 7, 'os_id' => 2, 'nome' => 'foto_marco_01.jpg', 'tipo' => 'imagem', 'tamanho' => '2.2 MB', 'data' => '2024-02-15'],
    ['id' => 8, 'os_id' => 3, 'nome' => 'perfil_longitudinal_drenagem.pdf', 'tipo' => 'pdf', 'tamanho' => '2.3 MB', 'data' => '2024-03-08'],
    ['id' => 9, 'os_id' => 5, 'nome' => 'implantacao_eixo_viario.dwg', 'tipo' => 'dwg', 'tamanho' => '3.5 MB', 'data' => '2023-11-18'],
    ['id' => 10, 'os_id' => 5, 'nome' => 'nf_005_2023.pdf', 'tipo' => 'pdf', 'tamanho' => '0.2 MB', 'data' => '2023-11-25'],
];

// -----------------------------------------------------------
// HISTÓRICO POR OS
// -----------------------------------------------------------
$historico_os = [
    // OS 1
    ['id' => 1,  'os_id' => 1, 'acao' => 'OS criada a partir da proposta PROP-2024-001', 'tipo' => 'criacao', 'data' => '2024-01-15 09:12', 'usuario' => 'Admin'],
    ['id' => 2,  'os_id' => 1, 'acao' => 'Status alterado para Em Execução', 'tipo' => 'status', 'data' => '2024-01-18 08:00', 'usuario' => 'Eng. Carlos Drummond'],
    ['id' => 3,  'os_id' => 1, 'acao' => 'Equipe definida: 3 membros adicionados', 'tipo' => 'equipe', 'data' => '2024-01-18 08:30', 'usuario' => 'Admin'],
    ['id' => 4,  'os_id' => 1, 'acao' => 'Custo registrado: Combustível R$ 420,00', 'tipo' => 'custo', 'data' => '2024-01-20 17:00', 'usuario' => 'Pedro Gomes'],
    ['id' => 5,  'os_id' => 1, 'acao' => 'Arquivo enviado: levantamento_planialt_horizonte.dwg', 'tipo' => 'arquivo', 'data' => '2024-02-03 10:45', 'usuario' => 'Eng. Carlos Drummond'],
    ['id' => 6,  'os_id' => 1, 'acao' => 'Status alterado para Finalizada', 'tipo' => 'status', 'data' => '2024-02-05 16:00', 'usuario' => 'Eng. Carlos Drummond'],
    ['id' => 7,  'os_id' => 1, 'acao' => 'Receita lançada: R$ 18.500,00 referente pagamento da proposta', 'tipo' => 'financeiro', 'data' => '2024-02-10 11:20', 'usuario' => 'Admin'],
    // OS 2
    ['id' => 8,  'os_id' => 2, 'acao' => 'OS criada a partir da proposta PROP-2024-002', 'tipo' => 'criacao', 'data' => '2024-01-28 14:00', 'usuario' => 'Admin'],
    ['id' => 9,  'os_id' => 2, 'acao' => 'Status alterado para Em Execução', 'tipo' => 'status', 'data' => '2024-02-01 07:30', 'usuario' => 'Eng. Carlos Drummond'],
    ['id' => 10, 'os_id' => 2, 'acao' => 'Equipe mobilizada: 4 membros adicionados', 'tipo' => 'equipe', 'data' => '2024-02-01 08:00', 'usuario' => 'Admin'],
    ['id' => 11, 'os_id' => 2, 'acao' => 'Custo registrado: GNSS RTK Leica R$ 2.400,00', 'tipo' => 'custo', 'data' => '2024-02-10 09:00', 'usuario' => 'Eng. Carlos Drummond'],
    // OS 3
    ['id' => 12, 'os_id' => 3, 'acao' => 'OS criada a partir da proposta PROP-2024-003', 'tipo' => 'criacao', 'data' => '2024-02-12 10:00', 'usuario' => 'Admin'],
    ['id' => 13, 'os_id' => 3, 'acao' => 'Status alterado para Em Execução', 'tipo' => 'status', 'data' => '2024-02-15 08:00', 'usuario' => 'Eng. Beatriz Santos'],
    ['id' => 14, 'os_id' => 3, 'acao' => 'Solicitação de revisão recebida do cliente', 'tipo' => 'status', 'data' => '2024-03-20 15:30', 'usuario' => 'Eng. Beatriz Santos'],
    ['id' => 15, 'os_id' => 3, 'acao' => 'Status alterado para Em Revisão', 'tipo' => 'status', 'data' => '2024-03-21 09:00', 'usuario' => 'Admin'],
    // OS 4
    ['id' => 16, 'os_id' => 4, 'acao' => 'OS criada a partir da proposta PROP-2024-007', 'tipo' => 'criacao', 'data' => '2024-03-25 11:00', 'usuario' => 'Admin'],
    // OS 5
    ['id' => 17, 'os_id' => 5, 'acao' => 'OS criada', 'tipo' => 'criacao', 'data' => '2023-10-01 09:00', 'usuario' => 'Admin'],
    ['id' => 18, 'os_id' => 5, 'acao' => 'Status alterado para Em Execução', 'tipo' => 'status', 'data' => '2023-10-05 08:00', 'usuario' => 'Eng. Carlos Drummond'],
    ['id' => 19, 'os_id' => 5, 'acao' => 'Status alterado para Finalizada', 'tipo' => 'status', 'data' => '2023-11-20 17:00', 'usuario' => 'Eng. Carlos Drummond'],
    ['id' => 20, 'os_id' => 5, 'acao' => 'Nota fiscal emitida e receita lançada', 'tipo' => 'financeiro', 'data' => '2023-11-25 10:00', 'usuario' => 'Admin'],
    ['id' => 21, 'os_id' => 5, 'acao' => 'Status alterado para Faturada', 'tipo' => 'status', 'data' => '2023-11-25 10:05', 'usuario' => 'Admin'],
    // OS 6
    ['id' => 22, 'os_id' => 6, 'acao' => 'OS criada', 'tipo' => 'criacao', 'data' => '2024-03-28 14:00', 'usuario' => 'Admin'],
];
