<?php
require_once 'includes/auth_check.php';
if (!can('admin')) { header('Location: ' . BASE_URL . '/dashboard.php'); exit; }
include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left"><h1>Configurações</h1><p>Gestão de usuários e configurações do sistema</p></div>
  <button class="btn btn-primary" onclick="openModal('modalUser')"><i class="fa-solid fa-user-plus"></i> Novo Usuário</button>
</div>

<div class="grid-2" style="align-items:start">
  <!-- Usuários -->
  <div class="card">
    <div class="card-header"><div class="card-title"><i class="fa-solid fa-users-cog"></i> Usuários do Sistema</div></div>
    <div class="table-wrapper">
      <table class="data-table" id="tableUsers">
        <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th></th></tr></thead>
        <tbody id="tbodyUsers"><tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin"></i></td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Info do sistema -->
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title"><i class="fa-solid fa-circle-info"></i> Informações do Sistema</div></div>
      <div class="card-body">
        <div style="display:flex;flex-direction:column;gap:12px;font-size:13.5px">
          <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
            <span style="color:var(--text2)">Sistema</span><strong>Terra System CRM</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
            <span style="color:var(--text2)">Versão</span><strong>2.0 MySQL</strong>
          </div>
          <div style="display:flex;justify-content:space-between;padding-bottom:10px;border-bottom:1px solid var(--border)">
            <span style="color:var(--text2)">Banco de Dados</span><strong id="dbInfo">Conectando...</strong>
          </div>
          <div style="display:flex;justify-content:space-between">
            <span style="color:var(--text2)">Data/Hora</span><strong><?= date('d/m/Y H:i') ?></strong>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title"><i class="fa-solid fa-user-circle"></i> Minha Conta</div></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px">
          <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,var(--ts-blue),var(--ts-blue-dk));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:#fff">
            <?= strtoupper(substr($user_nome,0,2)) ?>
          </div>
          <div>
            <div style="font-weight:700;font-size:16px"><?= htmlspecialchars($user_nome) ?></div>
            <div style="color:var(--text2);font-size:13px"><?= htmlspecialchars($user_email) ?></div>
            <span class="badge badge-blue" style="margin-top:4px"><?= ucfirst($user_perfil) ?></span>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Nova Senha</label><input type="password" class="form-control" id="novaSenha" placeholder="Deixe em branco para não alterar"></div>
        <div class="form-group"><label class="form-label">Confirmar Nova Senha</label><input type="password" class="form-control" id="confSenha" placeholder="Repita a nova senha"></div>
        <button class="btn btn-primary btn-sm" onclick="alterarSenha()"><i class="fa-solid fa-key"></i> Alterar Senha</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL USUÁRIO -->
<div class="modal-overlay" id="modalUser" style="display:none">
<div class="modal-box" style="max-width:500px">
  <div class="modal-header">
    <div class="modal-title"><i class="fa-solid fa-user-plus" style="color:var(--ts-blue)"></i> <span id="modalUserTitle">Novo Usuário</span></div>
    <button class="modal-close" onclick="closeModal('modalUser')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body">
    <input type="hidden" id="userId">
    <div class="form-row">
      <div class="form-group"><label class="form-label">Nome Completo *</label><input type="text" class="form-control" id="uNome" placeholder="Nome completo"></div>
      <div class="form-group"><label class="form-label">E-mail *</label><input type="email" class="form-control" id="uEmail" placeholder="email@terrasystem.com.br"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Perfil</label>
        <select class="form-control" id="uPerfil">
          <option value="tecnico">Técnico</option>
          <option value="comercial">Comercial</option>
          <option value="financeiro">Financeiro</option>
          <option value="gerente">Gerente</option>
          <option value="admin">Administrador</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Cargo</label><input type="text" class="form-control" id="uCargo" placeholder="Ex: Biólogo, Coord. Técnico"></div>
    </div>
    <div class="form-group"><label class="form-label">Telefone</label><input type="text" class="form-control" id="uTel" data-mask="phone" placeholder="(00) 00000-0000"></div>
    <div class="form-group"><label class="form-label" id="labelSenha">Senha *</label><input type="password" class="form-control" id="uSenha" placeholder="Mínimo 6 caracteres"></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalUser')">Cancelar</button>
    <button class="btn btn-primary" onclick="salvarUser()"><i class="fa-solid fa-save"></i> Salvar</button>
  </div>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
// Verificar DB
fetch(BASE_URL + '/api/users.php?action=ping')
  .then(r=>r.json())
  .then(d=>{ document.getElementById('dbInfo').textContent = d.ok ? 'MySQL ✓' : 'Erro'; })
  .catch(()=>{ document.getElementById('dbInfo').textContent = 'Offline'; });

async function loadUsers() {
  const lista = await fetch(BASE_URL + '/api/users.php').then(r=>r.json());
  const perfLabels = {admin:'Administrador',gerente:'Gerente',comercial:'Comercial',financeiro:'Financeiro',tecnico:'Técnico'};
  const perfBadge  = {admin:'badge-red',gerente:'badge-purple',comercial:'badge-orange',financeiro:'badge-green',tecnico:'badge-blue'};
  document.getElementById('tbodyUsers').innerHTML = lista.map(u=>`
    <tr>
      <td><div class="avatar-pill"><div class="avatar-sm" style="background:#${u.id.toString(16).padStart(6,'0').slice(0,6)}">${u.nome.slice(0,2).toUpperCase()}</div>${u.nome}</div></td>
      <td style="font-size:12.5px;color:var(--text2)">${u.email}</td>
      <td><span class="badge ${perfBadge[u.perfil]||'badge-gray'}">${perfLabels[u.perfil]||u.perfil}</span></td>
      <td><span class="badge ${u.ativo?'badge-green':'badge-red'}">${u.ativo?'Ativo':'Inativo'}</span></td>
      <td><div style="display:flex;gap:5px">
        <button class="btn btn-ghost btn-sm btn-icon" onclick="editarUser(${u.id})"><i class="fa-solid fa-pen"></i></button>
        ${u.id!=<?= $user_id ?>?`<button class="btn btn-danger btn-sm btn-icon" onclick="toggleUser(${u.id},${u.ativo})"><i class="fa-solid fa-${u.ativo?'ban':'check'}"></i></button>`:''}
      </div></td>
    </tr>`).join('');
}

function novoUser() {
  document.getElementById('userId').value='';
  document.getElementById('modalUserTitle').textContent='Novo Usuário';
  document.getElementById('labelSenha').textContent='Senha *';
  ['uNome','uEmail','uCargo','uTel','uSenha'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('uPerfil').value='tecnico';
  openModal('modalUser');
}
document.querySelector('[onclick="openModal(\'modalUser\')"]').addEventListener('click',novoUser);

async function editarUser(id) {
  const lista = await fetch(BASE_URL + '/api/users.php').then(r=>r.json());
  const u = lista.find(x=>x.id==id);
  if (!u) return;
  document.getElementById('userId').value = u.id;
  document.getElementById('modalUserTitle').textContent='Editar: '+u.nome;
  document.getElementById('labelSenha').textContent='Nova Senha (em branco = não altera)';
  document.getElementById('uNome').value  = u.nome;
  document.getElementById('uEmail').value = u.email;
  document.getElementById('uPerfil').value= u.perfil;
  document.getElementById('uCargo').value = u.cargo||'';
  document.getElementById('uTel').value   = u.telefone||'';
  document.getElementById('uSenha').value = '';
  openModal('modalUser');
}

async function salvarUser() {
  const id    = parseInt(document.getElementById('userId').value)||0;
  const nome  = document.getElementById('uNome').value.trim();
  const email = document.getElementById('uEmail').value.trim();
  const senha = document.getElementById('uSenha').value;
  if (!nome)  { toast('Nome obrigatório','error'); return; }
  if (!email) { toast('E-mail obrigatório','error'); return; }
  if (!id && !senha) { toast('Senha obrigatória','error'); return; }
  if (senha && senha.length<6) { toast('Senha mínima: 6 caracteres','error'); return; }
  const payload = {
    action: id?'update':'create', id, nome, email,
    perfil:   document.getElementById('uPerfil').value,
    cargo:    document.getElementById('uCargo').value,
    telefone: document.getElementById('uTel').value,
    senha:    senha||undefined,
  };
  const res = await api(BASE_URL + '/api/users.php', payload);
  if (res.success) { toast('Usuário salvo!'); closeModal('modalUser'); loadUsers(); }
  else toast(res.error||'Erro','error');
}

async function toggleUser(id, ativo) {
  if (!confirm(ativo?'Desativar este usuário?':'Reativar este usuário?')) return;
  const res = await api(BASE_URL + '/api/users.php',{action:'toggle',id,ativo:!ativo});
  if (res.success) { toast(ativo?'Usuário desativado':'Usuário reativado'); loadUsers(); }
}

async function alterarSenha() {
  const nova  = document.getElementById('novaSenha').value;
  const conf  = document.getElementById('confSenha').value;
  if (!nova) { toast('Informe a nova senha','error'); return; }
  if (nova.length<6) { toast('Mínimo 6 caracteres','error'); return; }
  if (nova!==conf) { toast('Senhas não coincidem','error'); return; }
  const res = await api(BASE_URL + '/api/users.php',{action:'change_password',senha:nova});
  if (res.success) { toast('Senha alterada com sucesso!'); document.getElementById('novaSenha').value=''; document.getElementById('confSenha').value=''; }
  else toast(res.error||'Erro','error');
}

loadUsers();
</script>
<?php include 'includes/footer.php'; ?>
