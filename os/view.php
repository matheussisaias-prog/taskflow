<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$id = (int)($_GET['id'] ?? 0);
$os = findById($ordens_servico, $id);
if (!$os) { header('Location: index.php'); exit; }

$cliente  = findById($clientes, (int)$os['cliente_id']);
$proposta = findById($propostas, (int)$os['proposta_id']);

$osEquipe   = findAllBy($equipe_os,   'os_id', $id);
$osCustos   = findAllBy($custos_os,   'os_id', $id);
$osAnexos   = findAllBy($anexos_os,   'os_id', $id);
$osHist     = findAllBy($historico_os,'os_id', $id);
$osFinanc   = findAllBy($financeiro,  'os_id', $id);

// Cálculos financeiros
$totalEquipe  = calcCustoEquipe($equipe_os, $id);
$totalCustos  = calcCustosDiretos($custos_os, $id);
$totalGeral   = $totalEquipe + $totalCustos;
$valorProposta = $proposta ? (float)$proposta['valor'] : 0;
$totalReceitas = calcTotalReceitas($financeiro, $id);
$totalDespesas = calcTotalDespesas($financeiro, $id);
$lucroEstimado = $valorProposta - $totalGeral;
$margem        = $valorProposta > 0 ? round($lucroEstimado / $valorProposta * 100, 1) : 0;

// Classe de status CSS (sem espaços)
$statusCssClass = 'status-' . str_replace(' ', '-', $os['status']);

renderHeader('OS ' . $os['codigo'], 'os');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="index.php">Ordens de Serviço</a>
    <span class="breadcrumb-sep">›</span>
    <span><?= htmlspecialchars($os['codigo']) ?></span>
</div>

<!-- ============================================================
     OS HERO — CABEÇALHO PRINCIPAL
     ============================================================ -->
<div class="os-hero <?= $statusCssClass ?>">
    <div class="os-hero-top">
        <div>
            <span class="os-hero-code"><?= htmlspecialchars($os['codigo']) ?></span>
            <h1 class="os-hero-title"><?= htmlspecialchars($os['descricao']) ?></h1>
            <div class="os-hero-type"><?= htmlspecialchars($os['tipo_servico']) ?></div>
        </div>
        <div class="os-hero-status">
            <div class="os-status-pill <?= $statusCssClass ?>">
                <span class="os-status-dot"></span>
                <?= osStatusLabel($os['status']) ?>
            </div>
            <!-- Ações rápidas de status -->
            <div class="flex gap-8">
                <?php
                $proxStatus = [
                    'aberta'       => ['em execucao', 'Iniciar Execução'],
                    'em execucao'  => ['em revisao',  'Enviar p/ Revisão'],
                    'em revisao'   => ['finalizada',  'Finalizar OS'],
                    'finalizada'   => ['faturada',    'Marcar Faturada'],
                    'faturada'     => [null, null],
                ];
                [$prox, $label] = $proxStatus[$os['status']] ?? [null, null];
                if ($prox):
                ?>
                <a href="view.php?id=<?= $id ?>&avanca=1" class="btn btn-secondary btn-sm"
                   onclick="return confirmAction('Avançar status para: <?= htmlspecialchars($label) ?>?')"
                   style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.8);background:rgba(255,255,255,.08)">
                    <?= htmlspecialchars($label) ?> →
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="os-hero-meta">
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Cliente</span>
            <span class="os-hero-meta-value"><?= htmlspecialchars($cliente['nome'] ?? '—') ?></span>
        </div>
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Responsável</span>
            <span class="os-hero-meta-value"><?= htmlspecialchars($os['responsavel']) ?></span>
        </div>
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Abertura</span>
            <span class="os-hero-meta-value"><?= formatDate($os['data_abertura']) ?></span>
        </div>
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Previsão</span>
            <span class="os-hero-meta-value"><?= formatDate($os['data_previsao']) ?></span>
        </div>
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Valor Proposta</span>
            <span class="os-hero-meta-value" style="font-family:var(--font-mono)"><?= formatMoney($valorProposta) ?></span>
        </div>
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Custo Total</span>
            <span class="os-hero-meta-value" style="font-family:var(--font-mono)"><?= formatMoney($totalGeral) ?></span>
        </div>
        <div class="os-hero-meta-item">
            <span class="os-hero-meta-label">Lucro Estimado</span>
            <span class="os-hero-meta-value" style="font-family:var(--font-mono);color:<?= $lucroEstimado>=0?'#6EE7B7':'#FCA5A5' ?>">
                <?= formatMoney($lucroEstimado) ?>
            </span>
        </div>
    </div>
</div>

<!-- ============================================================
     ABAS DO HUB
     ============================================================ -->
<div class="os-tabs">
    <button class="os-tab-btn active" data-tab="geral">
        📊 Visão Geral
    </button>
    <button class="os-tab-btn" data-tab="equipe">
        👷 Equipe
        <span class="tab-count"><?= count($osEquipe) ?></span>
    </button>
    <button class="os-tab-btn" data-tab="custos">
        💰 Custos
        <span class="tab-count"><?= count($osCustos) ?></span>
    </button>
    <button class="os-tab-btn" data-tab="financeiro">
        📈 Financeiro
    </button>
    <button class="os-tab-btn" data-tab="anexos">
        📎 Anexos
        <span class="tab-count"><?= count($osAnexos) ?></span>
    </button>
    <button class="os-tab-btn" data-tab="historico">
        🕐 Histórico
        <span class="tab-count"><?= count($osHist) ?></span>
    </button>
</div>

<!-- ============================================================
     ABA: VISÃO GERAL
     ============================================================ -->
<div class="tab-panel active" id="tab-geral">
    <div class="grid-2 gap-16">

        <!-- Dados da OS -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Dados da OS</div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item-label">Código</div>
                        <div class="info-item-value mono"><?= htmlspecialchars($os['codigo']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Tipo de Serviço</div>
                        <div class="info-item-value"><?= htmlspecialchars($os['tipo_servico']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Responsável</div>
                        <div class="info-item-value"><?= htmlspecialchars($os['responsavel']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Status</div>
                        <div class="info-item-value"><?= badge(osStatusLabel($os['status']), osStatusClass($os['status'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Data de Abertura</div>
                        <div class="info-item-value"><?= formatDate($os['data_abertura']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Início do Campo</div>
                        <div class="info-item-value"><?= formatDate($os['data_inicio']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Previsão Conclusão</div>
                        <div class="info-item-value"><?= formatDate($os['data_previsao']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Conclusão Real</div>
                        <div class="info-item-value"><?= formatDate($os['data_conclusao']) ?></div>
                    </div>
                    <div class="info-item" style="grid-column:span 2">
                        <div class="info-item-label">Descrição</div>
                        <div class="info-item-value"><?= htmlspecialchars($os['descricao']) ?></div>
                    </div>
                    <?php if ($os['observacoes']): ?>
                    <div class="info-item" style="grid-column:span 2">
                        <div class="info-item-label">Observações</div>
                        <div class="info-item-value"><?= htmlspecialchars($os['observacoes']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cliente e Proposta -->
        <div style="display:flex;flex-direction:column;gap:16px">

            <!-- CLIENTE -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Cliente</div>
                    <?php if ($cliente): ?>
                        <a href="../clientes/view.php?id=<?= $cliente['id'] ?>" class="btn btn-ghost btn-xs">Ver →</a>
                    <?php endif; ?>
                </div>
                <?php if ($cliente): ?>
                <div class="card-body">
                    <div class="client-card-info mb-12">
                        <div class="client-avatar"><?= mb_substr(strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', $cliente['nome'])))), 0, 2) ?></div>
                        <div>
                            <div style="font-weight:600;color:var(--gray-800);font-size:.92rem"><?= htmlspecialchars($cliente['nome']) ?></div>
                            <div class="text-xs text-muted"><?= htmlspecialchars($cliente['cnpj']) ?></div>
                        </div>
                    </div>
                    <div class="info-grid" style="grid-template-columns:1fr 1fr">
                        <div class="info-item">
                            <div class="info-item-label">Contato</div>
                            <div class="info-item-value"><?= htmlspecialchars($cliente['contato']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-label">Telefone</div>
                            <div class="info-item-value"><?= htmlspecialchars($cliente['telefone']) ?></div>
                        </div>
                        <div class="info-item" style="grid-column:span 2">
                            <div class="info-item-label">E-mail</div>
                            <div class="info-item-value"><?= htmlspecialchars($cliente['email']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- PROPOSTA -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Proposta Vinculada</div>
                    <?php if ($proposta): ?>
                        <a href="../propostas/view.php?id=<?= $proposta['id'] ?>" class="btn btn-ghost btn-xs">Ver →</a>
                    <?php endif; ?>
                </div>
                <?php if ($proposta): ?>
                <div class="card-body">
                    <div class="info-grid" style="grid-template-columns:1fr 1fr">
                        <div class="info-item">
                            <div class="info-item-label">Código</div>
                            <div class="info-item-value mono"><?= htmlspecialchars($proposta['codigo']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-label">Valor</div>
                            <div class="info-item-value" style="font-family:var(--font-mono);font-weight:700;color:var(--success)"><?= formatMoney($proposta['valor']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-label">Data</div>
                            <div class="info-item-value"><?= formatDate($proposta['data']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-label">Status</div>
                            <div class="info-item-value"><?= badge(propostaStatusLabel($proposta['status']), propostaStatusClass($proposta['status'])) ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>

    </div><!-- /grid -->

    <!-- RESUMO RÁPIDO -->
    <div class="stats-grid mt-16" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card stat-blue">
            <div class="stat-label">Equipe</div>
            <div class="stat-value"><?= count($osEquipe) ?></div>
            <div class="stat-sub">Membros alocados</div>
        </div>
        <div class="stat-card stat-amber">
            <div class="stat-label">Custos Diretos</div>
            <div class="stat-value" style="font-size:1.3rem"><?= formatMoney($totalCustos) ?></div>
            <div class="stat-sub"><?= count($osCustos) ?> lançamentos</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-label">Custo Equipe</div>
            <div class="stat-value" style="font-size:1.3rem"><?= formatMoney($totalEquipe) ?></div>
            <div class="stat-sub">Mão de obra</div>
        </div>
        <div class="stat-card <?= $lucroEstimado >= 0 ? 'stat-green' : 'stat-red' ?>">
            <div class="stat-label">Lucro Estimado</div>
            <div class="stat-value" style="font-size:1.2rem"><?= formatMoney($lucroEstimado) ?></div>
            <div class="stat-sub">Margem: <?= $margem ?>%</div>
        </div>
    </div>

</div><!-- /tab-geral -->

<!-- ============================================================
     ABA: EQUIPE
     ============================================================ -->
<div class="tab-panel" id="tab-equipe">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Equipe Alocada</div>
                <div class="card-subtitle">Profissionais vinculados a esta OS</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-equipe')">+ Adicionar Membro</button>
        </div>
        <div class="card-body">
            <?php if (empty($osEquipe)): ?>
                <div class="empty-state">
                    <div class="empty-icon">👷</div>
                    <div class="empty-title">Nenhum membro alocado</div>
                    <div class="empty-text">Adicione os profissionais responsáveis por esta OS</div>
                </div>
            <?php else: ?>
                <div class="team-list" id="equipe-list">
                <?php foreach ($osEquipe as $m):
                    $initials = mb_substr(strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', $m['nome'])))), 0, 2);
                    $totalM = (float)$m['horas'] * (float)$m['custo_hora'];
                ?>
                    <div class="team-card" id="eq-<?= $m['id'] ?>">
                        <div class="team-avatar"><?= $initials ?></div>
                        <div>
                            <div class="team-name"><?= htmlspecialchars($m['nome']) ?></div>
                            <div class="team-role"><?= htmlspecialchars($m['funcao']) ?></div>
                        </div>
                        <div class="team-stats">
                            <div>
                                <span class="team-stat-val"><?= (int)$m['horas'] ?>h</span>
                                <span class="team-stat-label">Horas</span>
                            </div>
                            <div>
                                <span class="team-stat-val">R$ <?= number_format($m['custo_hora'],2,',','.') ?>/h</span>
                                <span class="team-stat-label">Custo/h</span>
                            </div>
                            <div>
                                <span class="team-stat-val">R$ <?= number_format($totalM,2,',','.') ?></span>
                                <span class="team-stat-label">Total</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <div class="total-bar mt-16">
                    <span class="total-bar-label">Total Custo de Equipe</span>
                    <span class="total-bar-value" id="equipe-total"><?= formatMoney($totalEquipe) ?></span>
                </div>
            <?php endif; ?>

            <!-- Área para membros adicionados via JS -->
            <?php if (empty($osEquipe)): ?>
            <div class="team-list mt-8" id="equipe-list"></div>
            <div class="total-bar mt-8" style="display:none" id="equipe-total-bar">
                <span class="total-bar-label">Total Custo de Equipe</span>
                <span class="total-bar-value" id="equipe-total">R$ 0,00</span>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ============================================================
     ABA: CUSTOS
     ============================================================ -->
<div class="tab-panel" id="tab-custos">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Custos Operacionais</div>
                <div class="card-subtitle">Combustível, alimentação, equipamentos e outros</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-custo')">+ Adicionar Custo</button>
        </div>
        <div class="card-body">
            <?php if (empty($osCustos)): ?>
                <div class="empty-state">
                    <div class="empty-icon">💰</div>
                    <div class="empty-title">Nenhum custo registrado</div>
                    <div class="empty-text">Registre os custos operacionais desta OS</div>
                </div>
                <div class="cost-list" id="custos-list"></div>
            <?php else: ?>
                <div class="cost-list" id="custos-list">
                <?php foreach ($osCustos as $c): ?>
                    <div class="cost-item" id="custo-<?= $c['id'] ?>">
                        <div class="cost-icon <?= $c['tipo'] ?>"><?= custoTipoIcon($c['tipo']) ?></div>
                        <div>
                            <div class="cost-desc"><?= htmlspecialchars($c['descricao']) ?></div>
                            <div class="cost-date"><?= formatDate($c['data']) ?></div>
                        </div>
                        <span class="cost-tipo-tag"><?= custoTipoLabel($c['tipo']) ?></span>
                        <div class="cost-value"><?= formatMoney($c['valor']) ?></div>
                    </div>
                <?php endforeach; ?>
                </div>
                <div class="total-bar mt-12">
                    <span class="total-bar-label">Total Custos Diretos</span>
                    <span class="total-bar-value" id="custos-total"><?= formatMoney($totalCustos) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================================
     ABA: FINANCEIRO
     ============================================================ -->
<div class="tab-panel" id="tab-financeiro">

    <!-- Cards financeiros -->
    <div class="finance-grid mb-16">
        <div class="finance-card">
            <div class="finance-card-label">Valor da Proposta</div>
            <div class="finance-card-value finance-blue" id="prop-valor" data-valor="<?= $valorProposta ?>"><?= formatMoney($valorProposta) ?></div>
            <div class="finance-card-hint">Contrato com o cliente</div>
        </div>
        <div class="finance-card">
            <div class="finance-card-label">Custo Total</div>
            <div class="finance-card-value finance-red" id="fin-card-custo"><?= formatMoney($totalGeral) ?></div>
            <div class="finance-card-hint">Equipe + diretos</div>
        </div>
        <div class="finance-card">
            <div class="finance-card-label">Lucro Estimado</div>
            <div class="finance-card-value <?= $lucroEstimado >= 0 ? 'finance-green' : 'finance-red' ?>" id="fin-card-lucro"><?= formatMoney($lucroEstimado) ?></div>
            <div class="finance-card-hint">Proposta − Custos</div>
        </div>
        <div class="finance-card">
            <div class="finance-card-label">Margem (%)</div>
            <div class="finance-card-value <?= $margem >= 20 ? 'finance-green' : ($margem >= 0 ? 'finance-gold' : 'finance-red') ?>" id="fin-card-margem"><?= $margem ?>%</div>
            <div class="finance-card-hint">Sobre o valor contratado</div>
        </div>
    </div>

    <!-- Detalhamento -->
    <div class="grid-2 gap-16">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Composição de Custos</div>
            </div>
            <div class="card-body">
                <table>
                    <thead><tr><th>Item</th><th>Valor</th><th>%</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>Custo de Equipe (M.O.)</td>
                            <td style="font-family:var(--font-mono);font-weight:600"><?= formatMoney($totalEquipe) ?></td>
                            <td class="text-sm text-muted"><?= $totalGeral > 0 ? round($totalEquipe/$totalGeral*100, 1) : 0 ?>%</td>
                        </tr>
                        <tr>
                            <td>Custos Diretos</td>
                            <td style="font-family:var(--font-mono);font-weight:600"><?= formatMoney($totalCustos) ?></td>
                            <td class="text-sm text-muted"><?= $totalGeral > 0 ? round($totalCustos/$totalGeral*100, 1) : 0 ?>%</td>
                        </tr>
                        <tr style="background:var(--gray-50)">
                            <td class="text-bold">Total Geral</td>
                            <td style="font-family:var(--font-mono);font-weight:700"><?= formatMoney($totalGeral) ?></td>
                            <td>100%</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Barra de progresso visual -->
                <?php if ($valorProposta > 0): ?>
                <div style="margin-top:16px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span class="text-xs text-muted">Custo vs Valor Proposta</span>
                        <span class="text-xs text-muted"><?= round($totalGeral/$valorProposta*100, 1) ?>% consumido</span>
                    </div>
                    <div style="height:8px;background:var(--gray-100);border-radius:4px;overflow:hidden">
                        <div style="height:100%;width:<?= min(100, round($totalGeral/$valorProposta*100)) ?>%;background:<?= $totalGeral > $valorProposta ? 'var(--danger)' : ($totalGeral > $valorProposta*0.8 ? 'var(--warning)' : 'var(--accent)') ?>;border-radius:4px;transition:width .3s"></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Lançamentos Financeiros</div>
                <button class="btn btn-secondary btn-sm" onclick="openModal('modal-lancamento')">+ Lançamento</button>
            </div>
            <div class="card-body-sm">
                <?php if (empty($osFinanc)): ?>
                    <div class="empty-state" style="padding:24px">
                        <div class="empty-icon" style="font-size:1.5rem">📊</div>
                        <div class="empty-title">Sem lançamentos</div>
                        <div class="empty-text">Registre receitas e despesas desta OS</div>
                    </div>
                <?php endif; ?>
                <table>
                    <thead><tr><th>Data</th><th>Tipo</th><th>Descrição</th><th>Valor</th></tr></thead>
                    <tbody id="financeiro-list">
                    <?php foreach ($osFinanc as $f): ?>
                        <tr>
                            <td class="text-sm text-muted"><?= formatDate($f['data']) ?></td>
                            <td><?= badge($f['tipo']==='receita'?'Receita':'Despesa', $f['tipo']==='receita'?'badge-done':'badge-danger') ?></td>
                            <td class="text-sm"><?= htmlspecialchars($f['descricao']) ?></td>
                            <td class="<?= $f['tipo']==='receita'?'finance-green':'finance-red' ?>" style="font-family:var(--font-mono);font-weight:600;white-space:nowrap">
                                <?= $f['tipo']==='receita' ? '+' : '-' ?> <?= formatMoney($f['valor']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================
     ABA: ANEXOS
     ============================================================ -->
<div class="tab-panel" id="tab-anexos">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Arquivos e Documentos</div>
                <div class="card-subtitle">Plantas, fotos, ART, PDFs e arquivos DWG</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-upload')">+ Enviar Arquivo</button>
        </div>
        <div class="card-body">
            <?php if (empty($osAnexos)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📎</div>
                    <div class="empty-title">Nenhum arquivo</div>
                    <div class="empty-text">Envie plantas, fotos de campo e documentos desta OS</div>
                </div>
            <?php endif; ?>
            <div class="files-grid" id="files-grid">
            <?php foreach ($osAnexos as $a): ?>
                <div class="file-card" title="<?= htmlspecialchars($a['nome']) ?>">
                    <div class="file-icon"><?= anexoTipoIcon($a['tipo']) ?></div>
                    <div class="file-name"><?= htmlspecialchars($a['nome']) ?></div>
                    <div class="file-meta">
                        <span class="file-size"><?= htmlspecialchars($a['tamanho']) ?></span>
                        <span class="<?= anexoTipoClass($a['tipo']) ?>"><?= strtoupper($a['tipo']) ?></span>
                    </div>
                    <div class="text-xs text-muted"><?= formatDate($a['data']) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     ABA: HISTÓRICO
     ============================================================ -->
<div class="tab-panel" id="tab-historico">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Histórico de Ações</div>
            <div class="card-subtitle">Linha do tempo completa desta OS</div>
        </div>
        <div class="card-body">
            <?php if (empty($osHist)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🕐</div>
                    <div class="empty-title">Histórico vazio</div>
                </div>
            <?php else: ?>
            <div class="timeline">
                <?php foreach (array_reverse($osHist) as $h): ?>
                    <div class="timeline-item <?= historicoIcon($h['tipo']) ?>">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-action"><?= htmlspecialchars($h['acao']) ?></div>
                            <div class="timeline-meta">
                                <span><?= formatDateTime($h['data']) ?></span>
                                <span>•</span>
                                <span class="timeline-user"><?= htmlspecialchars($h['usuario']) ?></span>
                                <span>•</span>
                                <span><?= timeAgo($h['data']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- ============================================================
     MODAIS
     ============================================================ -->

<!-- Modal: Adicionar Membro de Equipe -->
<div class="modal-overlay" id="modal-equipe">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">👷 Adicionar Membro à Equipe</span>
            <button class="btn-icon" onclick="closeModal('modal-equipe')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid cols-1">
                <div class="form-group">
                    <label class="form-label">Nome Completo <span class="req">*</span></label>
                    <input type="text" id="eq_nome" class="form-control" placeholder="Ex: Pedro Gomes">
                </div>
                <div class="form-group">
                    <label class="form-label">Função <span class="req">*</span></label>
                    <select id="eq_funcao" class="form-control">
                        <option value="">— Selecione —</option>
                        <option>Engenheiro Responsável</option>
                        <option>Técnico Topógrafo</option>
                        <option>Auxiliar de Campo</option>
                        <option>Desenhista CAD</option>
                        <option>Motorista / Apoio</option>
                        <option>Estagiário</option>
                    </select>
                </div>
                <div class="form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="form-group">
                        <label class="form-label">Horas Trabalhadas</label>
                        <input type="number" id="eq_horas" class="form-control" placeholder="0" min="0" step="0.5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Custo por Hora (R$)</label>
                        <input type="number" id="eq_custo" class="form-control" placeholder="0.00" min="0" step="0.50">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-equipe')">Cancelar</button>
            <button class="btn btn-primary" onclick="addEquipeMember()">Adicionar</button>
        </div>
    </div>
</div>

<!-- Modal: Adicionar Custo -->
<div class="modal-overlay" id="modal-custo">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">💰 Registrar Custo</span>
            <button class="btn-icon" onclick="closeModal('modal-custo')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid cols-1">
                <div class="form-group">
                    <label class="form-label">Tipo de Custo</label>
                    <select id="custo_tipo" class="form-control">
                        <option value="combustivel">⛽ Combustível</option>
                        <option value="alimentacao">🍽 Alimentação</option>
                        <option value="equipamento">📡 Equipamento</option>
                        <option value="outros">📎 Outros</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Descrição <span class="req">*</span></label>
                    <input type="text" id="custo_desc" class="form-control" placeholder="Descreva o custo...">
                </div>
                <div class="form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="form-group">
                        <label class="form-label">Valor (R$) <span class="req">*</span></label>
                        <input type="number" id="custo_valor" class="form-control" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data</label>
                        <input type="date" id="custo_data" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-custo')">Cancelar</button>
            <button class="btn btn-primary" onclick="addCusto()">Registrar</button>
        </div>
    </div>
</div>

<!-- Modal: Lançamento Financeiro -->
<div class="modal-overlay" id="modal-lancamento">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📈 Lançamento Financeiro</span>
            <button class="btn-icon" onclick="closeModal('modal-lancamento')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid cols-1">
                <div class="form-group">
                    <label class="form-label">Tipo</label>
                    <select id="fin_tipo" class="form-control">
                        <option value="receita">Receita</option>
                        <option value="despesa">Despesa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Descrição <span class="req">*</span></label>
                    <input type="text" id="fin_desc" class="form-control" placeholder="Ex: Pagamento nota fiscal 012">
                </div>
                <div class="form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="form-group">
                        <label class="form-label">Valor (R$) <span class="req">*</span></label>
                        <input type="number" id="fin_valor" class="form-control" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data</label>
                        <input type="date" id="fin_data" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-lancamento')">Cancelar</button>
            <button class="btn btn-primary" onclick="addLancamento()">Lançar</button>
        </div>
    </div>
</div>

<!-- Modal: Upload de Arquivo -->
<div class="modal-overlay" id="modal-upload">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">📎 Enviar Arquivo</span>
            <button class="btn-icon" onclick="closeModal('modal-upload')">✕</button>
        </div>
        <div class="modal-body">
            <div class="upload-area" onclick="document.getElementById('file-input').click()">
                <div class="upload-area-icon">📂</div>
                <div class="upload-area-text">Clique para selecionar arquivo</div>
                <div class="upload-area-hint">PDF, DWG, JPG, PNG — máx. 50MB (simulação)</div>
            </div>
            <input type="file" id="file-input" style="display:none" accept=".pdf,.dwg,.jpg,.jpeg,.png,.gif">
            <div class="mt-8 text-xs text-muted" style="text-align:center">
                Modo demonstração — os arquivos não são salvos no servidor
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-upload')">Cancelar</button>
            <button class="btn btn-primary" onclick="simulateUpload()">Enviar</button>
        </div>
    </div>
</div>

<?php renderFooter(); ?>
