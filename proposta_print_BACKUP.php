<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id'])) { die('Acesso negado'); }

$id = (int)($_GET['id'] ?? 0);
$p  = db_find('propostas', $id);
if (!$p) { die('Proposta não encontrada'); }

$extra           = $p['extra_data'] ? json_decode($p['extra_data'], true) : [];
$cliente_nome    = $extra['cliente_nome']      ?? $p['empresa_nome'] ?? '';
$cliente_cnpj    = $extra['cliente_cnpj']      ?? '';
$cliente_end     = $extra['cliente_end']        ?? '';
$cliente_cont    = $extra['cliente_contato']   ?? '';
$introducao      = $extra['introducao']         ?? '';
$objetivo        = $extra['objetivo']           ?? $p['escopo'] ?? '';
$obj_esp_raw     = $extra['objetivos_esp']      ?? '';
$obj_esp         = array_filter(array_map('trim', explode("\n", $obj_esp_raw)));
$metodologias    = $extra['metodologias']       ?? [];
$etapas_exec     = $extra['etapas_exec']        ?? [];
$cronograma      = $extra['cronograma']         ?? [];
$equipe_desc     = $extra['equipe_desc']        ?? '';
$equipe_membros  = $extra['equipe_membros']     ?? [];
$logistica_raw   = $extra['logistica']          ?? '';
$qualidade_raw   = $extra['qualidade']          ?? '';
$condicoes       = $extra['condicoes']          ?? $p['condicoes'] ?? '';
$observacoes     = $extra['observacoes']        ?? '';
$obrig_cont      = $extra['obrig_contratante']  ?? '';
$grupos          = $extra['grupos']             ?? [];
$fotos           = $extra['fotos']              ?? [];
$local_exec      = $extra['local_exec']         ?? 'Fortaleza/CE';
$data_prop       = $extra['data_prop']          ?? substr($p['created_at'] ?? date('Y-m-d'), 0, 10);
$prazo           = $p['prazo']  ?? '';
$numero          = $p['numero'] ?? '';
$titulo          = $p['titulo'] ?? '';
$servico         = $p['servico'] ?? '';
$valor           = (float)($p['valor'] ?? 0);

// Data por extenso
$meses_pt = ['janeiro','fevereiro','março','abril','maio','junho',
             'julho','agosto','setembro','outubro','novembro','dezembro'];
$dp  = $data_prop ? explode('-', $data_prop) : [date('Y'), date('m'), date('d')];
$ano = $dp[0] ?? date('Y');
$mes = $meses_pt[((int)($dp[1] ?? 1)) - 1] ?? '';
$dia = ltrim($dp[2] ?? '1', '0') ?: '1';
$data_extenso = "$dia de $mes de $ano";
$is_pf   = strlen(preg_replace('/\D/','',(string)$cliente_cnpj)) <= 11;
$doc_lbl = $is_pf ? 'CPF' : 'CNPJ';

// Helper: split em intro + bullets
function splitParaBullets(string $raw): array {
    return array_values(array_filter(array_map('trim', explode("\n", $raw))));
}

// Logo base64 (webp)
$logo_src = "data:image/webp;base64,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";

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Proposta <?= htmlspecialchars($numero) ?> — Terra System</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
body{font-family:'Open Sans',Arial,sans-serif;color:#1a1a2e;background:#fff;font-size:11.5px;line-height:1.65}
@page{margin:0;size:A4 portrait}
@media print{
  .no-print{display:none!important}
  .pg{page-break-after:always}
  .pg:last-child{page-break-after:avoid}
}

/* ── BARRA IMPRIMIR ── */
.print-bar{background:#fff;border-bottom:2px solid #1a3f72;padding:10px 20px;display:flex;gap:10px;align-items:center;position:sticky;top:0;z-index:999;flex-wrap:wrap}
.btn-imp{padding:8px 20px;background:#1a3f72;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer}
.btn-sec{padding:8px 14px;background:#f1f5f9;color:#4a5568;border:1px solid #dde3ec;border-radius:6px;font-size:13px;cursor:pointer}
.print-tip{font-size:11px;color:#94a3b8}

/* ══ CAPA ═══════════════════════════════════════════════
   Fundo azul escuro total
   Faixa laranja vertical à esquerda (8% da largura)
   com triângulo azul no topo criando efeito diagonal
   Logo topo esquerdo (sobre fundo azul, à direita da faixa)
   Título + tipo centrado-direito
   Cliente inferior direito
   Local/ano inferior esquerdo
═══════════════════════════════════════════════════════ */
.cover{
  position:relative;
  width:100%;
  padding-bottom:141.42%;   /* proporção A4 */
  background:#0f1e3b;
  overflow:hidden;
  page-break-after:always;
}
@media screen{.cover{max-width:760px;margin:0 auto 20px;box-shadow:0 4px 40px rgba(0,0,0,.3)}}
@media print{.cover{width:210mm;height:297mm;padding-bottom:0}}
.cover-wrap{position:absolute;inset:0}

/* Faixa laranja vertical */
.cover-orange{
  position:absolute;inset:0;
  background:#f5a623;
  clip-path:polygon(0 0, 8% 0, 8% 100%, 0 100%);
  z-index:1;
}
/* Triângulo azul que corta a faixa criando diagonal */
.cover-triangle{
  position:absolute;inset:0;
  background:#0f1e3b;
  clip-path:polygon(0 0, 8% 0, 0 38%);
  z-index:2;
}

/* Logo topo esquerdo — sobre área azul à direita da faixa */
.cover-logo{
  position:absolute;
  top:3.5%;
  left:10%;
  width:26%;
  z-index:10;
}
.cover-logo img{width:100%;height:auto;display:block}

/* Linha divisória horizontal decorativa abaixo do logo */
.cover-divider{
  position:absolute;
  top:13%;
  left:10%;right:6%;
  height:1px;
  background:rgba(245,166,35,.45);
  z-index:10;
}

/* Número da proposta — canto superior direito */
.cover-numero{
  position:absolute;
  top:3.5%;right:6%;
  z-index:10;
  color:rgba(255,255,255,.5);
  font-size:1.1vw;
  letter-spacing:.08em;
  font-weight:600;
}
@media print{.cover-numero{font-size:8pt}}

/* Tipo + Título: centro vertical ligeiramente acima */
.cover-content{
  position:absolute;
  top:0;bottom:0;
  left:10%;right:6%;
  display:flex;
  flex-direction:column;
  justify-content:center;
  padding-bottom:12%;
  z-index:5;
}
.cover-tipo{
  font-size:2.2vw;
  font-weight:300;
  color:rgba(255,255,255,.8);
  letter-spacing:.15em;
  text-transform:uppercase;
  margin-bottom:2%;
}
@media print{.cover-tipo{font-size:15pt}}
.cover-titulo{
  font-size:2.8vw;
  font-weight:800;
  color:#fff;
  line-height:1.2;
  border-left:3px solid #f5a623;
  padding-left:14px;
}
@media print{.cover-titulo{font-size:19pt}}

/* Serviço / subtítulo */
.cover-servico{
  margin-top:2%;
  font-size:1.3vw;
  color:rgba(255,255,255,.65);
  padding-left:17px;
}
@media print{.cover-servico{font-size:9.5pt}}

/* Cliente inferior direito */
.cover-client{
  position:absolute;
  bottom:7%;right:6%;
  text-align:right;
  z-index:10;
}
.cover-client-label{
  font-size:1vw;color:rgba(255,255,255,.5);
  letter-spacing:.1em;text-transform:uppercase;
  margin-bottom:4px;
}
@media print{.cover-client-label{font-size:7pt}}
.cover-client-name{
  font-size:1.5vw;font-weight:700;color:#fff;line-height:1.35;
}
@media print{.cover-client-name{font-size:10.5pt}}
.cover-client-cnpj{font-size:1.1vw;color:rgba(255,255,255,.6);}
@media print{.cover-client-cnpj{font-size:8.5pt}}

/* Local/ano: inferior esquerdo (sobre faixa laranja, margem) */
.cover-city{
  position:absolute;
  bottom:7%;left:10%;
  z-index:10;
  color:#f5a623;
  font-size:1.3vw;font-weight:700;line-height:1.5;
}
@media print{.cover-city{font-size:9.5pt}}

/* Número proposta inferior */
.cover-prop-num{
  position:absolute;
  bottom:7%;left:10%;
  margin-top:18px;
  /* empurrado abaixo do city via margin-top não funciona em absolute;
     usamos transform */
  transform:translateY(130%);
  z-index:10;
  color:rgba(255,255,255,.45);
  font-size:1vw;
  letter-spacing:.08em;
}
@media print{.cover-prop-num{font-size:7.5pt}}

/* ══ PÁGINAS INTERNAS ═══════════════════════════════════ */
.inner{padding:14mm 18mm 10mm;max-width:210mm;margin:0 auto}
@media screen{.inner{max-width:800px;padding:22px 40px}}

/* Cabeçalho interno */
.pg-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding-bottom:8px;border-bottom:2.5px solid #1a3f72}
.pg-head-logo{height:40px;object-fit:contain}
.pg-head-info{text-align:right;font-size:9px;color:#64748b;line-height:2}

/* Título de página interna */
.pg-title{
  text-align:center;font-size:14px;font-weight:800;color:#1a3f72;
  margin-bottom:18px;
  padding-bottom:6px;
  border-bottom:2px solid #f5a623;
  text-transform:uppercase;letter-spacing:.05em;
}

/* Seções */
.sec-h{
  font-size:12.5px;font-weight:800;color:#0f1e3b;
  margin:18px 0 8px;
  padding:6px 12px;
  background:#f0f4fb;
  border-left:4px solid #1a3f72;
  border-radius:0 4px 4px 0;
}
.sub-sec-h{
  font-size:12px;font-weight:700;color:#1a3f72;
  margin:14px 0 6px;
  padding-left:12px;
  border-left:3px solid #f5a623;
}
.para{font-size:11.5px;color:#374151;text-align:justify;margin-bottom:6px;line-height:1.8;text-indent:2em}
.blist{list-style:none;padding-left:0;margin:6px 0 12px}
.blist li{font-size:11.5px;color:#374151;margin-bottom:5px;line-height:1.7;padding-left:16px;position:relative}
.blist li::before{content:"•";position:absolute;left:0;color:#f5a623;font-weight:700;font-size:14px;line-height:1.4}
.data-line{font-size:11.5px;color:#2d3748;margin-bottom:4px;line-height:1.7}
.data-label{font-weight:700;color:#1a3f72}

/* ══ TABELA CRONOGRAMA ══ */
.crono-tab{width:100%;border-collapse:collapse;margin:8px 0 14px;font-size:11.5px}
.crono-tab thead tr{background:#1a3f72;color:#fff}
.crono-tab thead th{padding:9px 12px;font-weight:700;text-align:left}
.crono-tab thead th:first-child{text-align:center;width:55px}
.crono-tab thead th:last-child{text-align:center;width:150px}
.crono-tab tbody tr:nth-child(even){background:#f8fafc}
.crono-tab tbody tr:hover{background:#e8f0fb}
.crono-tab tbody td{padding:9px 12px;border-bottom:1px solid #e2e8f0;color:#374151}
.crono-tab tbody td:first-child{text-align:center;font-weight:700;color:#1a3f72}
.crono-tab tbody td:last-child{text-align:center}

/* ══ TABELA DE VALORES ══ */
.val-group{margin-bottom:16px}
.val-group-title{
  background:#1a3f72;color:#fff;
  text-align:center;font-size:11.5px;font-weight:700;
  padding:8px 14px;
  text-transform:uppercase;letter-spacing:.05em;
}
.val-tab{width:100%;border-collapse:collapse;font-size:11.5px}
.val-tab thead tr{background:#f5a623}
.val-tab thead th{padding:8px 12px;font-weight:700;color:#1a1a2e;text-align:center}
.val-tab thead th:nth-child(2){text-align:left}
.val-tab tbody tr:nth-child(even){background:#fafafa}
.val-tab tbody td{padding:9px 12px;border-bottom:1px solid #e8edf3;text-align:center;color:#374151;vertical-align:middle}
.val-tab tbody td:nth-child(2){text-align:left}
.val-tab tbody td:last-child{text-align:right;font-weight:600;white-space:nowrap}
.val-tab tfoot tr{background:#1a3f72;color:#fff}
.val-tab tfoot td{padding:8px 12px;font-weight:700;text-align:right}
.val-tab tfoot td:first-child{text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.04em;font-weight:400}
.val-total-row{background:#0f1e3b!important}
.val-total-row td{color:#fff!important;font-size:14px!important;padding:12px!important;font-weight:800!important}
.val-total-row td:last-child{color:#f5a623!important}

/* ══ FOTOS ══ */
.fotos-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:12px;
  margin:12px 0;
}
.foto-item{
  border:1px solid #e2e8f0;
  border-radius:6px;
  overflow:hidden;
}
.foto-item img{
  width:100%;
  height:160px;
  object-fit:cover;
  display:block;
}
.foto-legenda{
  font-size:10px;
  color:#64748b;
  text-align:center;
  padding:5px 8px;
  background:#f8fafc;
  font-style:italic;
}
@media print{
  .foto-item img{height:140px}
}

/* ══ RODAPÉ DE PÁGINA ══ */
.pg-foot{
  display:flex;justify-content:space-between;align-items:flex-start;
  margin-top:16px;padding-top:7px;
  border-top:1.5px solid #e2e8f0;
  font-size:8.5px;color:#94a3b8;line-height:2;
}
.pg-foot strong{color:#1a3f72;font-size:9px}

/* ══ ASSINATURAS ══ */
.sign-date{text-align:right;font-size:11.5px;color:#374151;margin:28px 0 52px}
.sign-grid{display:grid;grid-template-columns:1fr 1fr;gap:70px}
.sign-block{text-align:center}
.sign-line{border-top:1.5px solid #1a1a2e;margin-bottom:9px}
.sign-name{font-size:11.5px;font-weight:800;color:#1a1a2e;text-transform:uppercase}
.sign-doc{font-size:10.5px;color:#64748b;margin-top:2px}
.sign-role{font-size:11px;font-weight:700;color:#1a3f72;text-transform:uppercase;margin-top:4px;letter-spacing:.04em}
</style>
</head>
<body>

<!-- BARRA IMPRIMIR -->
<div class="print-bar no-print">
  <button class="btn-imp" onclick="window.print()">🖨️ Imprimir / Salvar PDF</button>
  <button class="btn-sec" onclick="window.close()">✕ Fechar</button>
  <span class="print-tip">⚠ Margem: <strong>Nenhuma</strong> · ative <strong>Gráficos de fundo</strong> · desmarque <strong>Cabeçalhos e rodapés</strong></span>
</div>

<!-- ════════════ CAPA ════════════ -->
<div class="cover pg">
  <div class="cover-wrap">
    <div class="cover-orange"></div>
    <div class="cover-triangle"></div>

    <!-- Logo -->
    <div class="cover-logo">
      <img src="<?= $logo_src ?>" alt="Terra System">
    </div>

    <!-- Linha decorativa -->
    <div class="cover-divider"></div>

    <!-- Número topo direito -->
    <div class="cover-numero"><?= htmlspecialchars($numero) ?></div>

    <!-- Tipo + Título -->
    <div class="cover-content">
      <div class="cover-tipo">Proposta Comercial</div>
      <div class="cover-titulo"><?= nl2br(htmlspecialchars($titulo ?: $servico ?: 'Proposta de Serviços')) ?></div>
      <?php if ($servico && $servico !== $titulo): ?>
      <div class="cover-servico"><?= htmlspecialchars($servico) ?></div>
      <?php endif; ?>
    </div>

    <!-- Cliente inferior direito -->
    <?php if ($cliente_nome): ?>
    <div class="cover-client">
      <div class="cover-client-label">Preparado para</div>
      <div class="cover-client-name"><?= htmlspecialchars(strtoupper($cliente_nome)) ?></div>
      <?php if ($cliente_cnpj): ?>
      <div class="cover-client-cnpj"><?= $doc_lbl ?>: <?= htmlspecialchars($cliente_cnpj) ?></div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Local e ano inferior esquerdo -->
    <div class="cover-city">
      <?= htmlspecialchars($local_exec) ?><br>
      <span style="font-weight:300;color:rgba(255,255,255,.55);font-size:.9em"><?= $dia ?> de <?= $mes ?> de <?= $ano ?></span>
    </div>
  </div>
</div><!-- /cover -->

<!-- ════════════ PÁG 2 — DADOS DO CLIENTE + INTRODUÇÃO + OBJETIVO ════════════ -->
<div class="pg">
<div class="inner">
  <?php include_once __DIR__ . '/../includes/pg_head_foot.php'; // usa inline abaixo ?>

  <!-- Cabeçalho interno -->
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <div class="pg-title">Proposta de Serviços Técnicos</div>

  <!-- 1. DADOS DO CLIENTE -->
  <p class="sec-h">1. Dados do Cliente / Contratante</p>
  <div style="padding:10px 14px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0;margin-bottom:6px">
    <?php if ($cliente_nome): ?><div class="data-line"><span class="data-label">Razão Social / Unidade:</span> <?= htmlspecialchars($cliente_nome) ?></div><?php endif; ?>
    <?php if ($cliente_cnpj): ?><div class="data-line"><span class="data-label"><?= $doc_lbl ?>:</span> <?= htmlspecialchars($cliente_cnpj) ?></div><?php endif; ?>
    <?php if ($cliente_end):  ?><div class="data-line"><span class="data-label">Endereço / Área:</span> <?= htmlspecialchars($cliente_end) ?></div><?php endif; ?>
    <?php if ($cliente_cont): ?><div class="data-line"><span class="data-label">Contato:</span> <?= htmlspecialchars($cliente_cont) ?></div><?php endif; ?>
    <?php if ($prazo):        ?><div class="data-line"><span class="data-label">Prazo do Contrato:</span> <?= htmlspecialchars($prazo) ?></div><?php endif; ?>
  </div>

  <!-- 2. INTRODUÇÃO -->
  <?php if ($introducao): ?>
  <p class="sec-h">2. Introdução</p>
  <?php foreach(array_filter(array_map('trim',explode("\n",$introducao))) as $par): ?>
  <p class="para"><?= nl2br(htmlspecialchars($par)) ?></p>
  <?php endforeach; ?>
  <?php endif; ?>

  <!-- 3. OBJETIVO GERAL -->
  <p class="sec-h">3. Objetivo</p>
  <?php if ($objetivo): ?>
  <?php foreach(array_filter(array_map('trim',explode("\n",$objetivo))) as $par): ?>
  <p class="para"><?= nl2br(htmlspecialchars($par)) ?></p>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($obj_esp)): ?>
  <p class="sub-sec-h">3.1 Objetivos Específicos</p>
  <ul class="blist">
    <?php foreach($obj_esp as $item): ?><li><?= htmlspecialchars(rtrim($item,';')) ?>;</li><?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <?php // Rodapé -->
  $pgFootHtml = '<div class="pg-foot">
    <div>☎ (85) 9 9640-8459 &nbsp; contato@terrasystem.com.br &nbsp; www.terrasystem.com.br</div>
    <div style="text-align:right"><strong>Terra System Geologia e Meio Ambiente</strong><br>Rua Érico Mota, 1149 B — Amadeu Furtado, Fortaleza/CE</div>
  </div>'; echo $pgFootHtml; ?>
</div></div>

<!-- ════════════ PÁG 3 — METODOLOGIA E ETAPAS ════════════ -->
<?php if (!empty($metodologias) || !empty($etapas_exec)): ?>
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <div class="pg-title">Metodologia e Escopo dos Serviços</div>

  <!-- 4. METODOLOGIA -->
  <?php if (!empty($metodologias)): ?>
  <p class="sec-h">4. Metodologia</p>
  <?php foreach($metodologias as $idx => $met): ?>
  <?php if (!empty($met['titulo'])): ?>
  <p class="sub-sec-h"><?= htmlspecialchars($met['titulo']) ?></p>
  <?php endif; ?>
  <?php
  $linhas = array_filter(array_map('trim', explode("\n", $met['itens'] ?? '')));
  if (!empty($linhas)):
  ?>
  <ul class="blist">
    <?php foreach($linhas as $ln): ?><li><?= htmlspecialchars($ln) ?></li><?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php endforeach; ?>
  <?php endif; ?>

  <!-- 4.4 ETAPAS -->
  <?php if (!empty($etapas_exec)): ?>
  <p class="sec-h">4.<?= !empty($metodologias) ? count($metodologias)+1 : '1' ?>. Etapas Para a Realização do Serviço</p>
  <?php foreach($etapas_exec as $et): ?>
  <?php if (!empty($et['titulo'])): ?>
  <p class="sub-sec-h"><?= htmlspecialchars($et['titulo']) ?></p>
  <?php endif; ?>
  <?php foreach(array_filter(array_map('trim',explode("\n",$et['desc']??''))) as $par): ?>
  <p class="para"><?= nl2br(htmlspecialchars($par)) ?></p>
  <?php endforeach; ?>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php echo $pgFootHtml; ?>
</div></div>
<?php endif; ?>

<!-- ════════════ PÁG 4 — CRONOGRAMA ════════════ -->
<?php if (!empty($cronograma)): ?>
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <p class="sec-h" style="margin-top:0">5. Cronograma de Execução<?php if($prazo): ?> — <?= htmlspecialchars($prazo) ?><?php endif; ?></p>
  <p style="text-align:center;font-size:10.5px;color:#64748b;margin-bottom:10px">Quadro 1 – Distribuição de atividades e prazo estimado de realização.</p>

  <table class="crono-tab">
    <thead>
      <tr>
        <th>Etapa</th>
        <th>Atividade</th>
        <th>Prazo Estimado</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($cronograma as $cr): ?>
    <tr>
      <td><?= htmlspecialchars($cr['etapa']??'') ?></td>
      <td><?= htmlspecialchars($cr['atividade']??'') ?></td>
      <td><?= htmlspecialchars($cr['periodo']??$cr['frequencia']??'') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php echo $pgFootHtml; ?>
</div></div>
<?php endif; ?>

<!-- ════════════ PÁG 5 — EQUIPE + LOGÍSTICA + QUALIDADE ════════════ -->
<?php if ($equipe_desc || !empty($equipe_membros) || $logistica_raw || $qualidade_raw): ?>
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <!-- 6. EQUIPE TÉCNICA -->
  <?php if ($equipe_desc || !empty($equipe_membros)): ?>
  <p class="sec-h">6. Equipe Técnica</p>
  <?php if ($equipe_desc): ?>
  <?php foreach(array_filter(array_map('trim',explode("\n",$equipe_desc))) as $par): ?>
  <p class="para"><?= nl2br(htmlspecialchars($par)) ?></p>
  <?php endforeach; ?>
  <?php endif; ?>
  <?php if (!empty($equipe_membros)): ?>
  <ul class="blist" style="margin-top:8px">
    <?php foreach($equipe_membros as $m): ?>
    <li><strong><?= htmlspecialchars($m['cargo']??'') ?></strong><?php if(!empty($m['funcao'])): ?> — <?= htmlspecialchars($m['funcao']??'') ?><?php endif; ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php endif; ?>

  <!-- 7. RECURSOS E LOGÍSTICA -->
  <?php if ($logistica_raw): ?>
  <p class="sec-h">7. Recursos e Logística</p>
  <?php
  $logistica_linhas = splitParaBullets($logistica_raw);
  $intro_log = array_shift($logistica_linhas);
  if ($intro_log): ?>
  <p class="para"><?= nl2br(htmlspecialchars($intro_log)) ?></p>
  <?php endif; ?>
  <?php if (!empty($logistica_linhas)): ?>
  <ul class="blist">
    <?php foreach($logistica_linhas as $ln): ?><li><?= htmlspecialchars($ln) ?></li><?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php endif; ?>

  <!-- 8. GARANTIA DE QUALIDADE -->
  <?php if ($qualidade_raw): ?>
  <p class="sec-h">8. Garantia de Qualidade</p>
  <?php
  $qual_linhas = splitParaBullets($qualidade_raw);
  $intro_qual = array_shift($qual_linhas);
  if ($intro_qual): ?>
  <p class="para"><?= nl2br(htmlspecialchars($intro_qual)) ?></p>
  <?php endif; ?>
  <?php if (!empty($qual_linhas)): ?>
  <ul class="blist">
    <?php foreach($qual_linhas as $ln): ?><li><?= htmlspecialchars($ln) ?></li><?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php endif; ?>

  <?php echo $pgFootHtml; ?>
</div></div>
<?php endif; ?>

<!-- ════════════ PÁG 6 — FOTOS ════════════ -->
<?php if (!empty($fotos)): ?>
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <p class="sec-h" style="margin-top:0">Registro Fotográfico</p>

  <div class="fotos-grid">
    <?php foreach($fotos as $foto): ?>
    <div class="foto-item">
      <img src="<?= htmlspecialchars($foto['base64'] ?? '') ?>" alt="<?= htmlspecialchars($foto['legenda'] ?? 'Foto') ?>">
      <?php if (!empty($foto['legenda'])): ?>
      <div class="foto-legenda"><?= htmlspecialchars($foto['legenda']) ?></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <?php echo $pgFootHtml; ?>
</div></div>
<?php endif; ?>

<!-- ════════════ PÁG 7 — VALORES (INVESTIMENTO) ════════════ -->
<div class="pg">
<div class="inner">
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <p class="sec-h" style="margin-top:0">9. Investimento</p>
  <p style="text-align:center;font-size:10.5px;color:#64748b;margin-bottom:14px">Quadro 2 – Distribuição de atividades e valoração dos serviços.</p>

  <?php
  $grupos_usar = !empty($grupos)
    ? $grupos
    : [['nome'=>'','itens'=>[['desc'=>$servico?:$titulo,'qtd'=>'1','valor'=>$valor]]]];

  foreach ($grupos_usar as $grupo):
    $subtotal = array_sum(array_column($grupo['itens']??[], 'valor'));
  ?>
  <div class="val-group">
    <?php if (!empty($grupo['nome'])): ?>
    <div class="val-group-title"><?= htmlspecialchars($grupo['nome']) ?></div>
    <?php endif; ?>
    <table class="val-tab">
      <thead>
        <tr>
          <th style="width:44px">Item</th>
          <th>Descrição</th>
          <th style="width:80px">Qtd</th>
          <th style="width:130px">Valor Unit.</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach(($grupo['itens']??[]) as $idx => $item): ?>
      <tr>
        <td><?= $idx+1 ?></td>
        <td style="text-align:left"><?= htmlspecialchars($item['desc']??'') ?></td>
        <td><?= htmlspecialchars($item['qtd']??'1') ?></td>
        <td>R$ <?= number_format((float)($item['valor']??0),2,',','.') ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3">Subtotal</td>
          <td>R$ <?= number_format($subtotal,2,',','.') ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?php endforeach; ?>

  <!-- Total global -->
  <table class="val-tab" style="margin-top:10px">
    <tfoot>
      <tr class="val-total-row">
        <td colspan="3" style="text-align:left">Valor Global da Proposta</td>
        <td>R$ <?= number_format($valor,2,',','.') ?></td>
      </tr>
    </tfoot>
  </table>

  <!-- Condições de pagamento -->
  <?php
  $cond_linhas = $condicoes
    ? array_filter(array_map('trim', explode("\n", $condicoes)))
    : [
        "O valor da presente proposta é de <strong>R$ ".number_format($valor,2,',','.')."</strong>;",
        "O pagamento poderá ser realizado via depósito em conta corrente, boleto bancário e/ou PIX.",
      ];
  ?>
  <ul class="blist" style="margin-top:14px">
    <?php foreach($cond_linhas as $cl): ?><li><?= $cl ?></li><?php endforeach; ?>
  </ul>

  <?php echo $pgFootHtml; ?>
</div></div>

<!-- ════════════ PÁG 8 — OBRIGAÇÕES + OBSERVAÇÕES + ASSINATURAS ════════════ -->
<div>
<div class="inner">
  <div class="pg-head">
    <img src="<?= $logo_src ?>" alt="Terra System" class="pg-head-logo">
    <div class="pg-head-info">
      (85) 9 9640-8459 &nbsp;|&nbsp; Terra System Geologia e Meio Ambiente<br>
      contato@terrasystem.com.br &nbsp;|&nbsp; Rua Érico Mota, 1149 B<br>
      www.terrasystem.com.br &nbsp;|&nbsp; Amadeu Furtado, Fortaleza/CE
    </div>
  </div>

  <!-- 10. OBRIGAÇÕES -->
  <?php
  $obrig_list = [];
  if ($obrig_cont) foreach(array_filter(array_map('trim',explode("\n",$obrig_cont))) as $o) $obrig_list[] = $o;
  if (empty($obrig_list)) $obrig_list = [
    'Efetuar o pagamento dentro dos prazos estabelecidos.',
    'Oferecer informações necessárias para que a contratada possa executar os serviços acordados na proposta.',
    'Taxas Federais, Estaduais, Municipais e taxas referente à responsabilidade técnica ficam a cargo do CONTRATANTE.',
    'Executar os serviços contratados dentro do prazo previsto.',
    'Manter a confiabilidade das informações fornecidas pelo CONTRATANTE.',
  ];
  ?>
  <p class="sec-h" style="margin-top:0">10. Obrigações das Partes</p>
  <ul class="blist">
    <?php foreach($obrig_list as $o): ?><li><?= htmlspecialchars($o) ?></li><?php endforeach; ?>
  </ul>

  <!-- 11. OBSERVAÇÕES -->
  <?php
  $obs_list = [];
  if ($observacoes) foreach(array_filter(array_map('trim',explode("\n",$observacoes))) as $o) $obs_list[] = $o;
  if (empty($obs_list)) $obs_list = [
    'A CONTRATADA prestará ao CONTRATANTE atendimento de toda e qualquer informação que seja de sua competência, durante o processo, pelo e-mail contato@terrasystem.com.br, em horários comerciais.',
    'Documentos e informações para o andamento dos processos ficam a cargo do CONTRATANTE.',
    'A proposta contempla apenas os serviços nelas descritos; qualquer outro que seja solicitado deverá ser mediado através de nova proposta de serviços.',
  ];
  ?>
  <p class="sec-h">11. Observações</p>
  <?php foreach($obs_list as $obs): ?>
  <p class="para"><?= nl2br(htmlspecialchars($obs)) ?></p>
  <?php endforeach; ?>

  <!-- ASSINATURAS -->
  <div class="sign-date"><?= htmlspecialchars($local_exec) ?>, <?= $data_extenso ?></div>
  <div class="sign-grid">
    <div class="sign-block">
      <div class="sign-line"></div>
      <div class="sign-name"><?= htmlspecialchars(strtoupper($cliente_nome ?: 'Contratante')) ?></div>
      <?php if ($cliente_cnpj): ?><div class="sign-doc"><?= $doc_lbl ?>: <?= htmlspecialchars($cliente_cnpj) ?></div><?php endif; ?>
      <div class="sign-role">Contratante</div>
    </div>
    <div class="sign-block">
      <div class="sign-line"></div>
      <div class="sign-name">Terra System Geologia e Meio Ambiente</div>
      <div class="sign-doc">CNPJ: 50.822.206/0001-75</div>
      <div class="sign-role">Contratada</div>
    </div>
  </div>

  <?php echo $pgFootHtml; ?>
</div>
</div>

</body>
</html>
