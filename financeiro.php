<?php
require_once 'includes/auth_check.php';
$empresas = db_all('empresas','nome ASC');
include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left"><h1>Financeiro</h1><p>Controle de receitas e despesas</p></div>
  <div style="display:flex;gap:10px;align-items:center">
    <!-- Filtro de mês/ano -->
    <input type="month" class="filter-select" id="filtroMes" style="padding:6px 12px;font-size:13px" onchange="loadFin()">
    <button class="btn btn-success" onclick="abrirLancamento('receita')"><i class="fa-solid fa-plus"></i> Nova Receita</button>
    <button class="btn btn-danger"  onclick="abrirLancamento('despesa')"><i class="fa-solid fa-minus"></i> Nova Despesa</button>
    <button class="btn btn-primary" onclick="abrirImportarItau()" style="background:var(--ts-blue);border-color:var(--ts-blue)"><i class="fa-solid fa-file-import"></i> Importar Itaú CSV</button>
  </div>
</div>

<!-- KPIs -->
<div class="kpi-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px" id="finKpis">
  <div class="kpi-card" style="--kpi-color:var(--green);--kpi-soft:var(--green-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-arrow-trend-up"></i></div>
    <div><div class="kpi-label">Receitas (Mês)</div><div class="kpi-value" id="kpiRec" style="font-size:16px">–</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--red);--kpi-soft:var(--red-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-arrow-trend-down"></i></div>
    <div><div class="kpi-label">Despesas (Mês)</div><div class="kpi-value" id="kpiDesp" style="font-size:16px">–</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--ts-blue);--kpi-soft:var(--blue-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-scale-balanced"></i></div>
    <div><div class="kpi-label">Saldo (Mês)</div><div class="kpi-value" id="kpiSaldo" style="font-size:16px">–</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--yellow);--kpi-soft:var(--yellow-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-clock"></i></div>
    <div><div class="kpi-label">A Receber</div><div class="kpi-value" id="kpiPend" style="font-size:16px">–</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--red);--kpi-soft:var(--red-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div><div class="kpi-label">Atrasados</div><div class="kpi-value" id="kpiAtras" style="font-size:16px">–</div></div>
  </div>
</div>

<!-- Gráfico resumo mensal -->
<div class="card" style="margin-bottom:20px">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-chart-bar"></i> Resumo do Mês — <span id="labelMesResumo"></span></div>
  </div>
  <div style="padding:16px 24px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
    <div style="background:var(--green-soft,#f0fdf4);border-radius:10px;padding:16px;border-left:4px solid var(--green,#38a169)">
      <div style="font-size:12px;color:var(--text2);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Total Recebido</div>
      <div id="resumoRecebido" style="font-size:22px;font-weight:800;color:var(--green,#38a169)">–</div>
    </div>
    <div style="background:var(--red-soft,#fff5f5);border-radius:10px;padding:16px;border-left:4px solid var(--red,#e53e3e)">
      <div style="font-size:12px;color:var(--text2);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Total Gasto</div>
      <div id="resumoGasto" style="font-size:22px;font-weight:800;color:var(--red,#e53e3e)">–</div>
    </div>
    <div id="resumoSaldoCard" style="background:var(--blue-soft,#ebf8ff);border-radius:10px;padding:16px;border-left:4px solid var(--ts-blue)">
      <div style="font-size:12px;color:var(--text2);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Saldo Líquido</div>
      <div id="resumoSaldo" style="font-size:22px;font-weight:800;color:var(--ts-blue)">–</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-list"></i> Lançamentos</div>
    <div class="filters-bar" style="margin:0">
      <div class="filter-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="finSearch" placeholder="Buscar..."></div>
      <select class="filter-select" id="filterTipo" onchange="loadFin()">
        <option value="">Todos</option>
        <option value="receita">Receitas</option>
        <option value="despesa">Despesas</option>
      </select>
      <select class="filter-select" id="filterStFin" onchange="loadFin()">
        <option value="">Todos status</option>
        <option value="pendente">Pendente</option>
        <option value="pago">Pago</option>
        <option value="atrasado">Atrasado</option>
        <option value="cancelado">Cancelado</option>
      </select>
    </div>
  </div>
  <div class="table-wrapper">
    <table class="data-table" id="tableFinanceiro">
      <thead>
        <tr>
          <th>Descrição</th><th>Empresa</th><th>Tipo</th><th>Categoria</th>
          <th>Valor</th><th>Vencimento</th><th>Pagamento</th><th>Status</th><th>Ações</th>
        </tr>
      </thead>
      <tbody id="tbodyFin">
        <tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-spinner fa-spin"></i></td></tr>
      </tbody>
    </table>
  </div>
  <!-- Totalizador da lista filtrada -->
  <div id="finListaTotais" style="padding:10px 18px 14px;display:flex;gap:24px;font-size:13px;border-top:1px solid var(--border);flex-wrap:wrap;background:var(--surface)"></div>
</div>

<!-- MODAL LANÇAMENTO -->
<div class="modal-overlay" id="modalLancamento" style="display:none">
<div class="modal-box" style="max-width:560px">
  <div class="modal-header">
    <div class="modal-title" id="lancTitulo"><i class="fa-solid fa-coins" style="color:var(--ts-gold)"></i> Novo Lançamento</div>
    <button class="modal-close" onclick="closeModal('modalLancamento')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body">
    <input type="hidden" id="lancId">
    <input type="hidden" id="lancTipo">
    <div class="form-group"><label class="form-label">Descrição *</label><input type="text" class="form-control" id="lancDesc" placeholder="Descreva o lançamento..."></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Empresa</label>
        <select class="form-control" id="lancEmpresa">
          <option value="">Selecione...</option>
          <?php foreach($empresas as $e): ?><option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Categoria</label>
        <select class="form-control" id="lancCategoria">
          <option value="">Selecione...</option>
          <option>Serviços / OS</option><option>Projetos</option><option>Consultoria</option>
          <option>Mão de Obra</option><option>Materiais</option><option>Deslocamento</option>
          <option>Equipamentos</option><option>Software</option><option>Alimentação</option>
          <option>Hospedagem</option><option>Impostos</option><option>Administrativo</option><option>Outros</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Valor (R$) *</label><input type="number" class="form-control" id="lancValor" step="0.01" placeholder="0,00"></div>
      <div class="form-group"><label class="form-label">Data de Vencimento</label><input type="date" class="form-control" id="lancVenc"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Forma de Pagamento</label>
        <select class="form-control" id="lancForma">
          <option value="pix">PIX</option><option value="boleto">Boleto</option>
          <option value="ted">TED/DOC</option><option value="cartao">Cartão</option>
          <option value="dinheiro">Dinheiro</option><option value="outro">Outro</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Status</label>
        <select class="form-control" id="lancStatus">
          <option value="pendente">Pendente</option>
          <option value="pago">Pago</option>
          <option value="cancelado">Cancelado</option>
        </select>
      </div>
    </div>
    <div class="form-group" id="lancDataPagGrp"><label class="form-label">Data de Pagamento</label><input type="date" class="form-control" id="lancDataPag"></div>
    <div class="form-group"><label class="form-label">Observação</label><textarea class="form-control" id="lancObs" rows="2" placeholder="Informações adicionais..."></textarea></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalLancamento')">Cancelar</button>
    <button class="btn btn-primary" id="btnSalvarLanc" onclick="salvarLancamento()"><i class="fa-solid fa-save"></i> Salvar</button>
  </div>
</div>
</div>

<!-- MODAL PAGAR -->
<div class="modal-overlay" id="modalPagar" style="display:none">
<div class="modal-box" style="max-width:400px">
  <div class="modal-header"><div class="modal-title"><i class="fa-solid fa-circle-check" style="color:var(--green)"></i> Registrar Pagamento</div><button class="modal-close" onclick="closeModal('modalPagar')"><i class="fa-solid fa-xmark"></i></button></div>
  <div class="modal-body">
    <input type="hidden" id="pagarId">
    <div class="form-group"><label class="form-label">Data do Pagamento</label><input type="date" class="form-control" id="pagarData"></div>
    <div class="form-group"><label class="form-label">Forma de Pagamento</label>
      <select class="form-control" id="pagarForma">
        <option value="pix">PIX</option><option value="boleto">Boleto</option>
        <option value="ted">TED/DOC</option><option value="cartao">Cartão</option>
        <option value="dinheiro">Dinheiro</option><option value="outro">Outro</option>
      </select>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalPagar')">Cancelar</button>
    <button class="btn btn-success" onclick="confirmarPagamento()"><i class="fa-solid fa-check"></i> Confirmar</button>
  </div>
</div>
</div>

<!-- ════════ MODAL IMPORTAR ITAÚ CSV ════════ -->
<div class="modal-overlay" id="modalItau" style="display:none">
<div class="modal-box" style="max-width:820px;max-height:90vh;display:flex;flex-direction:column">
  <div class="modal-header">
    <div class="modal-title"><i class="fa-solid fa-file-import" style="color:var(--ts-blue)"></i> Importar Extrato Itaú (CSV)</div>
    <button class="modal-close" onclick="closeModal('modalItau')"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="modal-body" style="overflow-y:auto;flex:1">
    <!-- STEP 1 -->
    <div id="itauStep1">
      <div style="background:var(--blue-soft,#e8f0fe);border:2px dashed var(--ts-blue);border-radius:10px;padding:36px;text-align:center;cursor:pointer;transition:.2s" id="itauDropZone"
        onclick="document.getElementById('itauFileInput').click()"
        ondragover="event.preventDefault();this.style.borderStyle='solid'"
        ondragleave="this.style.borderStyle='dashed'"
        ondrop="itauHandleDrop(event)">
        <i class="fa-solid fa-file-csv" style="font-size:44px;color:var(--ts-blue);display:block;margin-bottom:12px"></i>
        <div style="font-weight:700;font-size:15px;margin-bottom:6px">Arraste o arquivo CSV aqui ou clique para selecionar</div>
        <div style="font-size:12px;color:var(--text3)">Extrato Itaú exportado em formato CSV (separado por ponto e vírgula)</div>
        <input type="file" id="itauFileInput" accept=".csv,text/csv" style="display:none" onchange="itauCarregarCSV(this.files[0])">
      </div>
      <div id="itauLoadingMsg" style="display:none;text-align:center;padding:24px;color:var(--ts-blue)"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><br><span style="font-size:13px;margin-top:8px;display:block">Lendo arquivo...</span></div>
    </div>
    <!-- STEP 2 -->
    <div id="itauStep2" style="display:none">
      <div id="itauInfo" style="background:var(--surface);border-radius:8px;padding:12px 18px;margin-bottom:14px;font-size:13px;display:flex;gap:20px;flex-wrap:wrap;border:1px solid var(--border)"></div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap">
        <span style="font-weight:600;font-size:13px">Mostrar:</span>
        <button class="btn btn-sm" id="btnFiltSaidas"   onclick="itauFiltrar('saidas')"   style="background:var(--ts-blue);color:#fff">Saídas</button>
        <button class="btn btn-sm" id="btnFiltEntradas" onclick="itauFiltrar('entradas')" style="">Entradas</button>
        <button class="btn btn-sm" id="btnFiltTodos"    onclick="itauFiltrar('todos')"    style="">Todos</button>
        <span style="margin-left:auto;font-size:12px;color:var(--text3)"><b id="itauSelecionados">0</b> selecionados</span>
        <button class="btn btn-sm btn-ghost" onclick="itauToggleTodos(true)">Marcar todos</button>
        <button class="btn btn-sm btn-ghost" onclick="itauToggleTodos(false)">Desmarcar</button>
      </div>
      <div class="table-wrapper" style="max-height:320px;overflow-y:auto">
        <table class="data-table" id="itauTable">
          <thead><tr>
            <th style="width:36px"><input type="checkbox" id="itauCheckAll" onchange="itauToggleTodos(this.checked)"></th>
            <th>Data</th><th>Descrição</th><th>Empresa/Favorecido</th>
            <th style="text-align:right">Valor</th><th>Tipo</th>
          </tr></thead>
          <tbody id="itauTbody"></tbody>
        </table>
      </div>
      <div id="itauTotais" style="display:flex;gap:20px;margin-top:12px;font-size:13px;flex-wrap:wrap"></div>
      <div style="margin-top:10px"><button class="btn btn-ghost btn-sm" onclick="itauVoltarStep1()"><i class="fa-solid fa-arrow-left"></i> Trocar arquivo</button></div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modalItau')">Cancelar</button>
    <button class="btn btn-primary" id="btnImportarItau" onclick="itauConfirmarImport()" disabled><i class="fa-solid fa-file-import"></i> Importar Selecionados</button>
  </div>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
// ── Inicialização ────────────────────────────────────────
const hoje = new Date();
document.getElementById('filtroMes').value = hoje.toISOString().slice(0,7);

// ── loadFin ──────────────────────────────────────────────
async function loadFin() {
  const mes    = document.getElementById('filtroMes').value || hoje.toISOString().slice(0,7);
  const tipo   = document.getElementById('filterTipo').value;
  const status = document.getElementById('filterStFin').value;
  const q      = document.getElementById('finSearch').value.toLowerCase();

  // Label do mês
  const [ano, m] = mes.split('-');
  const meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
  const labelMes = `${meses[parseInt(m)-1]} ${ano}`;
  const labelEl = document.getElementById('labelMesResumo');
  if (labelEl) labelEl.textContent = labelMes;

  // KPIs — busca no servidor filtrado pelo mês
  const res = await api(BASE_URL + '/api/financeiro.php', {action:'resumo', mes});
  const fmt  = v => 'R$ ' + parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2});
  const fmtV = v => parseFloat(v||0);

  document.getElementById('kpiRec').textContent   = fmt(res.receitas);
  document.getElementById('kpiDesp').textContent  = fmt(res.despesas);
  const saldo = fmtV(res.receitas) - fmtV(res.despesas);
  document.getElementById('kpiSaldo').textContent = fmt(saldo);
  document.getElementById('kpiPend').textContent  = fmt(res.pendentes);
  document.getElementById('kpiAtras').textContent = fmt(res.atrasados);

  // KPI saldo — cor dinâmica
  const kpiSaldoCard = document.querySelector('#finKpis .kpi-card:nth-child(3)');
  if (kpiSaldoCard) {
    kpiSaldoCard.style.setProperty('--kpi-color', saldo >= 0 ? 'var(--green)' : 'var(--red)');
    kpiSaldoCard.style.setProperty('--kpi-soft',  saldo >= 0 ? 'var(--green-soft)' : 'var(--red-soft)');
  }

  // Resumo visual
  document.getElementById('resumoRecebido').textContent = fmt(res.receitas);
  document.getElementById('resumoGasto').textContent    = fmt(res.despesas);
  document.getElementById('resumoSaldo').textContent    = fmt(saldo);
  const saldoCard = document.getElementById('resumoSaldoCard');
  if (saldoCard) {
    saldoCard.style.borderLeftColor = saldo >= 0 ? 'var(--green,#38a169)' : 'var(--red,#e53e3e)';
    saldoCard.style.background = saldo >= 0 ? 'var(--green-soft,#f0fdf4)' : 'var(--red-soft,#fff5f5)';
    document.getElementById('resumoSaldo').style.color = saldo >= 0 ? 'var(--green,#38a169)' : 'var(--red,#e53e3e)';
  }

  // Lista completa — busca todos os lançamentos (filtra no cliente por mes/tipo/status/busca)
  const lista = await fetch(BASE_URL + '/api/financeiro.php?all=1').then(r=>r.json());

  // Filtra por mês + tipo + status + busca
  const filtered = lista.filter(f => {
    // Prioridade: data_pagamento (se pago), senão data_vencimento, senão created_at
    const dataMes = (f.data_pagamento || f.data_vencimento || f.created_at || '').slice(0,7);
    const noMes = !mes || dataMes === mes;
    return noMes &&
      (!tipo   || f.tipo===tipo) &&
      (!status || f.status===status) &&
      (!q || (f.descricao+' '+(f.empresa_nome||'')+' '+(f.categoria||'')).toLowerCase().includes(q));
  });

  const tbody = document.getElementById('tbodyFin');
  if (!filtered.length) {
    tbody.innerHTML='<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text3)"><i class="fa-solid fa-inbox fa-2x" style="display:block;margin-bottom:12px;opacity:.3"></i>Nenhum lançamento</td></tr>';
    document.getElementById('finListaTotais').innerHTML='';
    return;
  }

  const stBadge = {pendente:'badge-yellow',pago:'badge-green',atrasado:'badge-red',cancelado:'badge-gray'};
  const stLabel = {pendente:'Pendente',pago:'Pago',atrasado:'Atrasado',cancelado:'Cancelado'};
  const fmtD = d => d?d.slice(0,10).split('-').reverse().join('/'):'-';

  tbody.innerHTML = filtered.map(f=>`
    <tr class="fin-row-${f.tipo}">
      <td>
        <div style="font-weight:600">${f.descricao}</div>
        ${f.os_id?`<div style="font-size:11px;color:var(--text3)">OS vinculada</div>`:''}
        ${f.observacao?`<div style="font-size:11px;color:var(--text3)">${f.observacao.length>50?f.observacao.slice(0,50)+'…':f.observacao}</div>`:''}
      </td>
      <td>${f.empresa_nome||'–'}</td>
      <td><span class="badge ${f.tipo==='receita'?'badge-green':'badge-red'}">${f.tipo==='receita'?'↑ Receita':'↓ Despesa'}</span></td>
      <td><span class="badge badge-gray" style="font-size:11px">${f.categoria||'–'}</span></td>
      <td class="fin-valor-${f.tipo}" style="font-weight:700;color:${f.tipo==='receita'?'var(--green)':'var(--red)'}">R$ ${parseFloat(f.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
      <td>${fmtD(f.data_vencimento)}</td>
      <td>${fmtD(f.data_pagamento)}</td>
      <td><span class="badge ${stBadge[f.status]||'badge-gray'}">${stLabel[f.status]||f.status}</span></td>
      <td><div style="display:flex;gap:5px">
        ${f.status==='pendente'?`<button class="btn btn-success btn-sm btn-icon" onclick="abrirPagar(${f.id})" title="Registrar Pagamento"><i class="fa-solid fa-check"></i></button>`:''}
        <button class="btn btn-ghost btn-sm btn-icon" onclick="editarLancamento(${f.id})" title="Editar"><i class="fa-solid fa-pen"></i></button>
        <button class="btn btn-danger btn-sm btn-icon" onclick="deletarLanc(${f.id})" title="Excluir"><i class="fa-solid fa-trash"></i></button>
      </div></td>
    </tr>`).join('');

  // Totalizador inferior
  const totalRec  = filtered.filter(f=>f.tipo==='receita').reduce((s,f)=>s+parseFloat(f.valor),0);
  const totalDesp = filtered.filter(f=>f.tipo==='despesa').reduce((s,f)=>s+parseFloat(f.valor),0);
  const saldoList = totalRec - totalDesp;
  document.getElementById('finListaTotais').innerHTML = `
    <span><b>${filtered.length}</b> lançamentos exibidos</span>
    <span style="color:var(--green)">Receitas: <b>${fmt(totalRec)}</b></span>
    <span style="color:var(--red)">Despesas: <b>${fmt(totalDesp)}</b></span>
    <span style="color:${saldoList>=0?'var(--ts-blue)':'var(--red)'}">Saldo: <b>${fmt(saldoList)}</b></span>
  `;
}

document.getElementById('finSearch').addEventListener('input', loadFin);

// ── Modal lançamento ──────────────────────────────────────
function abrirLancamento(tipo) {
  document.getElementById('lancId').value = '';
  document.getElementById('lancTipo').value = tipo;
  document.getElementById('lancTitulo').innerHTML = `<i class="fa-solid fa-coins" style="color:var(--ts-gold)"></i> ${tipo==='receita'?'Nova Receita':'Nova Despesa'}`;
  ['lancDesc','lancValor','lancObs'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('lancVenc').value = '';
  document.getElementById('lancDataPag').value = '';
  document.getElementById('lancEmpresa').selectedIndex = 0;
  document.getElementById('lancCategoria').selectedIndex = 0;
  document.getElementById('lancForma').selectedIndex = 0;
  document.getElementById('lancStatus').value = 'pendente';
  openModal('modalLancamento');
}

async function editarLancamento(id) {
  const lista = await fetch(BASE_URL + '/api/financeiro.php').then(r=>r.json());
  const f = lista.find(l=>l.id==id);
  if (!f) return;
  document.getElementById('lancId').value    = f.id;
  document.getElementById('lancTipo').value  = f.tipo;
  document.getElementById('lancDesc').value  = f.descricao;
  document.getElementById('lancValor').value = f.valor;
  document.getElementById('lancVenc').value  = f.data_vencimento||'';
  document.getElementById('lancDataPag').value = f.data_pagamento||'';
  document.getElementById('lancObs').value   = f.observacao||'';
  document.getElementById('lancEmpresa').value   = f.empresa_id||'';
  document.getElementById('lancCategoria').value = f.categoria||'';
  document.getElementById('lancForma').value     = f.forma_pagamento||'pix';
  document.getElementById('lancStatus').value    = f.status||'pendente';
  document.getElementById('lancTitulo').innerHTML = `<i class="fa-solid fa-pen" style="color:var(--ts-gold)"></i> Editar Lançamento`;
  openModal('modalLancamento');
}

async function salvarLancamento() {
  const id    = parseInt(document.getElementById('lancId').value)||0;
  const tipo  = document.getElementById('lancTipo').value;
  const desc  = document.getElementById('lancDesc').value.trim();
  const valor = parseFloat(document.getElementById('lancValor').value)||0;
  if (!desc)    { toast('Informe a descrição','error'); return; }
  if (valor<=0) { toast('Valor deve ser maior que zero','error'); return; }
  const status = document.getElementById('lancStatus').value;
  const payload = {
    action: id?'update':'create', id, tipo, descricao:desc, valor, status,
    empresa_id:      document.getElementById('lancEmpresa').value||null,
    categoria:       document.getElementById('lancCategoria').value,
    data_vencimento: document.getElementById('lancVenc').value,
    data_pagamento:  document.getElementById('lancDataPag').value || (status==='pago'?new Date().toISOString().slice(0,10):null),
    forma_pagamento: document.getElementById('lancForma').value,
    observacao:      document.getElementById('lancObs').value,
  };
  const res = await api(BASE_URL + '/api/financeiro.php', payload);
  if (res.success) { toast('Lançamento salvo!'); closeModal('modalLancamento'); loadFin(); }
  else toast(res.error||'Erro','error');
}

function abrirPagar(id) {
  document.getElementById('pagarId').value = id;
  document.getElementById('pagarData').value = new Date().toISOString().slice(0,10);
  document.getElementById('pagarForma').selectedIndex = 0;
  openModal('modalPagar');
}

async function confirmarPagamento() {
  const id    = parseInt(document.getElementById('pagarId').value);
  const data  = document.getElementById('pagarData').value;
  const forma = document.getElementById('pagarForma').value;
  const res   = await api(BASE_URL + '/api/financeiro.php',{action:'pagar',id,data_pagamento:data,forma_pagamento:forma});
  if (res.success) { toast('Pagamento registrado!'); closeModal('modalPagar'); loadFin(); }
  else toast(res.error||'Erro','error');
}

async function deletarLanc(id) {
  if (!confirm('Excluir este lançamento?')) return;
  const res = await api(BASE_URL + '/api/financeiro.php',{action:'delete',id});
  if (res.success) { toast('Excluído'); loadFin(); }
  else toast(res.error||'Erro','error');
}

// ════════ IMPORTAR ITAÚ CSV ════════
let itauDados = [];

function abrirImportarItau() {
  itauVoltarStep1();
  openModal('modalItau');
}
function itauVoltarStep1() {
  itauDados = [];
  document.getElementById('itauStep1').style.display = '';
  document.getElementById('itauStep2').style.display = 'none';
  document.getElementById('itauLoadingMsg').style.display = 'none';
  document.getElementById('itauDropZone').style.display = '';
  document.getElementById('itauFileInput').value = '';
  document.getElementById('btnImportarItau').disabled = true;
}
function itauHandleDrop(e) {
  e.preventDefault();
  document.getElementById('itauDropZone').style.borderStyle = 'dashed';
  const file = e.dataTransfer.files[0];
  if (file) itauCarregarCSV(file);
}
async function itauCarregarCSV(file) {
  if (!file) return;
  document.getElementById('itauDropZone').style.display = 'none';
  document.getElementById('itauLoadingMsg').style.display = '';
  const fd = new FormData();
  fd.append('action','preview');
  fd.append('csv', file);
  try {
    const res = await fetch(BASE_URL + '/api/importar_itau.php', {method:'POST',body:fd});
    const data = await res.json();
    if (!data.ok) throw new Error(data.error||'Erro ao ler CSV');
    itauRenderizarPreview(data);
  } catch(err) {
    document.getElementById('itauDropZone').style.display = '';
    document.getElementById('itauLoadingMsg').style.display = 'none';
    toast('Erro: ' + err.message, 'error');
  }
}
function itauRenderizarPreview(data) {
  itauDados = data.lancamentos || [];
  // default: saídas marcadas, entradas desmarcadas
  itauDados.forEach(l => { l._sel = l.tipo === 'despesa'; });
  document.getElementById('itauStep1').style.display = 'none';
  document.getElementById('itauStep2').style.display = '';
  const fmt = v => v!=null ? 'R$ '+parseFloat(v).toLocaleString('pt-BR',{minimumFractionDigits:2}) : '–';
  document.getElementById('itauInfo').innerHTML = `
    <div><b>Titular:</b> ${data.nome||'–'}</div>
    <div><b>Conta:</b> ${data.conta||'–'}</div>
    <div><b>Período:</b> ${data.periodo||'–'}</div>
    <div><b>Saldo anterior:</b> ${fmt(data.saldo_anterior)}</div>
    <div><b>Saldo final:</b> ${fmt(data.saldo_final)}</div>
    <div><b>Transações:</b> ${data.total}</div>`;
  itauFiltrar('saidas');
}
function itauFiltrar(tipo) {
  ['btnFiltTodos','btnFiltSaidas','btnFiltEntradas'].forEach(id => {
    document.getElementById(id).style.background='';
    document.getElementById(id).style.color='';
  });
  const mapa = {todos:'btnFiltTodos',saidas:'btnFiltSaidas',entradas:'btnFiltEntradas'};
  const btn = document.getElementById(mapa[tipo]);
  if (btn) { btn.style.background='var(--ts-blue)'; btn.style.color='#fff'; }
  const filtrados = itauDados.filter(l => tipo==='todos' || (tipo==='saidas'?l.tipo==='despesa':l.tipo==='receita'));
  const tbody = document.getElementById('itauTbody');
  if (!filtrados.length) {
    tbody.innerHTML='<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text3)">Nenhuma transação nesta categoria</td></tr>';
    itauAtualizarContagem(); return;
  }
  tbody.innerHTML = filtrados.map(l => {
    const idx = itauDados.indexOf(l);
    const cor = l.tipo==='despesa'?'var(--red,#e53e3e)':'var(--green,#38a169)';
    const sinal = l.tipo==='despesa'?'–':'+';
    return `<tr>
      <td><input type="checkbox" class="itau-chk" data-idx="${idx}" ${l._sel?'checked':''} onchange="itauChkChange(this)"></td>
      <td style="white-space:nowrap;font-size:12px">${l.data}</td>
      <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${l.descricao}">${l.descricao}</td>
      <td style="font-size:11px;color:var(--text3);max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${l.empresa||'–'}</td>
      <td style="text-align:right;font-weight:700;color:${cor};white-space:nowrap">${sinal} R$ ${Math.abs(l.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
      <td><span class="badge ${l.tipo==='despesa'?'badge-red':'badge-green'}" style="font-size:10px">${l.tipo==='despesa'?'↓ Saída':'↑ Entrada'}</span></td>
    </tr>`;
  }).join('');
  itauAtualizarContagem();
  itauAtualizarTotais();
}
function itauChkChange(el) {
  const idx = parseInt(el.dataset.idx);
  if (itauDados[idx]) itauDados[idx]._sel = el.checked;
  itauAtualizarContagem(); itauAtualizarTotais();
}
function itauToggleTodos(val) {
  document.querySelectorAll('.itau-chk').forEach(chk => {
    chk.checked = val;
    const idx = parseInt(chk.dataset.idx);
    if (itauDados[idx]) itauDados[idx]._sel = val;
  });
  document.getElementById('itauCheckAll').checked = val;
  itauAtualizarContagem(); itauAtualizarTotais();
}
function itauAtualizarContagem() {
  const sel = itauDados.filter(l=>l._sel).length;
  document.getElementById('itauSelecionados').textContent = sel;
  document.getElementById('btnImportarItau').disabled = sel===0;
}
function itauAtualizarTotais() {
  const sel = itauDados.filter(l=>l._sel);
  const saidas   = sel.filter(l=>l.tipo==='despesa').reduce((s,l)=>s+Math.abs(l.valor),0);
  const entradas = sel.filter(l=>l.tipo==='receita').reduce((s,l)=>s+l.valor,0);
  const fmt = v => 'R$ '+v.toLocaleString('pt-BR',{minimumFractionDigits:2});
  document.getElementById('itauTotais').innerHTML = `
    <span style="color:var(--red)"><b>Total saídas selecionadas:</b> ${fmt(saidas)}</span>
    <span style="color:var(--green)"><b>Total entradas selecionadas:</b> ${fmt(entradas)}</span>`;
}
async function itauConfirmarImport() {
  const selecionados = itauDados.filter(l=>l._sel);
  if (!selecionados.length) { toast('Selecione ao menos um lançamento','error'); return; }
  if (!confirm(`Importar ${selecionados.length} lançamento(s) para o Financeiro?`)) return;
  const btn = document.getElementById('btnImportarItau');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importando...';
  const fd = new FormData();
  fd.append('action','import');
  fd.append('itens', JSON.stringify(selecionados));
  try {
    const res = await fetch(BASE_URL + '/api/importar_itau.php', {method:'POST',body:fd});
    const data = await res.json();
    if (data.success) { toast(`✅ ${data.salvos} lançamento(s) importados!`); closeModal('modalItau'); loadFin(); }
    else toast(data.error||'Erro ao importar','error');
  } catch(err) { toast('Erro de conexão','error'); }
  finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-file-import"></i> Importar Selecionados';
  }
}

loadFin();
</script>
<?php include 'includes/footer.php'; ?>
