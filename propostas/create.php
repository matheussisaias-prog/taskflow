<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$preCliente = (int)($_GET['cliente_id'] ?? 0);
$saved      = isset($_GET['saved']);

renderHeader('Nova Proposta', 'propostas');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="index.php">Propostas</a>
    <span class="breadcrumb-sep">›</span>
    <span>Nova Proposta</span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <h2>Nova Proposta Comercial</h2>
        <p>Vinculada a um cliente. Somente propostas aprovadas podem gerar OS.</p>
    </div>
</div>

<?php if ($saved): ?>
    <div class="alert alert-success">✅ Proposta criada com sucesso! (modo demonstração)</div>
<?php endif; ?>

<div class="card" style="max-width:760px">
    <div class="card-header"><div class="card-title">Dados da Proposta</div></div>
    <div class="card-body">
        <form method="POST" action="create.php" onsubmit="this.action='create.php?saved=1'">
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">Cliente <span class="req">*</span></label>
                    <select name="cliente_id" class="form-control" required>
                        <option value="">— Selecione o cliente —</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $preCliente === (int)$c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group span-2">
                    <label class="form-label">Descrição do Serviço <span class="req">*</span></label>
                    <textarea name="descricao" class="form-control" rows="4" placeholder="Descreva detalhadamente o serviço a ser executado..." required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Valor (R$) <span class="req">*</span></label>
                    <input type="number" name="valor" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Data da Proposta <span class="req">*</span></label>
                    <input type="date" name="data" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Validade</label>
                    <input type="date" name="validade" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                    <span class="form-hint">Padrão: 30 dias</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Inicial</label>
                    <select name="status" class="form-control">
                        <option value="pendente">Pendente</option>
                        <option value="aprovada">Aprovada</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Proposta</button>
            </div>
        </form>
    </div>
</div>

<?php renderFooter(); ?>
