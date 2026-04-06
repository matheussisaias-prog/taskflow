<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';

$saved   = isset($_GET['saved']);
$message = $saved ? 'Cliente cadastrado com sucesso! (modo demonstração — dados não persistidos)' : '';

renderHeader('Novo Cliente', 'clientes');
?>

<div class="breadcrumb">
    <a href="../index.php">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="index.php">Clientes</a>
    <span class="breadcrumb-sep">›</span>
    <span>Novo Cliente</span>
</div>

<div class="page-header">
    <div class="page-header-info">
        <h2>Novo Cliente</h2>
        <p>Cadastre um novo cliente na base do sistema</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card" style="max-width:760px">
    <div class="card-header">
        <div class="card-title">Dados Cadastrais</div>
    </div>
    <div class="card-body">
        <form method="POST" action="create.php" onsubmit="this.action='create.php?saved=1'">
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">Razão Social <span class="req">*</span></label>
                    <input type="text" name="nome" class="form-control" placeholder="Nome completo da empresa" required>
                </div>
                <div class="form-group">
                    <label class="form-label">CNPJ <span class="req">*</span></label>
                    <input type="text" name="cnpj" class="form-control" placeholder="00.000.000/0001-00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nome do Contato</label>
                    <input type="text" name="contato" class="form-control" placeholder="Responsável pelo contato">
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" placeholder="email@empresa.com.br">
                </div>
                <div class="form-group span-2">
                    <label class="form-label">Endereço Completo</label>
                    <input type="text" name="endereco" class="form-control" placeholder="Rua, número, bairro, cidade/UF">
                </div>
            </div>
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Cliente</button>
            </div>
        </form>
    </div>
</div>

<?php renderFooter(); ?>
