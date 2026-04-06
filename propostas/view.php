<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$id       = (int)($_GET['id'] ?? 0);
$proposta = findById($propostas, $id);

if (!$proposta) { header('Location: index.php'); exit; }

// Simular aprovação se parâmetro presente
$aprovado = isset($_GET['aprovar']) && $proposta['status'] === 'pendente';
if ($aprovado) {
    $proposta['status'] = 'aprovada';
    $proposta['aprovada_em'] = date('Y-m-d');
}

$cliente  = findById($clientes, (int)$proposta['cliente_id']);
$osGerada = null;
foreach ($ordens_servico as $os) {
    if ((int)$os['proposta_id'] === $id) { $osGerada = $os; break; }
}

renderHeader('Proposta — ' . $proposta['codigo'], 'propostas');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="index.php">Propostas</a>
    <span class="breadcrumb-sep">›</span>
    <span><?= htmlspecialchars($proposta['codigo']) ?></span>
</div>

<?php if ($aprovado): ?>
    <div class="alert alert-success">✅ Proposta aprovada com sucesso! Você já pode criar uma Ordem de Serviço a partir dela.</div>
<?php endif; ?>

<div class="page-header">
    <div class="page-header-info">
        <div class="flex flex-center gap-8 mb-4">
            <span style="font-family:var(--font-mono);font-size:.82rem;color:var(--accent);background:var(--accent-dim);padding:3px 10px;border-radius:20px"><?= htmlspecialchars($proposta['codigo']) ?></span>
            <?= badge(propostaStatusLabel($proposta['status']), propostaStatusClass($proposta['status'])) ?>
        </div>
        <h2><?= htmlspecialchars(mb_strimwidth($proposta['descricao'], 0, 80, '…')) ?></h2>
        <p><?= $cliente ? htmlspecialchars($cliente['nome']) : '—' ?></p>
    </div>
    <div class="page-header-actions">
        <?php if ($proposta['status'] === 'pendente'): ?>
            <a href="view.php?id=<?= $id ?>&aprovar=1" class="btn btn-success" onclick="return confirmAction('Confirmar aprovação da proposta?')">✓ Aprovar Proposta</a>
        <?php endif; ?>
        <?php if ($proposta['status'] === 'aprovada' && !$osGerada): ?>
            <a href="../os/create.php?proposta_id=<?= $id ?>" class="btn btn-primary">+ Criar Ordem de Serviço</a>
        <?php endif; ?>
    </div>
</div>

<!-- INFO CARDS -->
<div class="grid-2 gap-16 mb-16">

    <div class="card">
        <div class="card-header"><div class="card-title">Dados da Proposta</div></div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-label">Código</div>
                    <div class="info-item-value mono"><?= htmlspecialchars($proposta['codigo']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Valor</div>
                    <div class="info-item-value" style="font-family:var(--font-mono);font-size:1.1rem;font-weight:700;color:var(--success)"><?= formatMoney($proposta['valor']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Data da Proposta</div>
                    <div class="info-item-value"><?= formatDate($proposta['data']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Validade</div>
                    <div class="info-item-value"><?= formatDate($proposta['validade']) ?></div>
                </div>
                <?php if ($proposta['aprovada_em']): ?>
                <div class="info-item">
                    <div class="info-item-label">Aprovada em</div>
                    <div class="info-item-value"><?= formatDate($proposta['aprovada_em']) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-item span-2" style="grid-column:span 2">
                    <div class="info-item-label">Descrição do Serviço</div>
                    <div class="info-item-value"><?= htmlspecialchars($proposta['descricao']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Cliente</div></div>
        <?php if ($cliente): ?>
        <div class="card-body">
            <div class="client-card-info mb-12">
                <div class="client-avatar"><?= mb_substr(strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', $cliente['nome'])))), 0, 2) ?></div>
                <div>
                    <div style="font-weight:600;color:var(--gray-800)"><?= htmlspecialchars($cliente['nome']) ?></div>
                    <div class="text-xs text-muted"><?= htmlspecialchars($cliente['cnpj']) ?></div>
                </div>
            </div>
            <div class="info-grid">
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
            <div class="mt-16">
                <a href="../clientes/view.php?id=<?= $cliente['id'] ?>" class="btn btn-secondary btn-sm">Ver cliente completo →</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- OS GERADA -->
<?php if ($osGerada): ?>
<div class="card">
    <div class="card-header">
        <div class="card-title">Ordem de Serviço Gerada</div>
        <?= badge('OS Criada', 'badge-done') ?>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item-label">Código da OS</div>
                <div class="info-item-value mono"><?= htmlspecialchars($osGerada['codigo']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Tipo de Serviço</div>
                <div class="info-item-value"><?= htmlspecialchars($osGerada['tipo_servico']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Status</div>
                <div class="info-item-value"><?= badge(osStatusLabel($osGerada['status']), osStatusClass($osGerada['status'])) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Responsável</div>
                <div class="info-item-value"><?= htmlspecialchars($osGerada['responsavel']) ?></div>
            </div>
        </div>
        <div class="mt-16">
            <a href="../os/view.php?id=<?= $osGerada['id'] ?>" class="btn btn-primary btn-sm">Abrir OS Completa →</a>
        </div>
    </div>
</div>
<?php elseif ($proposta['status'] === 'aprovada'): ?>
<div class="alert alert-info">
    Esta proposta foi aprovada mas ainda não gerou uma Ordem de Serviço.
    <a href="../os/create.php?proposta_id=<?= $id ?>" style="font-weight:600;margin-left:8px">Criar OS agora →</a>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <strong>Atenção:</strong> Para gerar uma Ordem de Serviço, a proposta precisa ser aprovada primeiro.
</div>
<?php endif; ?>

<?php renderFooter(); ?>
