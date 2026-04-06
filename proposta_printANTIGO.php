<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id'])) { die('Acesso negado'); }

$id    = (int)($_GET['id'] ?? 0);
$p     = db_find('propostas', $id);
if (!$p) { die('Proposta não encontrada'); }

$extra          = $p['extra_data'] ? json_decode($p['extra_data'], true) : [];
$cliente_nome   = $extra['cliente_nome']     ?? $p['empresa_nome'] ?? '';
$cliente_cnpj   = $extra['cliente_cnpj']     ?? '';
$cliente_end    = $extra['cliente_end']       ?? '';
$cliente_cont   = $extra['cliente_contato']  ?? '';
$introducao     = $extra['introducao']        ?? '';
$objetivo       = $extra['objetivo']          ?? $p['escopo'] ?? '';
$obj_esp_raw    = $extra['objetivos_esp']     ?? '';
$obj_esp        = array_filter(array_map('trim', explode("\n", $obj_esp_raw)));
$metodologias   = $extra['metodologias']      ?? [];
$etapas_exec    = $extra['etapas_exec']       ?? [];
$cronograma     = $extra['cronograma']        ?? [];
$equipe_desc    = $extra['equipe_desc']       ?? '';
$equipe_membros = $extra['equipe_membros']    ?? [];
$logistica_raw  = $extra['logistica']         ?? '';
$qualidade_raw  = $extra['qualidade']         ?? '';
$condicoes      = $extra['condicoes']         ?? $p['condicoes'] ?? '';
$observacoes    = $extra['observacoes']       ?? '';
$obrig_cont     = $extra['obrig_contratante'] ?? '';
$grupos         = $extra['grupos']            ?? [];
$local_exec     = $extra['local_exec']        ?? 'Fortaleza/CE';
$data_prop      = $extra['data_prop']         ?? substr($p['created_at'] ?? date('Y-m-d'), 0, 10);
$prazo          = $p['prazo'] ?? '';
$numero         = $p['numero'] ?? '';
$titulo         = $p['titulo'] ?? '';
$servico        = $p['servico'] ?? '';
$valor          = (float)($p['valor'] ?? 0);

// Helper: split text into intro paragraph + bullet items
function splitParaBullets(string $raw): array {
    $lines = array_filter(array_map('trim', explode("\n", $raw)));
    return array_values($lines);
}

$meses_pt = ['janeiro','fevereiro','março','abril','maio','junho',
             'julho','agosto','setembro','outubro','novembro','dezembro'];
$dp  = $data_prop ? explode('-', $data_prop) : [date('Y'), date('m'), date('d')];
$ano = $dp[0] ?? date('Y');
$mes = $meses_pt[((int)($dp[1] ?? 1)) - 1] ?? '';
$dia = ltrim($dp[2] ?? '1', '0') ?: '1';
$data_extenso = "$dia de $mes de $ano";
$is_pf   = strlen(preg_replace('/\D/','',(string)$cliente_cnpj)) <= 11;
$doc_lbl = $is_pf ? 'CPF' : 'CNPJ';
$logo_src = "data:image/webp;base64,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";

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Proposta <?php echo htmlspecialchars($numero); ?> — Terra System</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
body{font-family:'Open Sans',Arial,sans-serif;color:#1a1a2e;background:#fff;font-size:12px;line-height:1.6}
@page{margin:0;size:A4 portrait}
@media print{
  .no-print{display:none!important}
  .pg{page-break-after:always}
  .pg:last-child{page-break-after:avoid}
  .inner{margin:15mm 18mm 12mm}
}

/* ── BARRA IMPRIMIR ── */
.print-bar{background:#fff;border-bottom:2px solid #1a3f72;padding:10px 20px;display:flex;gap:10px;align-items:center;position:sticky;top:0;z-index:999;flex-wrap:wrap}
.btn-imp{padding:8px 20px;background:#1a3f72;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer}
.btn-sec{padding:8px 14px;background:#f1f5f9;color:#4a5568;border:1px solid #dde3ec;border-radius:6px;font-size:13px;cursor:pointer}
.print-tip{font-size:11px;color:#94a3b8}

/* ══════════════════════════════════════════
   CAPA — fundo azul escuro total
   Faixa laranja diagonal à esquerda
   Logo topo esquerdo, título centro-direito,
   cliente inferior direito, local/ano inf esq
══════════════════════════════════════════ */
.cover{
  position:relative;
  width:100%;
  padding-bottom:141.42%;
  background:#0f1e3b;
  overflow:hidden;
  page-break-after:always;
}
@media screen{.cover{max-width:760px;margin:0 auto 20px;box-shadow:0 4px 40px rgba(0,0,0,.25)}}
@media print{.cover{width:210mm;height:297mm;padding-bottom:0}}
.cover-wrap{position:absolute;inset:0}

/* Faixa laranja diagonal */
.cover-orange{
  position:absolute;
  inset:0;
  background:#f5a623;
  clip-path:polygon(0 0, 13% 0, 13% 100%, 0 100%);
  z-index:1;
}
/* Triângulo branco dentro da faixa (cria o efeito diagonal) */
.cover-triangle{
  position:absolute;
  inset:0;
  background:#0f1e3b;
  clip-path:polygon(0 0, 13% 0, 0 40%);
  z-index:2;
}

/* Logo: topo esquerdo, sobre fundo azul */
.cover-logo{
  position:absolute;
  top:4%;
  left:3%;
  width:28%;
  z-index:10;
}
.cover-logo img{width:100%;height:auto;display:block}

/* Título: centro-direito na área branca */
.cover-content{
  position:absolute;
  top:0;bottom:0;
  left:16%;right:0;
  display:flex;
  flex-direction:column;
  justify-content:center;
  padding:8% 10% 8% 10%;
  z-index:5;
}
.cover-tipo{
  font-size:3.5vw;
  font-weight:400;
  color:#fff;
  line-height:1.2;
  margin-bottom:2%;
  font-style:normal;
}
@media print{.cover-tipo{font-size:24pt}}
.cover-titulo{
  font-size:2.2vw;
  font-weight:700;
  color:#fff;
  line-height:1.25;
}
@media print{.cover-titulo{font-size:16pt}}

/* Cliente: inferior direito */
.cover-client{
  position:absolute;
  bottom:8%;
  right:8%;
  text-align:right;
  z-index:10;
}
.cover-client-name{
  font-size:1.4vw;
  font-weight:700;
  color:#fff;
  line-height:1.4;
}
@media print{.cover-client-name{font-size:10pt}}
.cover-client-cnpj{
  font-size:1.1vw;
  color:rgba(255,255,255,.75);
}
@media print{.cover-client-cnpj{font-size:9pt}}

/* Local/ano: inferior esquerdo (sobre área azul) */
.cover-city{
  position:absolute;
  bottom:8%;
  left:3%;
  z-index:10;
  color:#f5a623;
  font-size:1.4vw;
  font-weight:700;
  line-height:1.4;
}
@media print{.cover-city{font-size:10pt}}

/* ══════════════════════════════════════════
   PÁGINAS INTERNAS
══════════════════════════════════════════ */
.inner{padding:20px 40px 20px;max-width:800px;margin:0 auto}

/* Cabeçalho de página interna */
.pg-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #e2e8f0}
.pg-head-logo{height:44px;object-fit:contain}
.pg-head-info{text-align:right;font-size:9.5px;color:#64748b;line-height:1.85}

/* Título da página interna */
.pg-title{text-align:center;font-size:15px;font-weight:700;color:#1a3f72;margin-bottom:20px;text-decoration:underline;text-underline-offset:3px}

/* Seções */
.sec-h{font-size:13px;font-weight:700;color:#1a3f72;margin:18px 0 8px}
.para{font-size:12.5px;color:#374151;text-align:justify;margin-bottom:7px;line-height:1.8;text-indent:2em}
.blist{list-style:disc;padding-left:24px;margin:8px 0 12px}
.blist li{font-size:12.5px;color:#374151;margin-bottom:5px;line-height:1.7}
.data-line{font-size:12.5px;color:#2d3748;margin-bottom:3px;line-height:1.65}

/* Cronograma - tabela simples */
.crono-tab{width:100%;border-collapse:collapse;margin:10px 0 16px;font-size:12.5px}
.crono-tab thead tr{background:#1a3f72;color:#fff}
.crono-tab thead th{padding:9px 14px;font-weight:700;text-align:left}
.crono-tab thead th:first-child{text-align:center;width:60px}
.crono-tab thead th:last-child{text-align:center;width:160px}
.crono-tab tbody tr:nth-child(even){background:#f8fafc}
.crono-tab tbody td{padding:10px 14px;border-bottom:1px solid #e2e8f0;color:#374151}
.crono-tab tbody td:first-child{text-align:center;font-weight:700}
.crono-tab tbody td:last-child{text-align:center}

/* Tabela de valores — múltiplos grupos */
.val-group{margin-bottom:14px}
.val-group-title{
  background:#1a3f72;
  color:#fff;
  text-align:center;
  font-size:12px;
  font-weight:700;
  padding:8px 12px;
  text-transform:uppercase;
  letter-spacing:.04em;
}
.val-tab{width:100%;border-collapse:collapse;font-size:12px}
.val-tab thead tr{background:#f5a623}
.val-tab thead th{padding:8px 12px;font-weight:700;color:#1a1a2e;text-align:center}
.val-tab thead th:nth-child(2){text-align:left}
.val-tab tbody tr:nth-child(even){background:#fafafa}
.val-tab tbody td{padding:9px 12px;border-bottom:1px solid #e8edf3;text-align:center;color:#374151;vertical-align:middle}
.val-tab tbody td:nth-child(2){text-align:left}
.val-tab tbody td:last-child{text-align:right;font-weight:600;white-space:nowrap}
.val-tab tfoot tr{background:#1a3f72;color:#fff}
.val-tab tfoot td{padding:8px 12px;font-weight:700;font-size:12.5px;text-align:right}
.val-tab tfoot td:first-child{text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:.04em}
.val-total-row{background:#0f1e3b!important}
.val-total-row td{color:#fff!important;font-size:14px!important;padding:12px 12px!important}

/* Rodapé de página */
.pg-foot{display:flex;justify-content:space-between;align-items:flex-start;margin-top:18px;padding-top:7px;border-top:1px solid #e2e8f0;font-size:9.5px;color:#94a3b8;line-height:1.9}
.pg-foot strong{color:#1a3f72;font-size:10px}

/* Assinaturas */
.sign-date{text-align:right;font-size:12.5px;color:#374151;margin:24px 0 48px}
.sign-grid{display:grid;grid-template-columns:1fr 1fr;gap:60px}
.sign-block{text-align:center}
.sign-line{border-top:1.5px solid #1a1a2e;margin-bottom:8px}
.sign-name{font-size:12px;font-weight:700;color:#1a1a2e;text-transform:uppercase}
.sign-doc{font-size:11px;color:#64748b;margin-top:2px}
.sign-role{font-size:11px;font-weight:700;color:#1a3f72;text-transform:uppercase;margin-top:3px}
</style>
</head>
<body>

<!-- BARRA IMPRIMIR -->
<div class="print-bar no-print">
  <button class="btn-imp" onclick="window.print()">🖨️ Imprimir / Salvar PDF</button>
  <button class="btn-sec" onclick="window.close()">✕ Fechar</button>
  <span class="print-tip">⚠ Margem: <strong>Nenhuma</strong> · ative <strong>Gráficos de fundo</strong> · desmarque <strong>Cabeçalhos e rodapés</strong></span>
</div>

<!-- ════════ CAPA ════════ -->
<div class="cover pg">
  <div class="cover-wrap">
    <div class="cover-orange"></div>
    <div class="cover-triangle"></div>

    <!-- Logo topo esquerdo -->
    <div class="cover-logo">
      <img src="<?php echo $logo_src; ?>" alt="Terra System">
    </div>

    <!-- Título centralizado -->
    <div class="cover-content">
      <div class="cover-tipo">Proposta Comercial</div>
      <div class="cover-titulo"><?php echo nl2br(htmlspecialchars($titulo ?: $servico ?: 'Proposta de Serviços')); ?></div>
    </div>

    <!-- Cliente inferior direito -->
    <?php if ($cliente_nome): ?>
    <div class="cover-client">
      <div class="cover-client-name"><?php echo htmlspecialchars(strtoupper($cliente_nome)); ?></div>
      <?php if ($cliente_cnpj): ?><div class="cover-client-cnpj"><?php echo $doc_lbl; ?> <?php echo htmlspecialchars($cliente_cnpj); ?></div><?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Local/ano inferior esquerdo -->
    <div class="cover-city"><?php echo htmlspecialchars($local_exec); ?>, <?php echo $ano; ?></div>
  </div>
</div>

<!-- ════════ PÁG INTERNA — DADOS, OBJETIVO, CRONOGRAMA ════════ -->
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?php echo $logo_src; ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <div class="pg-title">Proposta de Serviços</div>

  <!-- 1. DADOS DO CLIENTE -->
  <p class="sec-h">1. Dados do Cliente</p>
  <?php if ($cliente_nome): ?><div class="data-line">Razão Social: <?php echo htmlspecialchars($cliente_nome); ?></div><?php endif; ?>
  <?php if ($cliente_cnpj): ?><div class="data-line"><?php echo $doc_lbl; ?>: <?php echo htmlspecialchars($cliente_cnpj); ?></div><?php endif; ?>
  <?php if ($cliente_end):  ?><div class="data-line">Endereço: <?php echo htmlspecialchars($cliente_end); ?></div><?php endif; ?>
  <?php if ($cliente_cont): ?><div class="data-line">Contato: <?php echo htmlspecialchars($cliente_cont); ?></div><?php endif; ?>

  <!-- 2. OBJETIVO -->
  <p class="sec-h">2. Objetivo</p>
  <?php if ($objetivo): ?>
  <?php foreach(array_filter(array_map('trim',explode("\n",$objetivo))) as $par): ?>
  <p class="para"><?php echo nl2br(htmlspecialchars($par)); ?></p>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($obj_esp)): ?>
  <p class="sec-h" style="font-size:12.5px;margin-top:12px">1. Objetivo Específico</p>
  <ul class="blist">
    <?php foreach($obj_esp as $item): ?><li><?php echo htmlspecialchars(rtrim($item,';')); ?>;</li><?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <!-- 3. CRONOGRAMA -->
  <?php if (!empty($cronograma)): ?>
  <p class="sec-h">3. Cronograma de Execução<?php if($prazo): ?> – <?php echo htmlspecialchars($prazo); ?><?php endif; ?></p>
  <p style="text-align:center;font-size:11.5px;color:#64748b;margin-bottom:8px">Quadro 1 – Distribuição de atividades e prazo estimado de realização.</p>
  <table class="crono-tab">
    <thead><tr><th>Etapa</th><th>Atividade</th><th>Prazo Estimado</th></tr></thead>
    <tbody>
    <?php foreach($cronograma as $cr): ?>
    <tr>
      <td><?php echo htmlspecialchars($cr['etapa']??''); ?></td>
      <td><?php echo htmlspecialchars($cr['atividade']??''); ?></td>
      <td><?php echo htmlspecialchars($cr['periodo']??$cr['frequencia']??''); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <div class="pg-foot">
    <div>☎ (85) 9 9640-8459 &nbsp; contato@terrasystem.com.br &nbsp; www.terrasystem.com.br</div>
    <div style="text-align:right"><strong>Terra System Geologia e Meio Ambiente</strong><br>Rua Érico Mota, 1149 B — Amadeu Furtado, Fortaleza/CE</div>
  </div>
</div></div>

<!-- ════════ PÁG VALORES ════════ -->
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?php echo $logo_src; ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <!-- 4. VALORES -->
  <p class="sec-h">4. Valores</p>
  <p style="text-align:center;font-size:11.5px;color:#64748b;margin-bottom:12px">Quadro 2 – Distribuição de atividades e valoração.</p>

  <?php
  // Renderiza grupos de itens
  $grupos_usar = !empty($grupos) ? $grupos : [['nome'=>'','itens'=>[['desc'=>$servico?:$titulo,'qtd'=>'1','valor'=>$valor]]]];
  foreach ($grupos_usar as $grupo):
    $subtotal = array_sum(array_column($grupo['itens']??[], 'valor'));
  ?>
  <div class="val-group">
    <?php if (!empty($grupo['nome'])): ?>
    <div class="val-group-title"><?php echo htmlspecialchars($grupo['nome']); ?></div>
    <?php endif; ?>
    <table class="val-tab">
      <thead><tr><th style="width:50px">Item</th><th>Descrição</th><th style="width:90px">Quantidade</th><th style="width:130px">Valor por item</th></tr></thead>
      <tbody>
      <?php foreach(($grupo['itens']??[]) as $idx=>$item): ?>
      <tr>
        <td><?php echo $idx+1; ?></td>
        <td style="text-align:left"><?php echo htmlspecialchars($item['desc']??''); ?></td>
        <td><?php echo htmlspecialchars($item['qtd']??'1'); ?></td>
        <td>R$ <?php echo number_format((float)($item['valor']??0),2,',','.'); ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><td>Valor</td><td colspan="3">R$ <?php echo number_format($subtotal,2,',','.'); ?></td></tr>
      </tfoot>
    </table>
  </div>
  <?php endforeach; ?>

  <!-- Total Global -->
  <table class="val-tab" style="margin-top:8px">
    <tfoot>
      <tr class="val-total-row">
        <td colspan="3" style="text-align:left">Valor Global</td>
        <td>R$ <?php echo number_format($valor,2,',','.'); ?></td>
      </tr>
    </tfoot>
  </table>

  <!-- Condições de pagamento -->
  <?php if ($condicoes): ?>
  <ul class="blist" style="margin-top:14px">
    <?php foreach(array_filter(array_map('trim',explode("\n",$condicoes))) as $cl): ?>
    <li><?php echo htmlspecialchars($cl); ?></li>
    <?php endforeach; ?>
  </ul>
  <?php else: ?>
  <ul class="blist" style="margin-top:14px">
    <li>O valor da presente proposta de serviços é de <strong>R$ <?php echo number_format($valor,2,',','.'); ?></strong>;</li>
    <li>O pagamento poderá ser realizado via depósito em conta corrente, boleto bancário e/ou PIX.</li>
  </ul>
  <?php endif; ?>

  <div class="pg-foot">
    <div>☎ (85) 9 9640-8459 &nbsp; contato@terrasystem.com.br &nbsp; www.terrasystem.com.br</div>
    <div style="text-align:right"><strong>Terra System Geologia e Meio Ambiente</strong><br>Rua Érico Mota, 1149 B — Amadeu Furtado, Fortaleza/CE</div>
  </div>
</div></div>

<!-- ════════ PÁG FINAL — OBRIGAÇÕES + ASSINATURAS ════════ -->
<div>
<div class="inner">
  <div class="pg-head">
    <img src="<?php echo $logo_src; ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <!-- 5. OBRIGAÇÕES -->
  <?php
  $all_obrig=[];
  if($obrig_cont) foreach(array_filter(array_map('trim',explode("\n",$obrig_cont))) as $o) $all_obrig[]=$o;
  if(empty($all_obrig)) $all_obrig=[
    'Efetuar o pagamento dentro dos prazos estabelecidos.',
    'Oferecer informações necessárias para que a contratada possa executar os serviços acordados na proposta.',
    'Taxas Federais, Estaduais, Municipais e taxas referente a responsabilidade técnica ficam a cargo do CONTRATANTE.',
    'Executar os serviços contratados dentro do prazo previsto.',
    'Manter a confiabilidade das informações fornecidas pelo CONTRATANTE.',
  ];
  ?>
  <p class="sec-h">5. Obrigações das Partes</p>
  <ul class="blist">
    <?php foreach($all_obrig as $o): ?><li><?php echo htmlspecialchars($o); ?></li><?php endforeach; ?>
  </ul>

  <!-- 6. OBSERVAÇÕES -->
  <?php
  $obs_list=[];
  if($observacoes) foreach(array_filter(array_map('trim',explode("\n",$observacoes))) as $o) $obs_list[]=$o;
  if(empty($obs_list)) $obs_list=[
    'A CONTRATADA prestará ao CONTRATANTE, atendimento de toda e qualquer informação que seja de sua competência, durante o processo, pelo e-mail contato@terrasystem.com.br, em horários comerciais.',
    'Documentos e informações para o andamento dos processos ficam a cargo do CONTRATANTE.',
    'A proposta contempla apenas os serviços nelas descritos, qualquer outro que seja solicitado, deverá ser mediado através de nova proposta de serviços.',
  ];
  ?>
  <p class="sec-h">6. Observações</p>
  <?php foreach($obs_list as $obs): ?><p class="para"><?php echo nl2br(htmlspecialchars($obs)); ?></p><?php endforeach; ?>

  <!-- ASSINATURAS -->
  <div class="sign-date"><?php echo htmlspecialchars($local_exec); ?>, <?php echo $data_extenso; ?></div>
  <div class="sign-grid">
    <div class="sign-block">
      <div class="sign-line"></div>
      <div class="sign-name"><?php echo htmlspecialchars(strtoupper($cliente_nome ?: 'Contratante')); ?></div>
      <?php if ($cliente_cnpj): ?><div class="sign-doc"><?php echo $doc_lbl; ?>: <?php echo htmlspecialchars($cliente_cnpj); ?></div><?php endif; ?>
      <div class="sign-role">Contratante</div>
    </div>
    <div class="sign-block">
      <div class="sign-line"></div>
      <div class="sign-name">Terra System Geologia e Meio Ambiente</div>
      <div class="sign-doc">CNPJ: 50.822.206/0001-75</div>
      <div class="sign-role">Contratada</div>
    </div>
  </div>

  <div class="pg-foot" style="margin-top:28px">
    <div>☎ (85) 9 9640-8459 &nbsp; contato@terrasystem.com.br &nbsp; www.terrasystem.com.br</div>
    <div style="text-align:right"><strong>Terra System Geologia e Meio Ambiente</strong><br>Rua Érico Mota, 1149 B — Amadeu Furtado, Fortaleza/CE</div>
  </div>
</div>
</div>

</body>
</html>
