<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$filtro     = $_GET['status'] ?? 'todos';
$filtradas  = $filtro === 'todos' ? $propostas : findAllBy($propostas, 'status', $filtro);

$totalProp  = count($propostas);
$pendentes  = count(findAllBy($propostas, 'status', 'pendente'));
$aprovadas  = count(findAllBy($propostas, 'status', 'aprovada'));
$recusadas  = count(findAllBy($propostas, 'status', 'recusada'));

renderHeader('Propostas', 'propostas');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <span>Propostas</span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <h2>Propostas Comerciais</h2>
        <p><?= $totalProp ?> propostas — <?= $aprovadas ?> aprovadas, <?= $pendentes ?> pendentes</p>
    </div>
    <div class="page-header-actions">
        <a href="create.php" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Nova Proposta
        </a>
    </div>
</div>

<!-- STATUS FILTER BAR -->
<div class="os-status-bar">
    <a href="?status=todos" class="status-filter-btn <?= $filtro === 'todos' ? 'active' : '' ?>">Todas (<?= $totalProp ?>)</a>
    <a href="?status=pendente" class="status-filter-btn <?= $filtro === 'pendente' ? 'active' : '' ?>">Pendentes (<?= $pendentes ?>)</a>
    <a href="?status=aprovada" class="status-filter-btn <?= $filtro === 'aprovada' ? 'active' : '' ?>">Aprovadas (<?= $aprovadas ?>)</a>
    <a href="?status=recusada" class="status-filter-btn <?= $filtro === 'recusada' ? 'active' : '' ?>">Recusadas (<?= $recusadas ?>)</a>
</div>

<div class="card">
    <?php if (empty($filtradas)): ?>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Nenhuma proposta encontrada</div>
                <div class="empty-text">Ajuste o filtro ou crie uma nova proposta</div>
            </div>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th>OS Gerada</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($filtradas as $p):
                $cliente = findById($clientes, (int)$p['cliente_id']);
                $osGerada = array_filter($ordens_servico, fn($os) => (int)$os['proposta_id'] === (int)$p['id']);
                $osGerada = reset($osGerada);
            ?>
                <tr>
                    <td class="td-mono"><?= htmlspecialchars($p['codigo']) ?></td>
                    <td>
                        <a href="../clientes/view.php?id=<?= $p['cliente_id'] ?>" style="color:var(--accent);text-decoration:none;font-size:.86rem;font-weight:500">
                            <?= htmlspecialchars(mb_strimwidth($cliente['nome'] ?? '—', 0, 28, '…')) ?>
                        </a>
                    </td>
                    <td class="text-sm" style="max-width:240px"><?= htmlspecialchars(mb_strimwidth($p['descricao'], 0, 70, '…')) ?></td>
                    <td style="font-family:var(--font-mono);font-size:.88rem;font-weight:600;white-space:nowrap"><?= formatMoney($p['valor']) ?></td>
                    <td class="text-sm text-muted"><?= formatDate($p['data']) ?></td>
                    <td class="text-sm text-muted"><?= formatDate($p['validade']) ?></td>
                    <td><?= badge(propostaStatusLabel($p['status']), propostaStatusClass($p['status'])) ?></td>
                    <td>
                        <?php if ($osGerada): ?>
                            <a href="../os/view.php?id=<?= $osGerada['id'] ?>" class="td-mono" style="font-size:.78rem">
                                <?= htmlspecialchars($osGerada['codigo']) ?>
                            </a>
                        <?php elseif ($p['status'] === 'aprovada'): ?>
                            <a href="../os/create.php?proposta_id=<?= $p['id'] ?>" class="btn btn-success btn-xs">+ Criar OS</a>
                        <?php else: ?>
                            <span class="text-muted text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-actions" style="white-space:nowrap">
                        <?php if ($p['status'] === 'pendente'): ?>
                            <a href="view.php?id=<?= $p['id'] ?>&aprovar=1" class="btn btn-success btn-xs" onclick="return confirmAction('Aprovar esta proposta?')">Aprovar</a>
                        <?php endif; ?>
                        <a href="view.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-xs">Ver →</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php renderFooter(); ?>
