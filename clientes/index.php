<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

renderHeader('Clientes', 'clientes');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <span>Clientes</span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <h2>Clientes</h2>
        <p><?= count($clientes) ?> clientes cadastrados</p>
    </div>
    <div class="page-header-actions">
        <a href="create.php" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Novo Cliente
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome / Empresa</th>
                    <th>CNPJ</th>
                    <th>Contato</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Propostas</th>
                    <th>OS</th>
                    <th>Cadastro</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clientes as $c):
                $qtdProp = count(findAllBy($propostas, 'cliente_id', $c['id']));
                $qtdOS   = count(findAllBy($ordens_servico, 'cliente_id', $c['id']));
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', $c['nome']))));
                $initials = mb_substr($initials, 0, 2);
            ?>
                <tr>
                    <td>
                        <div class="flex flex-center gap-12">
                            <div class="client-avatar" style="width:36px;height:36px;border-radius:8px;flex-shrink:0"><?= $initials ?></div>
                            <div>
                                <div class="td-name"><?= htmlspecialchars($c['nome']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="td-mono" style="font-size:.8rem"><?= htmlspecialchars($c['cnpj']) ?></td>
                    <td class="text-sm"><?= htmlspecialchars($c['contato']) ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars($c['telefone']) ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars($c['email']) ?></td>
                    <td><span class="badge badge-gray"><?= $qtdProp ?></span></td>
                    <td><span class="badge <?= $qtdOS > 0 ? 'badge-exec' : 'badge-gray' ?>"><?= $qtdOS ?></span></td>
                    <td class="text-sm text-muted"><?= formatDate($c['criado_em']) ?></td>
                    <td class="td-actions">
                        <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-xs">Ver →</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php renderFooter(); ?>
