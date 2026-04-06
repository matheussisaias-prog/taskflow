<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$id     = (int)($_GET['id'] ?? 0);
$cliente = findById($clientes, $id);

if (!$cliente) {
    header('Location: index.php'); exit;
}

$cliProps = findAllBy($propostas, 'cliente_id', $id);
$cliOS    = findAllBy($ordens_servico, 'cliente_id', $id);
$initials = mb_substr(strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', $cliente['nome'])))), 0, 2);

renderHeader('Cliente — ' . $cliente['nome'], 'clientes');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="index.php">Clientes</a>
    <span class="breadcrumb-sep">›</span>
    <span><?= htmlspecialchars($cliente['nome']) ?></span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <div class="flex flex-center gap-12 mb-8">
            <div style="width:52px;height:52px;border-radius:12px;background:linear-gradient(135deg,var(--primary-light),var(--accent));display:grid;place-items:center;font-family:var(--font-display);font-size:1.3rem;font-weight:700;color:var(--white)"><?= $initials ?></div>
            <div>
                <h2><?= htmlspecialchars($cliente['nome']) ?></h2>
                <p style="margin-top:2px"><?= htmlspecialchars($cliente['cnpj']) ?></p>
            </div>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="../propostas/create.php?cliente_id=<?= $id ?>" class="btn btn-secondary btn-sm">Nova Proposta</a>
        <a href="../os/create.php?cliente_id=<?= $id ?>" class="btn btn-primary btn-sm">Nova OS</a>
    </div>
</div>

<!-- DADOS DO CLIENTE -->
<div class="card mb-16">
    <div class="card-header">
        <div class="card-title">Informações Cadastrais</div>
        <span class="text-xs text-muted">Cadastrado em <?= formatDate($cliente['criado_em']) ?></span>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item-label">Razão Social</div>
                <div class="info-item-value"><?= htmlspecialchars($cliente['nome']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">CNPJ</div>
                <div class="info-item-value mono"><?= htmlspecialchars($cliente['cnpj']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Contato Principal</div>
                <div class="info-item-value"><?= htmlspecialchars($cliente['contato']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Telefone</div>
                <div class="info-item-value"><?= htmlspecialchars($cliente['telefone']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">E-mail</div>
                <div class="info-item-value"><?= htmlspecialchars($cliente['email']) ?></div>
            </div>
            <div class="info-item" style="grid-column:span 2">
                <div class="info-item-label">Endereço</div>
                <div class="info-item-value"><?= htmlspecialchars($cliente['endereco']) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="grid-2 gap-16">

    <!-- PROPOSTAS DO CLIENTE -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Propostas</div>
                <div class="card-subtitle"><?= count($cliProps) ?> proposta(s) neste cliente</div>
            </div>
            <a href="../propostas/create.php?cliente_id=<?= $id ?>" class="btn btn-secondary btn-sm">+ Nova</a>
        </div>
        <?php if (empty($cliProps)): ?>
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">📄</div>
                    <div class="empty-title">Nenhuma proposta</div>
                    <div class="empty-text">Crie a primeira proposta para este cliente</div>
                </div>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Código</th><th>Valor</th><th>Data</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($cliProps as $p): ?>
                    <tr>
                        <td class="td-mono"><?= htmlspecialchars($p['codigo']) ?></td>
                        <td style="font-family:var(--font-mono);font-size:.85rem;font-weight:600"><?= formatMoney($p['valor']) ?></td>
                        <td class="text-sm text-muted"><?= formatDate($p['data']) ?></td>
                        <td><?= badge(propostaStatusLabel($p['status']), propostaStatusClass($p['status'])) ?></td>
                        <td><a href="../propostas/view.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-xs">→</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- OS DO CLIENTE -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Ordens de Serviço</div>
                <div class="card-subtitle"><?= count($cliOS) ?> OS neste cliente</div>
            </div>
        </div>
        <?php if (empty($cliOS)): ?>
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <div class="empty-title">Nenhuma OS</div>
                    <div class="empty-text">Aprove uma proposta para criar uma OS</div>
                </div>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Código</th><th>Serviço</th><th>Status</th><th>Previsão</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($cliOS as $os): ?>
                    <tr>
                        <td class="td-mono"><?= htmlspecialchars($os['codigo']) ?></td>
                        <td class="text-sm"><?= htmlspecialchars(mb_strimwidth($os['tipo_servico'], 0, 28, '…')) ?></td>
                        <td><?= badge(osStatusLabel($os['status']), osStatusClass($os['status'])) ?></td>
                        <td class="text-sm text-muted"><?= formatDate($os['data_previsao']) ?></td>
                        <td><a href="../os/view.php?id=<?= $os['id'] ?>" class="btn btn-ghost btn-xs">→</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php renderFooter(); ?>
