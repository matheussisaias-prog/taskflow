<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$filtro    = $_GET['status'] ?? 'todos';
$filtradas = $filtro === 'todos' ? $ordens_servico : findAllBy($ordens_servico, 'status', $filtro);

$counts = [
    'todos'       => count($ordens_servico),
    'aberta'      => count(findAllBy($ordens_servico, 'status', 'aberta')),
    'em execucao' => count(findAllBy($ordens_servico, 'status', 'em execucao')),
    'em revisao'  => count(findAllBy($ordens_servico, 'status', 'em revisao')),
    'finalizada'  => count(findAllBy($ordens_servico, 'status', 'finalizada')),
    'faturada'    => count(findAllBy($ordens_servico, 'status', 'faturada')),
];

renderHeader('Ordens de Serviço', 'os');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <span>Ordens de Serviço</span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <h2>Ordens de Serviço</h2>
        <p>Centro de controle operacional — <?= $counts['todos'] ?> OS cadastradas</p>
    </div>
    <div class="page-header-actions">
        <a href="create.php" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Nova OS
        </a>
    </div>
</div>

<!-- FILTRO STATUS -->
<div class="os-status-bar">
    <a href="?status=todos"       class="status-filter-btn <?= $filtro==='todos'       ? 'active':'' ?>">Todas (<?= $counts['todos'] ?>)</a>
    <a href="?status=aberta"      class="status-filter-btn <?= $filtro==='aberta'      ? 'active':'' ?>">Abertas (<?= $counts['aberta'] ?>)</a>
    <a href="?status=em execucao" class="status-filter-btn <?= $filtro==='em execucao' ? 'active':'' ?>">Em Execução (<?= $counts['em execucao'] ?>)</a>
    <a href="?status=em revisao"  class="status-filter-btn <?= $filtro==='em revisao'  ? 'active':'' ?>">Em Revisão (<?= $counts['em revisao'] ?>)</a>
    <a href="?status=finalizada"  class="status-filter-btn <?= $filtro==='finalizada'  ? 'active':'' ?>">Finalizadas (<?= $counts['finalizada'] ?>)</a>
    <a href="?status=faturada"    class="status-filter-btn <?= $filtro==='faturada'    ? 'active':'' ?>">Faturadas (<?= $counts['faturada'] ?>)</a>
</div>

<!-- LISTA -->
<div class="card">
    <?php if (empty($filtradas)): ?>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Nenhuma OS com este status</div>
                <div class="empty-text">Selecione outro filtro ou crie uma nova OS</div>
            </div>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Tipo de Serviço</th>
                    <th>Cliente</th>
                    <th>Responsável</th>
                    <th>Status</th>
                    <th>Abertura</th>
                    <th>Previsão</th>
                    <th>Equipe</th>
                    <th>Custos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($filtradas as $os):
                $cliente = findById($clientes, (int)$os['cliente_id']);
                $membros = count(findAllBy($equipe_os, 'os_id', $os['id']));
                $custos  = count(findAllBy($custos_os,  'os_id', $os['id']));
                $totalCusto = calcCustoEquipe($equipe_os, $os['id']) + calcCustosDiretos($custos_os, $os['id']);
            ?>
                <tr style="cursor:pointer" onclick="window.location='view.php?id=<?= $os['id'] ?>'">
                    <td class="td-mono"><?= htmlspecialchars($os['codigo']) ?></td>
                    <td class="td-name" style="max-width:220px"><?= htmlspecialchars(mb_strimwidth($os['descricao'], 0, 58, '…')) ?></td>
                    <td class="text-sm"><?= htmlspecialchars($os['tipo_servico']) ?></td>
                    <td class="text-sm"><?= htmlspecialchars(mb_strimwidth($cliente['nome'] ?? '—', 0, 26, '…')) ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars($os['responsavel']) ?></td>
                    <td><?= badge(osStatusLabel($os['status']), osStatusClass($os['status'])) ?></td>
                    <td class="text-sm text-muted"><?= formatDate($os['data_abertura']) ?></td>
                    <td class="text-sm text-muted"><?= formatDate($os['data_previsao']) ?></td>
                    <td>
                        <?php if ($membros > 0): ?>
                            <span class="badge badge-gray"><?= $membros ?> membro<?= $membros>1?'s':'' ?></span>
                        <?php else: ?>
                            <span class="text-muted text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-family:var(--font-mono);font-size:.8rem;font-weight:600;white-space:nowrap;color:<?= $totalCusto > 0 ? 'var(--gray-700)' : 'var(--gray-300)' ?>">
                        <?= $totalCusto > 0 ? formatMoney($totalCusto) : '—' ?>
                    </td>
                    <td class="td-actions" onclick="event.stopPropagation()">
                        <a href="view.php?id=<?= $os['id'] ?>" class="btn btn-primary btn-xs">Abrir HUB →</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php renderFooter(); ?>
