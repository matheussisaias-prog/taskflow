<?php
require_once 'includes/auth_check.php';

$empresas   = db_all('empresas','nome ASC');
$responsaveis = db_where_raw('users',"ativo=1", [], 'nome ASC');
$propostas  = db_where_raw('propostas',"status IN ('aprovada','enviada','em_negociacao')", [], 'id DESC');

include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left"><h1>Ordens de Serviço</h1><p>Gestão de execução e custos</p></div>
  <div style="display:flex;gap:10px">
    <div class="filter-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="osSearch" placeholder="Buscar OS..."></div>
    <select class="filter-select" id="filterStatus" onchange="filterOS()">
      <option value="">Todos os status</option>
      <option value="aberta">Aberta</option>
      <option value="em_andamento">Em Andamento</option>
      <option value="aguardando">Aguardando</option>
      <option value="concluida">Concluída</option>
      <option value="cancelada">Cancelada</option>
    </select>
    <button class="btn btn-primary" onclick="openModal('modalOS')"><i class="fa-solid fa-plus"></i> Nova OS</button>
  </div>
</div>

<!-- KPIs -->
<div class="kpi-grid" id="osKpis" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px">
  <div class="kpi-card" style="--kpi-color:var(--ts-blue);--kpi-soft:var(--blue-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-folder-open"></i></div><div><div class="kpi-label">Abertas</div><div class="kpi-value" id="kpiAbertas">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--orange);--kpi-soft:var(--orange-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-gears"></i></div><div><div class="kpi-label">Em Andamento</div><div class="kpi-value" id="kpiAndamento">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--yellow);--kpi-soft:var(--yellow-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-clock"></i></div><div><div class="kpi-label">Aguardando</div><div class="kpi-value" id="kpiAguardando">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--green);--kpi-soft:var(--green-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-circle-check"></i></div><div><div class="kpi-label">Concluídas</div><div class="kpi-value" id="kpiConcluidas">–</div></div></div>
  <div class="kpi-card" style="--kpi-color:var(--purple);--kpi-soft:var(--purple-soft)"><div class="kpi-icon-wrap"><i class="fa-solid fa-coins"></i></div><div><div class="kpi-label">Receita Gerada</div><div class="kpi-value" id="kpiReceita" style="font-size:15px">–</div></div></div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title"><i class="fa-solid fa-screwdriver-wrench"></i> Lista de Ordens de Serviço</div></div>
  <div class="table-wrapper">
    <table class="data-table" id="tableOS">
      <thead><tr><th>Número</th><th>Título</th><th>Empresa</th><th>Responsável</th><th>Prioridade</th><th>Status</th><th>Prazo</th><th>Financeiro</th><th>Ações</th></tr></thead>
      <tbody id="tbodyOS"><tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin"></i> Carregando...</td></tr></tbody>
    </table>
  </div>
</div>

<!-- ═══ MODAL NOVA/EDITAR OS ═══════════════════════════════ -->
<div class="modal-overlay" id="modalOS" style="display:none">
<div class="modal-box" style="max-width:860px">
  <div class="modal-header">
    <div class="modal-title"><i class="fa-solid fa-screwdriver-wrench" style="color:var(--ts-gold)"></i> <span id="modalOSTitle">Nova Ordem de Serviço</span></div>
    <button class="modal-close" onclick="closeModal('modalOS')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body" style="max-height:75vh">
    <input type="hidden" id="osId">
    <div id="osTabs">
      <div class="tabs">
        <button class="tab-btn active" data-tab="tabOSEmpresa"><i class="fa-solid fa-building"></i> Empresa</button>
        <button class="tab-btn" data-tab="tabOSServico"><i class="fa-solid fa-list-check"></i> Serviço</button>
        <button class="tab-btn" data-tab="tabOSCustos"><i class="fa-solid fa-coins"></i> Custos</button>
        <button class="tab-btn" data-tab="tabOSFaturamento"><i class="fa-solid fa-file-invoice-dollar"></i> Faturamento</button>
      </div>

      <!-- Empresa -->
      <div class="tab-pane active" id="tabOSEmpresa">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Empresa *</label>
            <select class="form-control" id="osEmpresaId" onchange="preencherEmpresa()">
              <option value="">Selecione...</option>
              <?php foreach($empresas as $e): ?><option value="<?= $e['id'] ?>" data-cnpj="<?= htmlspecialchars($e['cnpj']) ?>" data-contato="<?= htmlspecialchars($e['contato']) ?>" data-email="<?= htmlspecialchars($e['email']) ?>" data-tel="<?= htmlspecialchars($e['telefone']) ?>"><?= htmlspecialchars($e['nome']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label class="form-label">CNPJ</label><input type="text" class="form-control" id="osEmpresaCnpj" placeholder="00.000.000/0000-00" data-mask="cnpj"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Contato na Empresa</label><input type="text" class="form-control" id="osContato" placeholder="Nome do contato"></div>
          <div class="form-group"><label class="form-label">E-mail do Contato</label><input type="email" class="form-control" id="osEmailContato" placeholder="contato@empresa.com"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Telefone do Contato</label><input type="text" class="form-control" id="osTelContato" data-mask="phone" placeholder="(00) 00000-0000"></div>
          <div class="form-group"><label class="form-label">Proposta Vinculada</label>
            <select class="form-control" id="osPropostaId">
              <option value="">Nenhuma</option>
              <?php foreach($propostas as $p): ?><option value="<?= $p['id'] ?>" data-num="<?= htmlspecialchars($p['numero']) ?>"><?= htmlspecialchars($p['numero'].' — '.$p['titulo']) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Nº do Contrato</label><input type="text" class="form-control" id="osNumContrato" placeholder="Ex: CONT-2025-001"></div>
        </div>
      </div>

      <!-- Serviço -->
      <div class="tab-pane" id="tabOSServico">
        <div class="form-group"><label class="form-label">Título da OS *</label><input type="text" class="form-control" id="osTitulo" placeholder="Ex: Monitoramento de Fauna — 1ª Campanha"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Tipo de Serviço</label>
            <select class="form-control" id="osTipoServico">
              <option value="">Selecione...</option>
              <option>Acompanhamento de Supressão Vegetal</option>
              <option>Monitoramento de Fauna Terrestre</option>
              <option>Monitoramento de Avifauna</option>
              <option>Levantamento Florístico</option>
              <option>EIA/RIMA</option>
              <option>Licenciamento Ambiental</option>
              <option>Consultoria Ambiental</option>
              <option>Outros</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Responsável Técnico</label>
            <select class="form-control" id="osResponsavel">
              <option value="">Selecione...</option>
              <?php foreach($responsaveis as $r): ?><option value="<?= $r['id'] ?>" <?= $r['id']==$user_id?'selected':'' ?>><?= htmlspecialchars($r['nome']) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Descrição do Serviço</label><textarea class="form-control" id="osDescricao" rows="3" placeholder="Descreva o escopo do serviço..."></textarea></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Prioridade</label>
            <select class="form-control" id="osPrioridade">
              <option value="baixa">🟢 Baixa</option>
              <option value="media" selected>🟡 Média</option>
              <option value="alta">🔴 Alta</option>
              <option value="urgente">🚨 Urgente</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Status</label>
            <select class="form-control" id="osStatus">
              <option value="aberta">Aberta</option>
              <option value="em_andamento">Em Andamento</option>
              <option value="aguardando">Aguardando</option>
              <option value="concluida">Concluída</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>
        </div>
        <div class="form-row-3">
          <div class="form-group"><label class="form-label">Data Abertura</label><input type="date" class="form-control" id="osDataAbertura"></div>
          <div class="form-group"><label class="form-label">Prazo</label><input type="date" class="form-control" id="osDataPrazo"></div>
          <div class="form-group"><label class="form-label">Conclusão</label><input type="date" class="form-control" id="osDataConclusao"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Horas Estimadas</label><input type="number" class="form-control" id="osHorasEst" step="0.5" placeholder="0"></div>
          <div class="form-group"><label class="form-label">Horas Realizadas</label><input type="number" class="form-control" id="osHorasReal" step="0.5" placeholder="0"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Local de Execução</label>
            <select class="form-control" id="osLocal"><option value="remoto">Remoto</option><option value="presencial">Presencial</option><option value="misto">Misto</option></select>
          </div>
          <div class="form-group"><label class="form-label">Endereço / Localidade</label><input type="text" class="form-control" id="osEndereco" placeholder="Cidade/UF ou endereço completo"></div>
        </div>
        <div class="form-group"><label class="form-label">Observações</label><textarea class="form-control" id="osObs" rows="2" placeholder="Notas internas..."></textarea></div>
      </div>

      <!-- Custos -->
      <div class="tab-pane" id="tabOSCustos">
        <div class="form-section"><div class="form-section-title"><i class="fa-solid fa-hammer"></i> Custos Diretos</div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Mão de Obra (R$)</label><input type="number" class="form-control custo-input" id="osMaoObra" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Materiais (R$)</label><input type="number" class="form-control custo-input" id="osMateriais" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Deslocamento (R$)</label><input type="number" class="form-control custo-input" id="osDeslocamento" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Equipamentos (R$)</label><input type="number" class="form-control custo-input" id="osEquipamentos" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Terceiros (R$)</label><input type="number" class="form-control custo-input" id="osTerceiros" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Software/Licenças (R$)</label><input type="number" class="form-control custo-input" id="osSoftware" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Alimentação (R$)</label><input type="number" class="form-control custo-input" id="osAlimentacao" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Hospedagem (R$)</label><input type="number" class="form-control custo-input" id="osHospedagem" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Outros (R$)</label><input type="number" class="form-control custo-input" id="osOutros" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Margem de Lucro (%)</label><input type="number" class="form-control custo-input" id="osMargemLucro" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
          </div>
        </div>
        <div class="form-section"><div class="form-section-title"><i class="fa-solid fa-tag"></i> Descontos</div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Desconto % </label><input type="number" class="form-control custo-input" id="osDescontoPct" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Desconto Fixo (R$)</label><input type="number" class="form-control custo-input" id="osDescontoFixo" step="0.01" placeholder="0,00" oninput="calcularTotal()"></div>
          </div>
          <div class="form-group"><label class="form-label">Motivo do Desconto</label><input type="text" class="form-control" id="osMotivoDesconto" placeholder="Ex: Cliente antigo, volume..."></div>
        </div>
        <div class="form-section"><div class="form-section-title"><i class="fa-solid fa-percent"></i> Impostos</div>
          <div class="form-row-3">
            <div class="form-group"><label class="form-label">ISS (%)</label><input type="number" class="form-control custo-input" id="osISS" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">PIS/COFINS (%)</label><input type="number" class="form-control custo-input" id="osPisCofins" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">CSLL (%)</label><input type="number" class="form-control custo-input" id="osCSLL" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">IRPJ (%)</label><input type="number" class="form-control custo-input" id="osIRPJ" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
            <div class="form-group"><label class="form-label">Outros Impostos (%)</label><input type="number" class="form-control custo-input" id="osOutrosImp" step="0.1" placeholder="0" oninput="calcularTotal()"></div>
          </div>
        </div>
        <!-- Resumo de custos -->
        <div style="background:var(--ts-blue-deep);color:#fff;border-radius:var(--radius);padding:20px">
          <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px;color:rgba(255,255,255,.6)">Resumo Financeiro</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
            <div style="color:rgba(255,255,255,.5)">Subtotal Custos</div><div id="resSub" style="text-align:right;font-family:'DM Mono',monospace">R$ 0,00</div>
            <div style="color:rgba(255,255,255,.5)">+ Margem de Lucro</div><div id="resMargem" style="text-align:right;font-family:'DM Mono',monospace">R$ 0,00</div>
            <div style="color:rgba(255,255,255,.5)">– Descontos</div><div id="resDesc" style="text-align:right;font-family:'DM Mono',monospace;color:#fca5a5">R$ 0,00</div>
            <div style="color:rgba(255,255,255,.5)">+ Impostos</div><div id="resImp" style="text-align:right;font-family:'DM Mono',monospace;color:#fcd34d">R$ 0,00</div>
          </div>
          <div style="border-top:1px solid rgba(255,255,255,.15);margin-top:14px;padding-top:14px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-family:'Syne',sans-serif;font-size:16px;font-weight:800">TOTAL</span>
            <span id="resTotal" style="font-family:'DM Mono',monospace;font-size:22px;font-weight:700;color:var(--ts-gold)">R$ 0,00</span>
          </div>
        </div>
      </div>

      <!-- Faturamento -->
      <div class="tab-pane" id="tabOSFaturamento">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Forma de Cobrança</label>
            <select class="form-control" id="osFormaCobranca">
              <option value="unico">Pagamento Único</option>
              <option value="mensal">Mensal</option>
              <option value="etapas">Por Etapas</option>
              <option value="hora">Por Hora</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Data de Vencimento</label><input type="date" class="form-control" id="osDataVenc"></div>
        </div>
        <div class="form-group"><label class="form-label">Condições de Pagamento</label><textarea class="form-control" id="osCondicoes" rows="3" placeholder="Ex: 50% na assinatura + 50% na entrega do relatório..."></textarea></div>
        <div class="form-group"><label class="form-label">Garantia / Prazo de Entrega</label><input type="text" class="form-control" id="osGarantia" placeholder="Ex: Relatório entregue em 10 dias após a campanha"></div>
        <div style="background:var(--blue-soft);border:1px solid var(--ts-blue);border-radius:var(--radius-sm);padding:16px;margin-top:8px">
          <div style="font-weight:700;color:var(--ts-blue);margin-bottom:6px"><i class="fa-solid fa-link"></i> Integração com Financeiro</div>
          <div style="font-size:13px;color:var(--ts-blue)">Após salvar a OS, você pode gerar automaticamente um lançamento de receita no módulo Financeiro com o valor calculado acima.</div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalOS')">Cancelar</button>
    <button class="btn btn-primary" onclick="salvarOS()"><i class="fa-solid fa-save"></i> Salvar OS</button>
  </div>
</div>
</div>

<!-- ═══ MODAL VER OS ════════════════════════════════════════ -->
<div class="modal-overlay" id="modalViewOS" style="display:none">
<div class="modal-box" style="max-width:760px">
  <div class="modal-header">
    <div class="modal-title" id="viewOSTitle"><i class="fa-solid fa-screwdriver-wrench" style="color:var(--ts-gold)"></i> OS</div>
    <div style="display:flex;gap:8px;align-items:center">
      <button class="btn btn-secondary btn-sm" id="btnEditOS" onclick="editarOS()"><i class="fa-solid fa-pen"></i> Editar</button>
      <button class="btn btn-success btn-sm" id="btnGerarFin"><i class="fa-solid fa-coins"></i> Gerar Financeiro</button>
      <button class="modal-close" onclick="closeModal('modalViewOS')"><i class="fa-solid fa-xmark"></i></button>
    </div>
  </div>
  <div class="modal-body" id="viewOSBody">Carregando...</div>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
let currentOSId = null;
const statusLabels = {aberta:'Aberta',em_andamento:'Em Andamento',aguardando:'Aguardando',concluida:'Concluída',cancelada:'Cancelada'};
const priLabels    = {baixa:'🟢 Baixa',media:'🟡 Média',alta:'🔴 Alta',urgente:'🚨 Urgente'};

// Inicializar tabs do modal
document.querySelectorAll('#osTabs .tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('#osTabs .tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('#osTabs .tab-pane').forEach(p => p.classList.remove('active'));
    this.classList.add('active');
    document.getElementById(this.dataset.tab).classList.add('active');
  });
});

// Carregar lista de OS
async function loadOS() {
  const res = await fetch(BASE_URL + '/api/ordens_servico.php');
  const lista = await res.json();
  const filterSt = document.getElementById('filterStatus').value;
  const q = document.getElementById('osSearch').value.toLowerCase();
  let abertas=0,andamento=0,aguardando=0,concluidas=0,receita=0;
  lista.forEach(o => {
    if(o.status==='aberta') abertas++;
    if(o.status==='em_andamento') andamento++;
    if(o.status==='aguardando') aguardando++;
    if(o.status==='concluida') concluidas++;
    if(o.financeiro_gerado) receita++;
  });
  document.getElementById('kpiAbertas').textContent   = abertas;
  document.getElementById('kpiAndamento').textContent = andamento;
  document.getElementById('kpiAguardando').textContent= aguardando;
  document.getElementById('kpiConcluidas').textContent= concluidas;
  document.getElementById('kpiReceita').textContent   = receita + ' OS';

  const filtered = lista.filter(o =>
    (!filterSt || o.status===filterSt) &&
    (!q || (o.titulo+o.numero+o.empresa_nome_ref).toLowerCase().includes(q))
  );
  const tbody = document.getElementById('tbodyOS');
  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-inbox fa-2x" style="display:block;margin-bottom:12px;opacity:.3"></i>Nenhuma OS encontrada</td></tr>';
    return;
  }
  tbody.innerHTML = filtered.map(o => `
    <tr>
      <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--ts-blue);font-weight:700;cursor:pointer" onclick="viewOS(${o.id})">${o.numero}</span></td>
      <td><span style="cursor:pointer;font-weight:600" onclick="viewOS(${o.id})">${o.titulo.length>45?o.titulo.slice(0,45)+'…':o.titulo}</span></td>
      <td>${o.empresa_nome_ref||'–'}</td>
      <td>${o.responsavel_nome||'–'}</td>
      <td><span class="badge badge-${o.prioridade==='urgente'?'red':o.prioridade==='alta'?'orange':o.prioridade==='media'?'yellow':'green'}">${priLabels[o.prioridade]||o.prioridade}</span></td>
      <td><span class="badge os-status-${o.status}">${statusLabels[o.status]||o.status}</span></td>
      <td>${o.data_prazo?fmtData(o.data_prazo):'–'}</td>
      <td>${o.financeiro_gerado ? '<span class="badge badge-green"><i class="fa-solid fa-check"></i> Gerado</span>' : '<span class="badge badge-gray">Pendente</span>'}</td>
      <td><div style="display:flex;gap:5px">
        <button class="btn btn-ghost btn-sm btn-icon" onclick="viewOS(${o.id})" title="Ver"><i class="fa-solid fa-eye"></i></button>
        <button class="btn btn-ghost btn-sm btn-icon" onclick="editarOSById(${o.id})" title="Editar"><i class="fa-solid fa-pen"></i></button>
        <button class="btn btn-danger btn-sm btn-icon" onclick="deletarOS(${o.id})" title="Excluir"><i class="fa-solid fa-trash"></i></button>
      </div></td>
    </tr>`).join('');
}

function filterOS() { loadOS(); }
document.getElementById('osSearch').addEventListener('input', () => loadOS());

// Preencher campos ao selecionar empresa
function preencherEmpresa() {
  const opt = document.getElementById('osEmpresaId').selectedOptions[0];
  document.getElementById('osEmpresaCnpj').value   = opt.dataset.cnpj  || '';
  document.getElementById('osContato').value        = opt.dataset.contato|| '';
  document.getElementById('osEmailContato').value  = opt.dataset.email  || '';
  document.getElementById('osTelContato').value    = opt.dataset.tel    || '';
}

// Calcular total
function calcularTotal() {
  const g = id => parseFloat(document.getElementById(id).value)||0;
  const sub = g('osMaoObra')+g('osMateriais')+g('osDeslocamento')+g('osEquipamentos')+
              g('osTerceiros')+g('osSoftware')+g('osAlimentacao')+g('osHospedagem')+g('osOutros');
  const margem   = sub * (g('osMargemLucro')/100);
  const comMarg  = sub + margem;
  const descPct  = comMarg * (g('osDescontoPct')/100);
  const descFix  = g('osDescontoFixo');
  const semDesc  = comMarg - descPct - descFix;
  const impPct   = g('osISS')+g('osPisCofins')+g('osCSLL')+g('osIRPJ')+g('osOutrosImp');
  const impostos = semDesc * (impPct/100);
  const total    = Math.max(0, semDesc + impostos);
  const fmt = v => 'R$ ' + v.toLocaleString('pt-BR',{minimumFractionDigits:2});
  document.getElementById('resSub').textContent    = fmt(sub);
  document.getElementById('resMargem').textContent = fmt(margem);
  document.getElementById('resDesc').textContent   = fmt(descPct+descFix);
  document.getElementById('resImp').textContent    = fmt(impostos);
  document.getElementById('resTotal').textContent  = fmt(total);
}

// Abrir modal limpo para nova OS
document.querySelector('[onclick="openModal(\'modalOS\')"]').addEventListener('click', function(){
  document.getElementById('osId').value = '';
  document.getElementById('modalOSTitle').textContent = 'Nova Ordem de Serviço';
  // Reset todos os campos
  ['osEmpresaId','osTipoServico','osResponsavel','osPrioridade','osStatus','osLocal','osFormaCobranca']
    .forEach(id => document.getElementById(id).selectedIndex=0);
  ['osTitulo','osDescricao','osObs','osContato','osEmailContato','osTelContato','osEmpresaCnpj',
   'osNumContrato','osEndereco','osGarantia','osCondicoes','osMotivoDesconto',
   'osDataAbertura','osDataPrazo','osDataConclusao','osDataVenc']
    .forEach(id => document.getElementById(id).value='');
  document.getElementById('osDataAbertura').value = new Date().toISOString().slice(0,10);
  ['osMaoObra','osMateriais','osDeslocamento','osEquipamentos','osTerceiros','osSoftware',
   'osAlimentacao','osHospedagem','osOutros','osMargemLucro','osDescontoPct','osDescontoFixo',
   'osISS','osPisCofins','osCSLL','osIRPJ','osOutrosImp','osHorasEst','osHorasReal']
    .forEach(id => document.getElementById(id).value='');
  calcularTotal();
  // Resetar tabs
  document.querySelectorAll('#osTabs .tab-btn').forEach((b,i) => b.classList.toggle('active',i===0));
  document.querySelectorAll('#osTabs .tab-pane').forEach((p,i) => p.classList.toggle('active',i===0));
});

// Salvar OS
async function salvarOS() {
  const id = parseInt(document.getElementById('osId').value)||0;
  const propOpt = document.getElementById('osPropostaId').selectedOptions[0];
  const payload = {
    action: id ? 'update' : 'create', id,
    empresa_id:       document.getElementById('osEmpresaId').value,
    empresa_cnpj:     document.getElementById('osEmpresaCnpj').value,
    contato_empresa:  document.getElementById('osContato').value,
    email_contato:    document.getElementById('osEmailContato').value,
    telefone_contato: document.getElementById('osTelContato').value,
    proposta_id:      document.getElementById('osPropostaId').value||null,
    proposta_ref:     propOpt.dataset.num||'',
    num_contrato:     document.getElementById('osNumContrato').value,
    titulo:           document.getElementById('osTitulo').value,
    tipo_servico:     document.getElementById('osTipoServico').value,
    descricao:        document.getElementById('osDescricao').value,
    responsavel_id:   document.getElementById('osResponsavel').value,
    prioridade:       document.getElementById('osPrioridade').value,
    status:           document.getElementById('osStatus').value,
    data_abertura:    document.getElementById('osDataAbertura').value,
    data_prazo:       document.getElementById('osDataPrazo').value,
    data_conclusao:   document.getElementById('osDataConclusao').value,
    horas_estimadas:  document.getElementById('osHorasEst').value,
    horas_realizadas: document.getElementById('osHorasReal').value,
    local_execucao:   document.getElementById('osLocal').value,
    endereco:         document.getElementById('osEndereco').value,
    observacoes:      document.getElementById('osObs').value,
    custo_mao_obra:      document.getElementById('osMaoObra').value||0,
    custo_materiais:     document.getElementById('osMateriais').value||0,
    custo_deslocamento:  document.getElementById('osDeslocamento').value||0,
    custo_equipamentos:  document.getElementById('osEquipamentos').value||0,
    custo_terceiros:     document.getElementById('osTerceiros').value||0,
    custo_software:      document.getElementById('osSoftware').value||0,
    custo_alimentacao:   document.getElementById('osAlimentacao').value||0,
    custo_hospedagem:    document.getElementById('osHospedagem').value||0,
    custo_outros:        document.getElementById('osOutros').value||0,
    margem_lucro_pct:    document.getElementById('osMargemLucro').value||0,
    desconto_pct:        document.getElementById('osDescontoPct').value||0,
    desconto_fixo:       document.getElementById('osDescontoFixo').value||0,
    motivo_desconto:  document.getElementById('osMotivoDesconto').value,
    iss_pct:          document.getElementById('osISS').value||0,
    pis_cofins_pct:   document.getElementById('osPisCofins').value||0,
    csll_pct:         document.getElementById('osCSLL').value||0,
    irpj_pct:         document.getElementById('osIRPJ').value||0,
    outros_impostos_pct: document.getElementById('osOutrosImp').value||0,
    forma_cobranca:   document.getElementById('osFormaCobranca').value,
    data_vencimento:  document.getElementById('osDataVenc').value,
    condicoes_pagamento: document.getElementById('osCondicoes').value,
    garantia:         document.getElementById('osGarantia').value,
  };
  if (!payload.titulo) { toast('Informe o título da OS','error'); return; }
  if (!payload.empresa_id) { toast('Selecione a empresa','error'); return; }
  const res = await api(BASE_URL + '/api/ordens_servico.php', payload);
  if (res.success) {
    toast(id ? 'OS atualizada!' : `OS ${res.numero||''} criada com sucesso!`);
    closeModal('modalOS');
    loadOS();
  } else toast(res.error||'Erro ao salvar','error');
}

// Ver OS
async function viewOS(id) {
  currentOSId = id;
  openModal('modalViewOS');
  document.getElementById('viewOSBody').innerHTML = '<div style="text-align:center;padding:40px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>';
  const o = await fetch(BASE_URL + '/api/ordens_servico.php?id='+id).then(r=>r.json());
  document.getElementById('viewOSTitle').innerHTML = `<i class="fa-solid fa-screwdriver-wrench" style="color:var(--ts-gold)"></i> ${o.numero} — ${o.titulo}`;
  document.getElementById('btnGerarFin').style.display = o.financeiro_gerado ? 'none' : '';
  document.getElementById('btnGerarFin').onclick = () => gerarFinanceiro(id);

  const fmt = v => 'R$ ' + parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2});
  const fmtD = d => d ? d.split('-').reverse().join('/') : '–';

  const custos = ['custo_mao_obra','custo_materiais','custo_deslocamento','custo_equipamentos','custo_terceiros','custo_software','custo_alimentacao','custo_hospedagem','custo_outros'];
  const custoNomes = ['Mão de Obra','Materiais','Deslocamento','Equipamentos','Terceiros','Software','Alimentação','Hospedagem','Outros'];
  const custosHtml = custos.map((c,i)=>o[c]>0?`<div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px"><span style="color:var(--text2)">${custoNomes[i]}</span><span>${fmt(o[c])}</span></div>`:'').join('');

  const lançamentos = (o.lancamentos||[]).map(l=>`
    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--surface);border-radius:8px;margin-bottom:6px;font-size:13px">
      <span>${l.descricao}</span>
      <div style="display:flex;gap:10px;align-items:center">
        <span style="font-family:'DM Mono',monospace;color:var(--green);font-weight:700">${fmt(l.valor)}</span>
        <span class="badge badge-${l.status==='pago'?'green':l.status==='atrasado'?'red':'yellow'}">${l.status}</span>
      </div>
    </div>`).join('');

  document.getElementById('viewOSBody').innerHTML = `
    <div style="padding:0">
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px">
        <div class="kpi-card" style="--kpi-color:var(--ts-blue);--kpi-soft:var(--blue-soft);padding:14px"><div class="kpi-icon-wrap" style="width:36px;height:36px;font-size:14px"><i class="fa-solid fa-building"></i></div><div><div class="kpi-label">Empresa</div><div style="font-size:13px;font-weight:600">${o.empresa_nome_ref}</div></div></div>
        <div class="kpi-card" style="--kpi-color:var(--purple);--kpi-soft:var(--purple-soft);padding:14px"><div class="kpi-icon-wrap" style="width:36px;height:36px;font-size:14px"><i class="fa-solid fa-user"></i></div><div><div class="kpi-label">Responsável</div><div style="font-size:13px;font-weight:600">${o.responsavel_nome}</div></div></div>
        <div class="kpi-card" style="--kpi-color:var(--orange);--kpi-soft:var(--orange-soft);padding:14px"><div class="kpi-icon-wrap" style="width:36px;height:36px;font-size:14px"><i class="fa-solid fa-calendar"></i></div><div><div class="kpi-label">Prazo</div><div style="font-size:13px;font-weight:600">${fmtD(o.data_prazo)}</div></div></div>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
        <span class="badge os-status-${o.status}">${statusLabels[o.status]||o.status}</span>
        <span class="badge badge-${o.prioridade==='urgente'?'red':o.prioridade==='alta'?'orange':o.prioridade==='media'?'yellow':'green'}">${priLabels[o.prioridade]}</span>
        ${o.tipo_servico?`<span class="badge badge-blue">${o.tipo_servico}</span>`:''}
        ${o.local_execucao?`<span class="badge badge-gray">${o.local_execucao}</span>`:''}
      </div>
      ${o.descricao?`<div style="padding:12px 14px;background:var(--surface);border-radius:8px;font-size:13px;margin-bottom:16px;line-height:1.6">${o.descricao}</div>`:''}
      ${custosHtml?`<div style="margin-bottom:16px"><div class="form-label" style="margin-bottom:8px">Detalhamento de Custos</div>${custosHtml}<div style="display:flex;justify-content:space-between;padding:8px 0;border-top:2px solid var(--border);margin-top:6px;font-weight:700"><span>Total</span><span style="font-family:'DM Mono',monospace;color:var(--ts-gold)">${fmt(o.valor_total)}</span></div></div>`:''}
      ${o.condicoes_pagamento?`<div style="padding:12px 14px;background:var(--blue-soft);border-radius:8px;font-size:13px;margin-bottom:16px"><strong>Condições:</strong> ${o.condicoes_pagamento}</div>`:''}
      <div style="margin-top:16px"><div class="form-label" style="margin-bottom:8px"><i class="fa-solid fa-coins"></i> Lançamentos Financeiros</div>${lançamentos||'<div style="color:var(--text3);font-size:13px;padding:8px">Nenhum lançamento gerado ainda.</div>'}</div>
    </div>`;
}

// Editar OS (abre modal com dados preenchidos)
async function editarOSById(id) {
  currentOSId = id;
  const o = await fetch(BASE_URL + '/api/ordens_servico.php?id='+id).then(r=>r.json());
  document.getElementById('osId').value = id;
  document.getElementById('modalOSTitle').textContent = 'Editar OS: ' + o.numero;
  document.getElementById('osEmpresaId').value    = o.empresa_id||'';
  document.getElementById('osEmpresaCnpj').value  = o.empresa_cnpj||'';
  document.getElementById('osContato').value       = o.contato_empresa||'';
  document.getElementById('osEmailContato').value = o.email_contato||'';
  document.getElementById('osTelContato').value   = o.telefone_contato||'';
  document.getElementById('osPropostaId').value   = o.proposta_id||'';
  document.getElementById('osNumContrato').value  = o.num_contrato||'';
  document.getElementById('osTitulo').value       = o.titulo||'';
  document.getElementById('osTipoServico').value  = o.tipo_servico||'';
  document.getElementById('osDescricao').value    = o.descricao||'';
  document.getElementById('osResponsavel').value  = o.responsavel_id||'';
  document.getElementById('osPrioridade').value   = o.prioridade||'media';
  document.getElementById('osStatus').value       = o.status||'aberta';
  document.getElementById('osDataAbertura').value = o.data_abertura||'';
  document.getElementById('osDataPrazo').value    = o.data_prazo||'';
  document.getElementById('osDataConclusao').value= o.data_conclusao||'';
  document.getElementById('osHorasEst').value     = o.horas_estimadas||'';
  document.getElementById('osHorasReal').value    = o.horas_realizadas||'';
  document.getElementById('osLocal').value        = o.local_execucao||'remoto';
  document.getElementById('osEndereco').value     = o.endereco||'';
  document.getElementById('osObs').value          = o.observacoes||'';
  document.getElementById('osMaoObra').value      = o.custo_mao_obra||'';
  document.getElementById('osMateriais').value    = o.custo_materiais||'';
  document.getElementById('osDeslocamento').value = o.custo_deslocamento||'';
  document.getElementById('osEquipamentos').value = o.custo_equipamentos||'';
  document.getElementById('osTerceiros').value    = o.custo_terceiros||'';
  document.getElementById('osSoftware').value     = o.custo_software||'';
  document.getElementById('osAlimentacao').value  = o.custo_alimentacao||'';
  document.getElementById('osHospedagem').value   = o.custo_hospedagem||'';
  document.getElementById('osOutros').value       = o.custo_outros||'';
  document.getElementById('osMargemLucro').value  = o.margem_lucro_pct||'';
  document.getElementById('osDescontoPct').value  = o.desconto_pct||'';
  document.getElementById('osDescontoFixo').value = o.desconto_fixo||'';
  document.getElementById('osMotivoDesconto').value=o.motivo_desconto||'';
  document.getElementById('osISS').value          = o.iss_pct||'';
  document.getElementById('osPisCofins').value    = o.pis_cofins_pct||'';
  document.getElementById('osCSLL').value         = o.csll_pct||'';
  document.getElementById('osIRPJ').value         = o.irpj_pct||'';
  document.getElementById('osOutrosImp').value    = o.outros_impostos_pct||'';
  document.getElementById('osFormaCobranca').value= o.forma_cobranca||'unico';
  document.getElementById('osDataVenc').value     = o.data_vencimento||'';
  document.getElementById('osCondicoes').value    = o.condicoes_pagamento||'';
  document.getElementById('osGarantia').value     = o.garantia||'';
  calcularTotal();
  openModal('modalOS');
}
function editarOS() { closeModal('modalViewOS'); editarOSById(currentOSId); }

async function gerarFinanceiro(id) {
  if (!confirm('Gerar lançamento de receita no Financeiro para esta OS?')) return;
  const res = await api(BASE_URL + '/api/ordens_servico.php', {action:'gerar_financeiro',id});
  if (res.success) {
    toast(`Lançamento de ${fmtMoeda(res.valor)} gerado no Financeiro!`);
    viewOS(id); loadOS();
  } else toast(res.error||'Erro','error');
}

async function deletarOS(id) {
  if (!confirm('Excluir esta OS? Esta ação não pode ser desfeita.')) return;
  const res = await api(BASE_URL + '/api/ordens_servico.php', {action:'delete',id});
  if (res.success) { toast('OS excluída'); loadOS(); }
  else toast(res.error||'Erro','error');
}

loadOS();
</script>
<?php include 'includes/footer.php'; ?>
