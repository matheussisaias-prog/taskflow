<?php
require_once 'includes/auth_check.php';

// KPIs
$leads_total   = db_count('leads');
$leads_fechados= db_count('leads', ['etapa' => 'fechado']);
$props_total   = db_count('propostas');
$props_aprov   = db_count('propostas', ['status' => 'aprovada']);
$os_abertas    = db_count('ordens_servico', ['status' => 'aberta']);
$os_andamento  = db_count('ordens_servico', ['status' => 'em_andamento']);

$receitas = db_sum('financeiro', 'valor', ['tipo' => 'receita', 'status' => 'pago']);
$despesas = db_sum('financeiro', 'valor', ['tipo' => 'despesa', 'status' => 'pago']);
$pendentes= db_sum('financeiro', 'valor', ['tipo' => 'receita', 'status' => 'pendente']);

// Últimas OS
$ultimas_os = db_where_raw('ordens_servico', "status != 'cancelada'", [], 'id DESC');
$ultimas_os = array_slice($ultimas_os, 0, 6);

// Últimos leads
$ultimos_leads = db_all('leads', 'id DESC');
$ultimos_leads = array_slice($ultimos_leads, 0, 5);

// Financeiro mensal (6 meses)
$meses_data = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime("-$i months");
    $ym = date('Y-m', $ts);
    $label = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'][(int)date('m',$ts)-1];
    $st_r = db()->prepare("SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='receita' AND status='pago' AND DATE_FORMAT(data_pagamento,'%Y-%m')=?");
    $st_r->execute([$ym]); $rec = (float)$st_r->fetchColumn();
    $st_d = db()->prepare("SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='despesa' AND status='pago' AND DATE_FORMAT(data_pagamento,'%Y-%m')=?");
    $st_d->execute([$ym]); $desp = (float)$st_d->fetchColumn();
    $meses_data[] = ['label' => $label, 'receita' => $rec, 'despesa' => $desp];
}

include 'includes/topnav.php';
?>
<div class="page-header">
  <div class="page-header-left">
    <h1>Dashboard</h1>
    <p>Visão geral do sistema — <?= date('d/m/Y') ?></p>
  </div>
</div>

<!-- KPIs -->
<div class="kpi-grid" style="grid-template-columns:repeat(5,1fr)">
  <div class="kpi-card" style="--kpi-color:var(--ts-blue);--kpi-soft:var(--blue-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-users"></i></div>
    <div><div class="kpi-label">Leads Ativos</div><div class="kpi-value"><?= $leads_total ?></div><div class="kpi-sub"><?= $leads_fechados ?> fechados</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--orange);--kpi-soft:var(--orange-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-file-contract"></i></div>
    <div><div class="kpi-label">Propostas</div><div class="kpi-value"><?= $props_total ?></div><div class="kpi-sub"><?= $props_aprov ?> aprovadas</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--purple);--kpi-soft:var(--purple-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-screwdriver-wrench"></i></div>
    <div><div class="kpi-label">Ordens de Serviço</div><div class="kpi-value"><?= $os_abertas + $os_andamento ?></div><div class="kpi-sub"><?= $os_andamento ?> em andamento</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--green);--kpi-soft:var(--green-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-arrow-trend-up"></i></div>
    <div><div class="kpi-label">Receitas Pagas</div><div class="kpi-value" style="font-size:16px"><?= moeda($receitas) ?></div><div class="kpi-sub" style="color:var(--yellow)"><?= moeda($pendentes) ?> pendente</div></div>
  </div>
  <div class="kpi-card" style="--kpi-color:var(--red);--kpi-soft:var(--red-soft)">
    <div class="kpi-icon-wrap"><i class="fa-solid fa-arrow-trend-down"></i></div>
    <div><div class="kpi-label">Despesas</div><div class="kpi-value" style="font-size:16px"><?= moeda($despesas) ?></div><div class="kpi-sub">Saldo: <?= moeda($receitas - $despesas) ?></div></div>
  </div>
</div>

<div class="grid-2" style="margin-bottom:24px">
  <!-- Gráfico financeiro -->
  <div class="card">
    <div class="card-header"><div class="card-title"><i class="fa-solid fa-chart-bar"></i> Financeiro — Últimos 6 Meses</div></div>
    <div class="card-body"><div class="chart-wrap"><canvas id="chartFin"></canvas></div></div>
  </div>
  <!-- Últimas OS -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><i class="fa-solid fa-list-check"></i> Ordens de Serviço Recentes</div>
      <a href="<?= BASE_URL ?>/ordens_servico.php" class="btn btn-secondary btn-sm">Ver todas</a>
    </div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Número</th><th>Título</th><th>Status</th><th>Prazo</th></tr></thead>
        <tbody>
        <?php foreach($ultimas_os as $os): ?>
        <tr>
          <td><span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--ts-blue);font-weight:700"><?= htmlspecialchars($os['numero']) ?></span></td>
          <td><?= htmlspecialchars(mb_strimwidth($os['titulo'],0,40,'…')) ?></td>
          <td><span class="badge os-status-<?= $os['status'] ?>"><?= str_replace('_',' ',ucfirst($os['status'])) ?></span></td>
          <td><?= $os['data_prazo'] ? fmtDataBR($os['data_prazo']) : '–' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($ultimas_os)): ?><tr><td colspan="4" style="text-align:center;color:var(--text3);padding:24px">Nenhuma OS</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Últimos leads -->
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-user-clock"></i> Leads Recentes</div>
    <a href="<?= BASE_URL ?>/comercial.php" class="btn btn-secondary btn-sm">Ver todos</a>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead><tr><th>Nome</th><th>Empresa</th><th>Etapa</th><th>Temperatura</th><th>Valor</th></tr></thead>
      <tbody>
      <?php
      $etapas_label=['lead_recebido'=>['Recebido','badge-blue'],'contato_realizado'=>['Contato','badge-purple'],'proposta_enviada'=>['Proposta','badge-orange'],'negociacao'=>['Negociação','badge-yellow'],'fechado'=>['Fechado','badge-green'],'perdido'=>['Perdido','badge-red']];
      foreach($ultimos_leads as $l):
        [$el,$ec]=$etapas_label[$l['etapa']]??['–','badge-gray'];
      ?>
      <tr>
        <td><b><?= htmlspecialchars($l['nome']) ?></b></td>
        <td><?= htmlspecialchars($l['empresa_nome']??'–') ?></td>
        <td><span class="badge <?= $ec ?>"><?= $el ?></span></td>
        <td><span class="badge temp-<?= $l['temperatura'] ?>"><?= ucfirst($l['temperatura']) ?></span></td>
        <td style="font-family:'DM Mono',monospace;color:var(--green);font-weight:700"><?= $l['valor_estimado']>0?moeda((float)$l['valor_estimado']):'–' ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
const meses = <?= json_encode(array_column($meses_data,'label')) ?>;
const recs   = <?= json_encode(array_column($meses_data,'receita')) ?>;
const desps  = <?= json_encode(array_column($meses_data,'despesa')) ?>;

new Chart(document.getElementById('chartFin'), {
  type: 'bar',
  data: {
    labels: meses,
    datasets: [
      { label: 'Receitas', data: recs, backgroundColor: 'rgba(16,185,129,.7)', borderRadius: 6 },
      { label: 'Despesas', data: desps, backgroundColor: 'rgba(239,68,68,.6)', borderRadius: 6 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } },
    scales: {
      y: { ticks: { callback: v => 'R$ ' + v.toLocaleString('pt-BR') }, grid: { color: 'rgba(0,0,0,.05)' } }
    }
  }
});
</script>

<?php
function fmtDataBR($d) {
    if (!$d) return '–';
    [$y,$m,$dd] = explode('-', $d);
    return "$dd/$m/$y";
}
include 'includes/footer.php';
?>
