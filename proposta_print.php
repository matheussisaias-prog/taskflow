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

$meses_pt = ['janeiro','fevereiro','marco','abril','maio','junho',
             'julho','agosto','setembro','outubro','novembro','dezembro'];
$dp  = $data_prop ? explode('-', $data_prop) : [date('Y'), date('m'), date('d')];
$ano = $dp[0] ?? date('Y');
$mes = $meses_pt[((int)($dp[1] ?? 1)) - 1] ?? '';
$dia = ltrim($dp[2] ?? '1', '0') ?: '1';
$data_extenso = "$dia de $mes de $ano";
$is_pf   = strlen(preg_replace('/\D/','',(string)$cliente_cnpj)) <= 11;
$doc_lbl = $is_pf ? 'CPF' : 'CNPJ';

function splitParaBullets(string $raw): array {
    return array_values(array_filter(array_map('trim', explode("\n", $raw))));
}

$logo_src = "data:image/webp;base64,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";

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Proposta <?= htmlspecialchars($numero) ?> - Terra System</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #222; background: #f4f4f4; }

/* Barra de impressao */
.print-bar { background:#fff; border-bottom:2px solid #0f2747; padding:10px 20px; display:flex; gap:10px; align-items:center; position:sticky; top:0; z-index:999; }
.btn-imp { padding:8px 20px; background:#0f2747; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer; }
.btn-sec { padding:8px 14px; background:#f1f5f9; color:#4a5568; border:1px solid #dde3ec; border-radius:6px; font-size:13px; cursor:pointer; }
@media print { .print-bar { display:none !important; } }

/* Pagina A4 */
.page { background:#fff; width:794px; margin:24px auto; padding:0; box-shadow:0 2px 12px rgba(0,0,0,0.15); }
@media print {
  body { background:#fff; }
  .page { box-shadow:none; margin:0; width:auto; }
  .pg-break { page-break-after: always; }
  .pg-break:last-child { page-break-after: avoid; }
}

/* === CAPA === */
.cover { background:#0f2747; width:794px; height:1123px; position:relative; overflow:hidden; }
@media print { .cover { width:210mm; height:297mm; } }

.cover-logo { position:absolute; top:26px; left:26px; z-index:10; display:flex; align-items:center; gap:10px; }
.cover-body { position:absolute; top:295px; right:40px; left:310px; z-index:10; text-align:center; }
.cover-title { font-size:36px; font-weight:300; color:#0f2747; line-height:1.2; }
.cover-subtitle { font-size:16px; font-weight:700; color:#0f2747; margin-top:10px; line-height:1.4; }
.cover-client { position:absolute; bottom:240px; right:70px; text-align:right; z-index:10; }
.cover-client p { color:#0f2747; font-weight:700; font-size:12px; line-height:2; }
.cover-client .prop-num { font-weight:400; font-size:11px; opacity:.7; }
.cover-city { position:absolute; left:26px; bottom:34px; z-index:10; color:#f5a31a; font-weight:700; font-size:13px; }

/* === PAGINAS INTERNAS === */
.content-page { padding:50px 60px 80px; min-height:1050px; position:relative; }
.header-bar { display:flex; align-items:center; gap:12px; margin-bottom:36px; padding-bottom:16px; border-bottom:3px solid #1a2b4a; }
.header-logo-small { display:flex; align-items:center; gap:8px; }
.hlm { font-size:20px; font-weight:900; color:#1a2b4a; }
.hlm span { color:#f5a623; }
.hls { font-size:7px; color:#1a2b4a; letter-spacing:2px; text-transform:uppercase; }

h1.section-title { font-size:20px; color:#1a2b4a; font-weight:700; margin-bottom:20px; padding-left:10px; border-left:4px solid #f5a623; }
h2.sub-title { font-size:15px; color:#1a2b4a; font-weight:700; margin:24px 0 12px; }

p { line-height:1.7; margin-bottom:10px; text-align:justify; }
ul { margin-left:28px; margin-bottom:14px; }
ul li { line-height:1.9; }

/* Tabelas */
table { width:100%; border-collapse:collapse; margin-bottom:28px; font-size:13px; }
.table-group-header { background:#1a2b4a; color:#fff; text-align:center; font-weight:700; font-size:14px; padding:10px; letter-spacing:1px; }
thead tr th { background:#f5a623; color:#fff; text-align:center; padding:9px 8px; font-weight:700; border:1px solid #ddd; }
tbody tr td { padding:8px 10px; border:1px solid #ddd; text-align:center; vertical-align:middle; }
tbody tr:nth-child(even) { background:#f9f9f9; }
.value-row td { background:#1a2b4a !important; color:#fff; font-weight:700; text-align:right; padding-right:16px; }
.global-row td { background:#1a2b4a !important; color:#fff; font-weight:700; font-size:15px; text-align:right; padding:12px 16px; }

/* Fotos */
.photos-grid { display:flex; flex-direction:column; gap:20px; margin-top:10px; }
.photo-item { text-align:center; }
.photo-item img { max-width:100%; max-height:350px; object-fit:cover; border-radius:6px; border:1px solid #ddd; }
.photo-caption { font-size:12px; color:#555; margin-top:6px; font-style:italic; }

/* Rodape */
.page-footer { position:absolute; bottom:24px; left:60px; right:60px; display:flex; justify-content:space-between; align-items:flex-end; border-top:1px solid #ddd; padding-top:12px; font-size:11px; color:#555; }
.footer-left p { line-height:1.7; }
.footer-right { text-align:right; line-height:1.7; }

/* Assinaturas */
.signatures { display:flex; justify-content:space-around; margin-top:50px; gap:40px; }
.sig-block { text-align:center; flex:1; }
.sig-line { border-top:2px solid #1a2b4a; margin-bottom:8px; margin-top:50px; }
.sig-block p { font-weight:700; font-size:13px; line-height:1.5; text-align:center; }

.highlight-box { background:#eef3fb; border-left:4px solid #1a2b4a; padding:14px 18px; margin-bottom:18px; border-radius:3px; }
.highlight-box p { margin:0; line-height:1.7; }
</style>
</head>
<body>

<div class="print-bar">
  <button class="btn-imp" onclick="window.print()">Imprimir / Salvar PDF</button>
  <button class="btn-sec" onclick="window.close()">Fechar</button>
  <span style="font-size:11px;color:#94a3b8;margin-left:8px">Dica: selecione "Salvar como PDF" ao imprimir</span>
</div>

<?php
// Componentes reutilizaveis
$inner_header = '
<div class="header-bar">
  <div class="header-logo-small">
    <svg width="34" height="34" viewBox="0 0 54 54" fill="none">
      <path d="M6 48 C10 20, 38 6, 48 6 C48 6, 30 14, 20 30 C14 40, 10 48, 6 48Z" fill="#f59e0b"/>
      <path d="M6 48 C20 44, 40 36, 48 6 C48 6, 46 30, 30 40 C20 46, 6 48, 6 48Z" fill="#2e7d32"/>
    </svg>
    <div>
      <div class="hlm"><span>TERRA</span> SYSTEM</div>
      <div class="hls">Geologia e Meio Ambiente</div>
    </div>
  </div>
</div>';

$footer = '
<div class="page-footer">
  <div class="footer-left">
    <p>(85) 9 9640-8459</p>
    <p>contato@terrasystem.com.br</p>
    <p>www.terrasystem.com.br</p>
  </div>
  <div class="footer-right">
    <p><strong>Terra System Geologia e Meio Ambiente</strong></p>
    <p>Rua Erico Mota, 1149 B</p>
    <p>Amadeu Furtado, Fortaleza/CE</p>
  </div>
</div>';
?>

<!-- ============ PAGINA 1 - CAPA ============ -->
<div class="page pg-break">
  <div class="cover">
    <svg style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;"
         viewBox="0 0 794 1123" preserveAspectRatio="none"
         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <rect x="0" y="0" width="794" height="1123" fill="#0f2747"/>
      <!-- <path d="M146.3,297.0 Q132.0,308.0 119.0,319.0 L63.9,333.8 Q52.0,347.0 49.7,372.0 L372.1,1110.3 Q374.0,1116.0 381.0,1123.0 L375.0,1123.0 Q369.0,1117.0 365.0,1108.0 L49.7,372.0 Q46.0,346.0 63.9,333.8 Q101.0,313.0 146.3,297.0 Z" fill="#f59e0b"/> -->
      <!-- <path d="
M120,210
Q110,180 120,160
L360,1110
L375,1123
L364,1111
Z"
fill="#f59e0b"/> -->
<path d="
M 19 338 Q 88 297 158 253 L 327 917 L 386 1160 L 295 951 Z"
fill="#f59e0b"/>
      <path d="M137.8,199.4 Q128.0,171.0 138.5,146.0 L794.0,0.0 L794.0,1123.0 L375.0,1123.0 Q369.0,1121.0 364.0,1111.0 Z" fill="#ffffff"/>
    </svg>

    <div class="cover-logo">
      <img src="<?= $logo_src ?>" style="height:52px;display:block;" alt="Terra System Logo"/>
    </div>

    <div class="cover-body">
      <div class="cover-title">Proposta Comercial</div>
      <div class="cover-subtitle"><?= nl2br(htmlspecialchars($titulo ?: $servico ?: 'Proposta de Servicos')) ?></div>
    </div>

    <?php if ($cliente_nome): ?>
    <div class="cover-client">
      <p><?= htmlspecialchars(strtoupper($cliente_nome)) ?></p>
      <?php if ($cliente_cnpj): ?><p><?= $doc_lbl ?>: <?= htmlspecialchars($cliente_cnpj) ?></p><?php endif; ?>
      <?php if ($numero): ?><p class="prop-num">Proposta N<?= htmlspecialchars($numero) ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="cover-city"><?= htmlspecialchars($local_exec) ?>, <?= $ano ?></div>
  </div>
</div>


<!-- ============ PAGINA 2 - DADOS DO CLIENTE + OBJETIVO ============ -->
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>
    <h1 class="section-title" style="border:none;padding:0;font-size:18px;text-align:center;margin-bottom:30px;">Proposta de Servicos</h1>

    <h2 class="sub-title">1. Dados do Cliente</h2>
    <?php if ($cliente_nome): ?><p><strong>Razao Social:</strong> <?= htmlspecialchars($cliente_nome) ?></p><?php endif; ?>
    <?php if ($cliente_cnpj): ?><p><strong><?= $doc_lbl ?>:</strong> <?= htmlspecialchars($cliente_cnpj) ?></p><?php endif; ?>
    <?php if ($cliente_end):  ?><p><strong>Endereco:</strong> <?= htmlspecialchars($cliente_end) ?></p><?php endif; ?>
    <?php if ($cliente_cont): ?><p><strong>Contato:</strong> <?= htmlspecialchars($cliente_cont) ?></p><?php endif; ?>
    <?php if ($prazo):        ?><p><strong>Prazo do Contrato:</strong> <?= htmlspecialchars($prazo) ?></p><?php endif; ?>

    <?php if ($introducao): ?>
    <h2 class="sub-title">2. Introducao</h2>
    <?php foreach(array_filter(array_map('trim', explode("\n", $introducao))) as $par): ?>
    <p><?= nl2br(htmlspecialchars($par)) ?></p>
    <?php endforeach; ?>
    <?php endif; ?>

    <h2 class="sub-title"><?= $introducao ? '3.' : '2.' ?> Objetivo</h2>
    <?php if ($objetivo): ?>
    <?php foreach(array_filter(array_map('trim', explode("\n", $objetivo))) as $par): ?>
    <p><?= nl2br(htmlspecialchars($par)) ?></p>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($obj_esp)): ?>
    <h2 class="sub-title"><?= $introducao ? '3.1' : '2.1' ?> Objetivos Especificos</h2>
    <ul>
      <?php foreach($obj_esp as $item): ?><li><?= htmlspecialchars(rtrim($item, ';')) ?>;</li><?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <?= $footer ?>
  </div>
</div>


<!-- ============ PAGINA 3 - METODOLOGIA (se houver) ============ -->
<?php if (!empty($metodologias) || !empty($etapas_exec)): ?>
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>
    <h1 class="section-title">4. Metodologia e Escopo</h1>

    <?php foreach($metodologias as $met): ?>
    <?php if (!empty($met['titulo'])): ?><h2 class="sub-title"><?= htmlspecialchars($met['titulo']) ?></h2><?php endif; ?>
    <?php $linhas = array_filter(array_map('trim', explode("\n", $met['itens'] ?? ''))); ?>
    <?php if (!empty($linhas)): ?><ul><?php foreach($linhas as $ln): ?><li><?= htmlspecialchars($ln) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <?php endforeach; ?>

    <?php if (!empty($etapas_exec)): ?>
    <h2 class="sub-title">Etapas de Realizacao do Servico</h2>
    <?php foreach($etapas_exec as $et): ?>
    <?php if (!empty($et['titulo'])): ?><h2 class="sub-title"><?= htmlspecialchars($et['titulo']) ?></h2><?php endif; ?>
    <?php foreach(array_filter(array_map('trim', explode("\n", $et['desc'] ?? ''))) as $par): ?>
    <p><?= nl2br(htmlspecialchars($par)) ?></p>
    <?php endforeach; ?>
    <?php endforeach; ?>
    <?php endif; ?>

    <?= $footer ?>
  </div>
</div>
<?php endif; ?>


<!-- ============ PAGINA - CRONOGRAMA (se houver) ============ -->
<?php if (!empty($cronograma)): ?>
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>
    <h2 class="sub-title">3. Cronograma de Execucao<?= $prazo ? ' - ' . htmlspecialchars($prazo) : '' ?></h2>
    <p style="margin-bottom:12px"><em>Quadro 1 - Distribuicao de atividades e prazo estimado de realizacao.</em></p>
    <table>
      <thead><tr><th style="width:60px">Etapa</th><th>Atividade</th><th style="width:160px">Prazo Estimado</th></tr></thead>
      <tbody>
      <?php foreach($cronograma as $cr): ?>
      <tr>
        <td><?= htmlspecialchars($cr['etapa'] ?? '') ?></td>
        <td style="text-align:left"><?= htmlspecialchars($cr['atividade'] ?? '') ?></td>
        <td><?= htmlspecialchars($cr['periodo'] ?? $cr['frequencia'] ?? '') ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?= $footer ?>
  </div>
</div>
<?php endif; ?>


<!-- ============ PAGINA - EQUIPE + LOGISTICA + QUALIDADE (se houver) ============ -->
<?php if ($equipe_desc || !empty($equipe_membros) || $logistica_raw || $qualidade_raw): ?>
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>

    <?php if ($equipe_desc || !empty($equipe_membros)): ?>
    <h1 class="section-title">6. Equipe Tecnica</h1>
    <?php if ($equipe_desc): ?>
    <?php foreach(array_filter(array_map('trim', explode("\n", $equipe_desc))) as $par): ?><p><?= nl2br(htmlspecialchars($par)) ?></p><?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($equipe_membros)): ?>
    <ul><?php foreach($equipe_membros as $m): ?><li><strong><?= htmlspecialchars($m['cargo'] ?? '') ?></strong><?php if(!empty($m['funcao'])): ?> - <?= htmlspecialchars($m['funcao']) ?><?php endif; ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($logistica_raw): ?>
    <h1 class="section-title">7. Recursos e Logistica</h1>
    <?php $log_linhas = splitParaBullets($logistica_raw); $intro_log = array_shift($log_linhas); ?>
    <?php if ($intro_log): ?><p><?= nl2br(htmlspecialchars($intro_log)) ?></p><?php endif; ?>
    <?php if (!empty($log_linhas)): ?><ul><?php foreach($log_linhas as $ln): ?><li><?= htmlspecialchars($ln) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <?php endif; ?>

    <?php if ($qualidade_raw): ?>
    <h1 class="section-title">8. Garantia de Qualidade</h1>
    <?php $q_linhas = splitParaBullets($qualidade_raw); $intro_q = array_shift($q_linhas); ?>
    <?php if ($intro_q): ?><p><?= nl2br(htmlspecialchars($intro_q)) ?></p><?php endif; ?>
    <?php if (!empty($q_linhas)): ?><ul><?php foreach($q_linhas as $ln): ?><li><?= htmlspecialchars($ln) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <?php endif; ?>

    <?= $footer ?>
  </div>
</div>
<?php endif; ?>


<!-- ============ PAGINAS - VALORES (divididas em chunks de 4 grupos) ============ -->
<?php
$grupos_usar = !empty($grupos)
  ? $grupos
  : [['nome' => '', 'itens' => [['desc' => $servico ?: $titulo, 'qtd' => '1', 'valor' => $valor]]]];
$total_grupos = array_sum(array_map(function($g){ return array_sum(array_column($g['itens'] ?? [], 'valor')); }, $grupos_usar));
$grupos_chunks = array_chunk($grupos_usar, 4);
$chunk_total = count($grupos_chunks);
foreach ($grupos_chunks as $chunk_idx => $chunk):
$is_first = ($chunk_idx === 0);
$is_last  = ($chunk_idx === $chunk_total - 1);
?>
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>

    <?php if ($is_first): ?>
    <h2 class="sub-title">4. Valores</h2>
    <p style="margin-bottom:14px"><em>Quadro 2 - Distribuicao de atividades e valoracao.</em></p>
    <?php else: ?>
    <h2 class="sub-title">Valores (continuacao)</h2>
    <?php endif; ?>

    <?php foreach($chunk as $grupo):
      $subtotal = array_sum(array_column($grupo['itens'] ?? [], 'valor'));
    ?>
    <table>
      <?php if (!empty($grupo['nome'])): ?>
      <thead>
        <tr><th colspan="4" class="table-group-header"><?= htmlspecialchars(strtoupper($grupo['nome'])) ?></th></tr>
        <tr><th>Item</th><th>Descricao</th><th>Quantidade</th><th>Valor por item</th></tr>
      </thead>
      <?php else: ?>
      <thead><tr><th>Item</th><th>Descricao</th><th>Quantidade</th><th>Valor por item</th></tr></thead>
      <?php endif; ?>
      <tbody>
      <?php foreach(($grupo['itens'] ?? []) as $idx => $item): ?>
      <tr>
        <td><?= $idx + 1 ?></td>
        <td style="text-align:left"><?= htmlspecialchars($item['desc'] ?? '') ?></td>
        <td><?= htmlspecialchars($item['qtd'] ?? '1') ?></td>
        <td>R$ <?= number_format((float)($item['valor'] ?? 0), 2, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
      <tr class="value-row">
        <td colspan="3">Valor</td>
        <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
      </tr>
      </tbody>
    </table>
    <?php endforeach; ?>

    <?php if ($is_last): ?>
    <table>
      <tbody>
        <tr class="global-row">
          <td colspan="3">Valor Global</td>
          <td>R$ <?= number_format($valor ?: $total_grupos, 2, ',', '.') ?></td>
        </tr>
      </tbody>
    </table>
    <?php endif; ?>

    <?= $footer ?>
  </div>
</div>
<?php endforeach; ?>


<!-- ============ PAGINA - CONDICOES + OBRIGACOES + ASSINATURAS ============ -->
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>

    <div class="highlight-box" style="margin-bottom:24px">
      <p>O valor da presente proposta de servicos e de <strong>R$ <?= number_format($valor ?: $total_grupos, 2, ',', '.') ?></strong>;</p>
      <?php
      $cond_linhas = $condicoes
        ? array_filter(array_map('trim', explode("\n", $condicoes)))
        : ['A forma de pagamento sera definida entre as partes.'];
      foreach($cond_linhas as $cl): ?>
      <p>• <?= nl2br(htmlspecialchars($cl)) ?></p>
      <?php endforeach; ?>
    </div>

    <?php
    $obrig_list = [];
    if ($obrig_cont) foreach(array_filter(array_map('trim', explode("\n", $obrig_cont))) as $o) $obrig_list[] = $o;
    if (empty($obrig_list)) $obrig_list = [
      'Efetuar o pagamento dentro dos prazos estabelecidos.',
      'Oferecer informacoes necessarias para que a contratada possa executar os servicos acordados na proposta.',
      'Taxas Federais, Estaduais, Municipais e taxas referente a responsabilidade tecnica ficam a cargo do CONTRATANTE.',
      'Executar os servicos contratados dentro do prazo previsto.',
      'Manter a confiabilidade das informacoes fornecidas pelo CONTRATANTE.',
    ];
    ?>
    <h2 class="sub-title">5. Obrigacoes das Partes</h2>
    <ul><?php foreach($obrig_list as $o): ?><li><?= htmlspecialchars($o) ?></li><?php endforeach; ?></ul>

    <?php
    $obs_list = [];
    if ($observacoes) foreach(array_filter(array_map('trim', explode("\n", $observacoes))) as $o) $obs_list[] = $o;
    if (empty($obs_list)) $obs_list = [
      'A CONTRATADA prestara ao CONTRATANTE atendimento pelo e-mail contato@terrasystem.com.br em horarios comerciais.',
      'Documentos e informacoes para o andamento dos processos ficam a cargo do CONTRATANTE.',
      'A proposta contempla apenas os servicos nelas descritos; qualquer outro devera ser mediado atraves de nova proposta.',
    ];
    ?>
    <h2 class="sub-title">6. Observacoes</h2>
    <?php foreach($obs_list as $obs): ?><p><?= nl2br(htmlspecialchars($obs)) ?></p><?php endforeach; ?>

    <p style="text-align:right;margin-top:30px;margin-bottom:40px">
      <?= htmlspecialchars($local_exec) ?>, <?= $data_extenso ?>
    </p>

    <div class="signatures">
      <div class="sig-block">
        <div class="sig-line"></div>
        <p><?= htmlspecialchars(strtoupper($cliente_nome ?: 'Contratante')) ?></p>
        <?php if ($cliente_cnpj): ?><p style="font-weight:400;font-size:12px"><?= $doc_lbl ?>: <?= htmlspecialchars($cliente_cnpj) ?></p><?php endif; ?>
        <p>CONTRATANTE</p>
      </div>
      <div class="sig-block">
        <div class="sig-line"></div>
        <p>TERRA SYSTEM GEOLOGIA E MEIO AMBIENTE</p>
        <p style="font-weight:400;font-size:12px">CNPJ: 50.822.206/0001-75</p>
        <p>CONTRATADA</p>
      </div>
    </div>

    <?= $footer ?>
  </div>
</div>


<!-- ============ PAGINAS - FOTOS DA VISITA TECNICA (se houver) ============ -->
<?php if (!empty($fotos)):
$foto_chunks = array_chunk($fotos, 2);
foreach ($foto_chunks as $foto_chunk):
?>
<div class="page pg-break">
  <div class="content-page">
    <?= $inner_header ?>
    <h1 class="section-title" style="border:none;padding:0;font-size:18px;text-align:center;margin-bottom:24px;letter-spacing:1px;">
      FOTOS DA VISITA TECNICA
    </h1>
    <div class="photos-grid">
      <?php foreach($foto_chunk as $foto): ?>
      <div class="photo-item">
        <img src="<?= htmlspecialchars($foto['base64'] ?? '') ?>" alt="<?= htmlspecialchars($foto['legenda'] ?? 'Foto') ?>">
        <?php if (!empty($foto['legenda'])): ?><div class="photo-caption"><?= htmlspecialchars($foto['legenda']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?= $footer ?>
  </div>
</div>
<?php endforeach; endif; ?>

</body>
</html>
