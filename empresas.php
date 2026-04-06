<?php
require_once 'includes/auth_check.php';
include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left"><h1>Empresas / Clientes</h1><p>Cadastro de clientes e parceiros</p></div>
  <div style="display:flex;gap:10px">
    <div class="filter-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="empSearch" placeholder="Buscar empresa..."></div>
    <button class="btn btn-primary" onclick="novaEmpresa()"><i class="fa-solid fa-plus"></i> Nova Empresa</button>
  </div>
</div>

<div id="empGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
  <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>
</div>

<!-- MODAL EMPRESA -->
<div class="modal-overlay" id="modalEmpresa" style="display:none">
<div class="modal-box" style="max-width:620px">
  <div class="modal-header">
    <div class="modal-title"><i class="fa-solid fa-building" style="color:var(--ts-blue)"></i> <span id="modalEmpTitle">Nova Empresa</span></div>
    <button class="modal-close" onclick="closeModal('modalEmpresa')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body">
    <input type="hidden" id="empId">
    <div class="form-row">
      <div class="form-group"><label class="form-label">Nome *</label><input type="text" class="form-control" id="empNome" placeholder="Razão social ou nome fantasia"></div>
      <div class="form-group"><label class="form-label">CNPJ / CPF</label><input type="text" class="form-control" id="empCnpj" data-mask="cnpj" placeholder="00.000.000/0000-00"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">E-mail</label><input type="email" class="form-control" id="empEmail" placeholder="empresa@email.com"></div>
      <div class="form-group"><label class="form-label">Telefone</label><input type="text" class="form-control" id="empTel" data-mask="phone" placeholder="(00) 00000-0000"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Contato Principal</label><input type="text" class="form-control" id="empContato" placeholder="Nome do responsável"></div>
      <div class="form-group"><label class="form-label">Segmento</label>
        <select class="form-control" id="empSegmento">
          <option value="">Selecione...</option>
          <option>Mineração</option><option>Energia</option><option>Infraestrutura</option>
          <option>Agronegócio</option><option>Indústria</option><option>Governo</option>
          <option>Construtora</option><option>Consultoria</option><option>Outros</option>
        </select>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Endereço</label><input type="text" class="form-control" id="empEndereco" placeholder="Rua, número, bairro"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Cidade</label><input type="text" class="form-control" id="empCidade" placeholder="Cidade"></div>
      <div class="form-group"><label class="form-label">Estado (UF)</label><input type="text" class="form-control" id="empEstado" maxlength="2" placeholder="CE"></div>
    </div>
    <div class="form-group"><label class="form-label">Notas</label><textarea class="form-control" id="empNotas" rows="2" placeholder="Observações internas..."></textarea></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalEmpresa')">Cancelar</button>
    <button class="btn btn-primary" onclick="salvarEmpresa()"><i class="fa-solid fa-save"></i> Salvar</button>
  </div>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
async function loadEmpresas() {
  const q = document.getElementById('empSearch').value.toLowerCase();
  const lista = await fetch(BASE_URL + '/api/empresas.php').then(r=>r.json());
  const filtered = q ? lista.filter(e=>(e.nome+e.cidade+e.segmento).toLowerCase().includes(q)) : lista;
  const grid = document.getElementById('empGrid');
  if (!filtered.length) {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--text3)"><i class="fa-solid fa-building fa-3x" style="display:block;margin-bottom:16px;opacity:.2"></i><h3>Nenhuma empresa cadastrada</h3></div>';
    return;
  }
  grid.innerHTML = filtered.map(e=>`
    <div class="card" style="padding:20px;transition:all var(--tr)" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px">
        <div style="display:flex;align-items:center;gap:10px">
          <div style="width:44px;height:44px;background:linear-gradient(135deg,var(--ts-blue),var(--ts-blue-dk));border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'Syne',sans-serif;font-size:16px;font-weight:800;flex-shrink:0">${e.nome.slice(0,2).toUpperCase()}</div>
          <div>
            <div style="font-weight:700;font-size:14px">${e.nome}</div>
            ${e.segmento?`<span class="badge badge-blue" style="font-size:11px">${e.segmento}</span>`:''}
          </div>
        </div>
        <div style="display:flex;gap:5px">
          <button class="btn btn-ghost btn-sm btn-icon" onclick="editarEmpresa(${e.id})"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-danger btn-sm btn-icon" onclick="deletarEmpresa(${e.id})"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
      <div style="font-size:12.5px;color:var(--text2);display:flex;flex-direction:column;gap:5px">
        ${e.cnpj?`<div><i class="fa-solid fa-id-card" style="width:16px;color:var(--text3)"></i> ${e.cnpj}</div>`:''}
        ${e.contato?`<div><i class="fa-solid fa-user" style="width:16px;color:var(--text3)"></i> ${e.contato}</div>`:''}
        ${e.telefone?`<div><i class="fa-solid fa-phone" style="width:16px;color:var(--text3)"></i> ${e.telefone}</div>`:''}
        ${e.email?`<div><i class="fa-solid fa-envelope" style="width:16px;color:var(--text3)"></i> ${e.email}</div>`:''}
        ${e.cidade?`<div><i class="fa-solid fa-location-dot" style="width:16px;color:var(--text3)"></i> ${e.cidade}${e.estado?' - '+e.estado:''}</div>`:''}
      </div>
    </div>`).join('');
}

document.getElementById('empSearch').addEventListener('input', loadEmpresas);

function novaEmpresa() {
  document.getElementById('empId').value='';
  document.getElementById('modalEmpTitle').textContent='Nova Empresa';
  ['empNome','empCnpj','empEmail','empTel','empContato','empEndereco','empCidade','empEstado','empNotas'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('empSegmento').selectedIndex=0;
  openModal('modalEmpresa');
}

async function editarEmpresa(id) {
  const e = await fetch(BASE_URL + '/api/empresas.php?id='+id).then(r=>r.json());
  document.getElementById('empId').value    = e.id;
  document.getElementById('modalEmpTitle').textContent = 'Editar: ' + e.nome;
  document.getElementById('empNome').value    = e.nome||'';
  document.getElementById('empCnpj').value    = e.cnpj||'';
  document.getElementById('empEmail').value   = e.email||'';
  document.getElementById('empTel').value     = e.telefone||'';
  document.getElementById('empContato').value = e.contato||'';
  document.getElementById('empSegmento').value= e.segmento||'';
  document.getElementById('empEndereco').value= e.endereco||'';
  document.getElementById('empCidade').value  = e.cidade||'';
  document.getElementById('empEstado').value  = e.estado||'';
  document.getElementById('empNotas').value   = e.notas||'';
  openModal('modalEmpresa');
}

async function salvarEmpresa() {
  const id   = parseInt(document.getElementById('empId').value)||0;
  const nome = document.getElementById('empNome').value.trim();
  if (!nome) { toast('Informe o nome da empresa','error'); return; }
  const payload = {
    action: id?'update':'create', id,
    nome, cnpj: document.getElementById('empCnpj').value,
    email: document.getElementById('empEmail').value,
    telefone: document.getElementById('empTel').value,
    contato: document.getElementById('empContato').value,
    segmento: document.getElementById('empSegmento').value,
    endereco: document.getElementById('empEndereco').value,
    cidade: document.getElementById('empCidade').value,
    estado: document.getElementById('empEstado').value,
    notas: document.getElementById('empNotas').value,
  };
  const res = await api(BASE_URL + '/api/empresas.php', payload);
  if (res.success) { toast('Empresa salva!'); closeModal('modalEmpresa'); loadEmpresas(); }
  else toast(res.error||'Erro','error');
}

async function deletarEmpresa(id) {
  if (!confirm('Excluir esta empresa?')) return;
  const res = await api(BASE_URL + '/api/empresas.php',{action:'delete',id});
  if (res.success) { toast('Empresa excluída'); loadEmpresas(); }
  else toast(res.error||'Erro','error');
}

loadEmpresas();
</script>
<?php include 'includes/footer.php'; ?>
