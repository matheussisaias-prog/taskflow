<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$prePropostaId = (int)($_GET['proposta_id'] ?? 0);
$preClienteId  = (int)($_GET['cliente_id'] ?? 0);
$saved         = isset($_GET['saved']);

$preProposta = $prePropostaId ? findById($propostas, $prePropostaId) : null;
if ($preProposta) {
    $preClienteId = (int)$preProposta['cliente_id'];
}

// Somente propostas aprovadas e sem OS gerada
$propostasAprovadas = [];
foreach ($propostas as $p) {
    if ($p['status'] !== 'aprovada') continue;
    $temOS = false;
    foreach ($ordens_servico as $os) {
        if ((int)$os['proposta_id'] === (int)$p['id']) { $temOS = true; break; }
    }
    if (!$temOS) $propostasAprovadas[] = $p;
}

$tiposServico = [
    'Levantamento Planialtimétrico',
    'Georreferenciamento Rural',
    'Georreferenciamento Urbano',
    'Projeto de Drenagem',
    'Demarcação de Lotes',
    'Locação e Implantação',
    'Monitoramento Geodésico',
    'Batimetria',
    'Mapeamento Cadastral',
    'Levantamento Topobatimétrico',
    'Outros',
];

$responsaveis = [
    'Eng. Carlos Drummond',
    'Eng. Beatriz Santos',
    'Eng. Rafael Torres',
    'Téc. Pedro Gomes',
];

renderHeader('Nova OS', 'os');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="index.php">Ordens de Serviço</a>
    <span class="breadcrumb-sep">›</span>
    <span>Nova OS</span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <h2>Nova Ordem de Serviço</h2>
        <p>Toda OS deve estar vinculada a um cliente e a uma proposta aprovada</p>
    </div>
</div>

<?php if ($saved): ?>
    <div class="alert alert-success">✅ Ordem de Serviço criada com sucesso! (modo demonstração)</div>
<?php endif; ?>

<?php if (empty($propostasAprovadas) && !$preProposta): ?>
    <div class="alert alert-warning">
        <strong>Atenção:</strong> Não há propostas aprovadas disponíveis para gerar uma OS.
        <a href="../propostas/index.php" style="font-weight:600;margin-left:6px">Gerenciar Propostas →</a>
    </div>
<?php endif; ?>

<div class="card" style="max-width:860px">
    <div class="card-header"><div class="card-title">Dados da Ordem de Serviço</div></div>
    <div class="card-body">
        <form method="POST" action="create.php" onsubmit="this.action='create.php?saved=1'">

            <div class="form-grid">

                <!-- VINCULAÇÃO -->
                <div class="form-group">
                    <label class="form-label">Cliente <span class="req">*</span></label>
                    <select name="cliente_id" id="sel_cliente" class="form-control" required onchange="filterPropostas(this.value)">
                        <option value="">— Selecione o cliente —</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $preClienteId===$c['id']?'selected':'' ?>>
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Proposta Aprovada <span class="req">*</span></label>
                    <select name="proposta_id" id="sel_proposta" class="form-control" required>
                        <option value="">— Selecione a proposta —</option>
                        <?php
                        $listar = $preProposta ? [$preProposta] : $propostasAprovadas;
                        foreach ($listar as $p):
                            $cl = findById($clientes, (int)$p['cliente_id']);
                        ?>
                            <option value="<?= $p['id'] ?>" <?= $prePropostaId===$p['id']?'selected':'' ?>>
                                <?= htmlspecialchars($p['codigo']) ?> — <?= formatMoney($p['valor']) ?>
                                <?= $cl ? ' (' . htmlspecialchars(mb_strimwidth($cl['nome'],0,24,'…')) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($preProposta): ?>
                        <span class="form-hint">✅ Valor da proposta: <?= formatMoney($preProposta['valor']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- IDENTIFICAÇÃO -->
                <div class="form-group">
                    <label class="form-label">Código da OS</label>
                    <input type="text" name="codigo" class="form-control" value="OS-<?= date('Y') ?>-00<?= count($ordens_servico)+1 ?>" placeholder="OS-2024-007">
                    <span class="form-hint">Gerado automaticamente</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de Serviço <span class="req">*</span></label>
                    <select name="tipo_servico" class="form-control" required>
                        <option value="">— Selecione —</option>
                        <?php foreach ($tiposServico as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group span-2">
                    <label class="form-label">Descrição Completa <span class="req">*</span></label>
                    <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva o serviço a ser executado nesta OS..." required><?= $preProposta ? htmlspecialchars(mb_strimwidth($preProposta['descricao'],0,200,'')) : '' ?></textarea>
                </div>

                <!-- RESPONSÁVEL E DATAS -->
                <div class="form-group">
                    <label class="form-label">Responsável Técnico <span class="req">*</span></label>
                    <select name="responsavel" class="form-control" required>
                        <option value="">— Selecione —</option>
                        <?php foreach ($responsaveis as $r): ?>
                            <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Inicial</label>
                    <select name="status" class="form-control">
                        <option value="aberta">Aberta</option>
                        <option value="em execucao">Em Execução</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Data de Abertura <span class="req">*</span></label>
                    <input type="date" name="data_abertura" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Data de Início</label>
                    <input type="date" name="data_inicio" class="form-control">
                    <span class="form-hint">Quando o trabalho de campo começa</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Previsão de Conclusão</label>
                    <input type="date" name="data_previsao" class="form-control">
                </div>

                <div class="form-group span-2">
                    <label class="form-label">Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2" placeholder="Informações adicionais, requisitos específicos, condições de campo..."></textarea>
                </div>

            </div>
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Criar Ordem de Serviço</button>
            </div>
        </form>
    </div>
</div>

<script>
// Filtrar propostas pelo cliente selecionado
const allPropostas = <?= json_encode(array_values($propostasAprovadas)) ?>;
const allClientes  = <?= json_encode($clientes) ?>;

function filterPropostas(clienteId) {
    const sel = document.getElementById('sel_proposta');
    sel.innerHTML = '<option value="">— Selecione a proposta —</option>';
    const filtered = clienteId
        ? allPropostas.filter(p => String(p.cliente_id) === String(clienteId))
        : allPropostas;
    filtered.forEach(p => {
        const cl = allClientes.find(c => c.id == p.cliente_id);
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.codigo} — R$ ${parseFloat(p.valor).toFixed(2).replace('.',',')} (${cl ? cl.nome.substring(0,24) : ''})`;
        sel.appendChild(opt);
    });
}
</script>

<?php renderFooter(); ?>
