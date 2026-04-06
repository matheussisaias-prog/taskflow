<?php
require_once 'includes/auth_check.php';

$leads_all = db_all('leads','id DESC');
foreach ($leads_all as &$l) {
    $u = db_find('users',(int)($l['responsavel_id']??0));
    $l['responsavel_nome'] = $u['nome']??'–';
}
$vendedores = db_where_raw('users',"perfil IN ('admin','gerente','comercial') AND ativo=1", [], 'nome ASC');

$etapas_config = [
    'lead_recebido'    => ['label'=>'Lead Recebido',    'color'=>'#3b82f6'],
    'contato_realizado'=> ['label'=>'Contato Realizado','color'=>'#8b5cf6'],
    'proposta_enviada' => ['label'=>'Proposta Enviada', 'color'=>'#f97316'],
    'negociacao'       => ['label'=>'Negociação',       'color'=>'#f59e0b'],
    'fechado'          => ['label'=>'Fechado',          'color'=>'#10b981'],
    'perdido'          => ['label'=>'Perdido',          'color'=>'#ef4444'],
];
$kanban = [];
foreach($leads_all as $l) $kanban[$l['etapa']][] = $l;

$total    = count($leads_all);
$fechados = count($kanban['fechado']??[]);
$em_neg   = count($kanban['negociacao']??[]);
$prop_env = count($kanban['proposta_enviada']??[]);
$taxa     = $total > 0 ? round($fechados/$total*100,1) : 0;

include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left"><h1>Comercial</h1><p>Funil de vendas e gestão de leads</p></div>
  <div style="display:flex;gap:10px">
    <button class="btn btn-secondary btn-sm" id="btnKanban"><i class="fa-solid fa-table-columns"></i> Kanban</button>
    <button class="btn btn-secondary btn-sm" id="btnLista"><i class="fa-solid fa-list"></i> Lista</button>
    <button class="btn btn-primary" onclick="openModal('modalLead')"><i class="fa-solid fa-plus"></i> Novo Lead</button>
  </div>
</div>

<div class="kpi-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px">
  <div class="kpi-card" style="--kpi-color:var(--ts-blue);--kpi-soft:var(--blue-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-users"></i></div><div><div class="kpi-label">Total Leads</div><div class="kpi-value"><?= $total ?></div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--purple);--kpi-soft:var(--purple-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-handshake"></i></div><div><div class="kpi-label">Negociação</div><div class="kpi-value"><?= $em_neg ?></div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--orange);--kpi-soft:var(--orange-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-paper-plane"></i></div><div><div class="kpi-label">Proposta Enviada</div><div class="kpi-value"><?= $prop_env ?></div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--green);--kpi-soft:var(--green-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-circle-check"></i></div><div><div class="kpi-label">Fechados</div><div class="kpi-value"><?= $fechados ?></div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--ts-gold);--kpi-soft:var(--ts-gold-lt)"><div class="kpi-icon-wrap"><i class="fa-solid fa-percent"></i></div><div><div class="kpi-label">Conversão</div><div class="kpi-value"><?= $taxa ?>%</div></div></div>
</div>

<!-- KANBAN -->
<div id="viewKanban">
<div class="kanban-board">
<?php foreach($etapas_config as $ekey=>$cfg): ?>
<div class="kanban-col" id="col-<?= $ekey ?>" data-etapa="<?= $ekey ?>">
  <div class="kanban-col-header">
    <div class="kanban-col-title"><div class="dot" style="background:<?= $cfg['color'] ?>"></div><?= $cfg['label'] ?></div>
    <div class="kanban-count"><?= count($kanban[$ekey]??[]) ?></div>
  </div>
  <div class="kanban-cards" id="cards-<?= $ekey ?>">
    <?php foreach(($kanban[$ekey]??[]) as $l): ?>
    <div class="kanban-card" draggable="true" data-id="<?= $l['id'] ?>" onclick="viewLead(<?= $l['id'] ?>)">
      <div class="kanban-card-name"><?= htmlspecialchars($l['nome']) ?></div>
      <div class="kanban-card-company"><?= htmlspecialchars($l['empresa_nome']??'') ?></div>
      <div class="kanban-card-meta">
        <span class="badge temp-<?= $l['temperatura'] ?>" style="font-size:10px"><?= ucfirst($l['temperatura']) ?></span>
        <span class="kanban-card-value"><?= $l['valor_estimado']>0?moeda((float)$l['valor_estimado']):'' ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- LISTA -->
<div id="viewLista" style="display:none">
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-users"></i> Todos os Leads (<?= $total ?>)</div>
    <div class="filters-bar" style="margin:0;flex:1;justify-content:flex-end">
      <div class="filter-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="leadSearch" placeholder="Buscar..."></div>
      <select class="filter-select" id="filterEtapa" onchange="filterLeads()">
        <option value="">Todas as etapas</option>
        <?php foreach($etapas_config as $k=>$c): ?><option value="<?= $k ?>"><?= $c['label'] ?></option><?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="table-wrapper">
    <table class="data-table" id="tableLeads">
      <thead><tr><th>Nome</th><th>Empresa</th><th>Contato</th><th>Etapa</th><th>Temp.</th><th>Valor</th><th>Responsável</th><th></th></tr></thead>
      <tbody>
      <?php
      $etb=['lead_recebido'=>['Recebido','badge-blue'],'contato_realizado'=>['Contato','badge-purple'],'proposta_enviada'=>['Proposta','badge-orange'],'negociacao'=>['Negociação','badge-yellow'],'fechado'=>['Fechado','badge-green'],'perdido'=>['Perdido','badge-red']];
      foreach($leads_all as $l):
        [$el,$ec]=$etb[$l['etapa']]??['?','badge-gray'];
      ?>
      <tr data-etapa="<?= $l['etapa'] ?>">
        <td><div class="avatar-pill"><div class="avatar-sm" style="background:#<?= substr(md5($l['nome']),0,6) ?>"><?= strtoupper(substr($l['nome'],0,2)) ?></div><?= htmlspecialchars($l['nome']) ?></div></td>
        <td><?= htmlspecialchars($l['empresa_nome']??'–') ?></td>
        <td style="font-size:12px"><?= htmlspecialchars($l['telefone']??'') ?><br><span style="color:var(--text3)"><?= htmlspecialchars($l['email']??'') ?></span></td>
        <td><span class="badge <?= $ec ?>"><?= $el ?></span></td>
        <td><span class="badge temp-<?= $l['temperatura'] ?>"><?= ucfirst($l['temperatura']) ?></span></td>
        <td style="font-family:'DM Mono',monospace;color:var(--green);font-weight:700"><?= $l['valor_estimado']>0?moeda((float)$l['valor_estimado']):'–' ?></td>
        <td><?= htmlspecialchars($l['responsavel_nome']??'–') ?></td>
        <td><div style="display:flex;gap:6px">
          <button class="btn btn-ghost btn-sm btn-icon" onclick="viewLead(<?= $l['id'] ?>)"><i class="fa-solid fa-eye"></i></button>
          <button class="btn btn-ghost btn-sm btn-icon" onclick="moverLead(<?= $l['id'] ?>,'<?= $l['etapa'] ?>')"><i class="fa-solid fa-arrows-turn-to-dots"></i></button>
        </div></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<!-- MODAL NOVO LEAD -->
<div class="modal-overlay" id="modalLead" style="display:none">
<div class="modal-box">
  <div class="modal-header"><div class="modal-title"><i class="fa-solid fa-user-plus" style="color:var(--ts-blue)"></i> Novo Lead</div><button class="modal-close" onclick="closeModal('modalLead')"><i class="fa-solid fa-xmark"></i></button></div>
  <div class="modal-body">
    <form id="formLead">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Nome *</label><input type="text" class="form-control" name="nome" required placeholder="Nome completo"></div>
        <div class="form-group"><label class="form-label">Empresa</label><input type="text" class="form-control" name="empresa_nome" placeholder="Nome da empresa"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" name="email" placeholder="email@empresa.com"></div>
        <div class="form-group"><label class="form-label">Telefone</label><input type="text" class="form-control" name="telefone" data-mask="phone" placeholder="(00) 00000-0000"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Origem</label>
          <select class="form-control" name="origem"><option value="site">Site</option><option value="indicacao">Indicação</option><option value="linkedin">LinkedIn</option><option value="evento">Evento</option><option value="ligacao">Ligação</option><option value="email">E-mail</option><option value="outro">Outro</option></select>
        </div>
        <div class="form-group"><label class="form-label">Temperatura</label>
          <select class="form-control" name="temperatura"><option value="frio">🔵 Frio</option><option value="morno" selected>🟡 Morno</option><option value="quente">🔴 Quente</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Responsável</label>
          <select class="form-control" name="responsavel_id">
            <?php foreach($vendedores as $v): ?><option value="<?= $v['id'] ?>" <?= $v['id']==$user_id?'selected':'' ?>><?= htmlspecialchars($v['nome']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Valor Estimado (R$)</label><input type="number" class="form-control" name="valor_estimado" step="0.01" placeholder="0,00"></div>
      </div>
      <div class="form-group"><label class="form-label">Notas</label><textarea class="form-control" name="notas" placeholder="Observações sobre este lead..."></textarea></div>
    </form>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('modalLead')">Cancelar</button><button class="btn btn-primary" onclick="saveLead()"><i class="fa-solid fa-save"></i> Salvar</button></div>
</div>
</div>

<!-- MODAL VER LEAD -->
<div class="modal-overlay" id="modalViewLead" style="display:none">
<div class="modal-box" style="max-width:700px">
  <div class="modal-header">
    <div class="modal-title" id="viewLeadTitle">Lead</div>
    <button class="modal-close" onclick="closeModal('modalViewLead')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body" id="viewLeadBody">Carregando...</div>
</div>
</div>

<!-- MODAL MOVER ETAPA -->
<div class="modal-overlay" id="modalEtapa" style="display:none">
<div class="modal-box" style="max-width:400px">
  <div class="modal-header"><div class="modal-title"><i class="fa-solid fa-arrows-turn-to-dots"></i> Mover Lead</div><button class="modal-close" onclick="closeModal('modalEtapa')"><i class="fa-solid fa-xmark"></i></button></div>
  <div class="modal-body">
    <input type="hidden" id="etapaLeadId">
    <div class="form-group"><label class="form-label">Nova Etapa</label>
      <select class="form-control" id="novaEtapa">
        <?php foreach($etapas_config as $k=>$c): ?><option value="<?= $k ?>"><?= $c['label'] ?></option><?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('modalEtapa')">Cancelar</button><button class="btn btn-primary" onclick="salvarEtapa()">Mover</button></div>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
// Toggle views
document.getElementById('btnKanban').onclick = () => {
  document.getElementById('viewKanban').style.display='';
  document.getElementById('viewLista').style.display='none';
};
document.getElementById('btnLista').onclick = () => {
  document.getElementById('viewKanban').style.display='none';
  document.getElementById('viewLista').style.display='';
};

// Drag & Drop Kanban
let dragId = null;
document.querySelectorAll('.kanban-card').forEach(card => {
  card.addEventListener('dragstart', e => { dragId = card.dataset.id; card.style.opacity='.5'; });
  card.addEventListener('dragend', e => { card.style.opacity='1'; });
});
document.querySelectorAll('.kanban-cards').forEach(zone => {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag-over');
    const etapa = zone.id.replace('cards-','');
    if (dragId) {
      fetch(BASE_URL + '/api/leads.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'update_etapa',id:parseInt(dragId),etapa})})
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else toast('Erro ao mover lead','error'); });
    }
  });
});

// Save lead
async function saveLead() {
  const form = document.getElementById('formLead');
  const fd = new FormData(form);
  fd.append('action', 'create');  // necessário para o backend identificar a ação
  const nome = fd.get('nome') ? fd.get('nome').trim() : '';
  if (!nome) { toast('Informe o nome do lead','error'); return; }
  const res = await fetch(BASE_URL + '/api/leads.php', { method:'POST', body: fd });
  const d = await res.json();
  if (d.success) { toast('Lead criado com sucesso!'); closeModal('modalLead'); setTimeout(()=>location.reload(),800); }
  else toast(d.error||'Erro ao salvar','error');
}

// View lead
async function viewLead(id) {
  openModal('modalViewLead');
  document.getElementById('viewLeadBody').innerHTML = '<div style="text-align:center;padding:40px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>';
  const d = await fetch(BASE_URL + '/api/leads.php?id='+id).then(r=>r.json());
  document.getElementById('viewLeadTitle').textContent = d.nome||'Lead';
  const etb={'lead_recebido':'badge-blue','contato_realizado':'badge-purple','proposta_enviada':'badge-orange','negociacao':'badge-yellow','fechado':'badge-green','perdido':'badge-red'};
  const etl={'lead_recebido':'Recebido','contato_realizado':'Contato Realizado','proposta_enviada':'Proposta Enviada','negociacao':'Negociação','fechado':'Fechado','perdido':'Perdido'};
  let atendHtml = '';
  (d.atendimentos||[]).forEach(a => {
    atendHtml += `<div style="padding:10px 0;border-bottom:1px solid var(--border)"><div style="font-size:12px;color:var(--text3);margin-bottom:4px">${a.tipo} · ${a.usuario_nome} · ${a.data_atendimento}</div><div style="font-size:13px">${a.descricao}</div></div>`;
  });
  document.getElementById('viewLeadBody').innerHTML = `
    <div style="padding:0">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:16px 0;border-bottom:1px solid var(--border);margin-bottom:16px">
        <div><div class="form-label">Empresa</div><div>${d.empresa_nome||'–'}</div></div>
        <div><div class="form-label">Contato</div><div>${d.telefone||'–'} ${d.email?'· '+d.email:''}</div></div>
        <div><div class="form-label">Etapa</div><div><span class="badge ${etb[d.etapa]||'badge-gray'}">${etl[d.etapa]||d.etapa}</span></div></div>
        <div><div class="form-label">Temperatura</div><div><span class="badge temp-${d.temperatura}">${d.temperatura}</span></div></div>
        <div><div class="form-label">Valor Estimado</div><div style="font-family:'DM Mono',monospace;color:var(--green);font-weight:700">${d.valor_estimado>0?'R$ '+parseFloat(d.valor_estimado).toLocaleString('pt-BR',{minimumFractionDigits:2}):'–'}</div></div>
        <div><div class="form-label">Responsável</div><div>${d.responsavel_nome||'–'}</div></div>
      </div>
      ${d.notas?`<div style="padding:12px;background:var(--surface);border-radius:8px;font-size:13px;margin-bottom:16px">${d.notas}</div>`:''}
      <div class="form-label" style="margin-bottom:10px">Atendimentos</div>
      <div style="max-height:200px;overflow-y:auto">${atendHtml||'<div style="color:var(--text3);font-size:13px">Nenhum atendimento registrado.</div>'}</div>
      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Tipo</label><select class="form-control" id="atTipo"><option value="nota">Nota</option><option value="ligacao">Ligação</option><option value="email">E-mail</option><option value="reuniao">Reunião</option><option value="whatsapp">WhatsApp</option></select></div>
        </div>
        <div class="form-group"><label class="form-label">Descrição</label><textarea class="form-control" id="atDesc" placeholder="Descreva o atendimento..."></textarea></div>
        <button class="btn btn-primary btn-sm" onclick="addAtendimento(${d.id})"><i class="fa-solid fa-plus"></i> Registrar</button>
      </div>
    </div>`;
}

async function addAtendimento(leadId) {
  const desc = document.getElementById('atDesc').value.trim();
  const tipo = document.getElementById('atTipo').value;
  if (!desc) { toast('Informe a descrição','error'); return; }
  const res = await api(BASE_URL + '/api/leads.php', {action:'add_atendimento',lead_id:leadId,tipo,descricao:desc});
  if (res.success) { toast('Atendimento registrado!'); viewLead(leadId); }
  else toast(res.error||'Erro','error');
}

function moverLead(id, etapaAtual) {
  document.getElementById('etapaLeadId').value = id;
  document.getElementById('novaEtapa').value = etapaAtual;
  openModal('modalEtapa');
}
async function salvarEtapa() {
  const id    = parseInt(document.getElementById('etapaLeadId').value);
  const etapa = document.getElementById('novaEtapa').value;
  const res   = await api(BASE_URL + '/api/leads.php', {action:'update_etapa',id,etapa});
  if (res.success) { toast('Lead movido!'); closeModal('modalEtapa'); setTimeout(()=>location.reload(),600); }
  else toast(res.error||'Erro','error');
}

// Filter lista
function filterLeads() {
  const etapa = document.getElementById('filterEtapa').value;
  document.querySelectorAll('#tableLeads tbody tr').forEach(tr => {
    tr.style.display = (!etapa || tr.dataset.etapa===etapa) ? '' : 'none';
  });
}
document.getElementById('leadSearch')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#tableLeads tbody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
<?php include 'includes/footer.php'; ?>
