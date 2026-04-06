<?php
// includes/topnav.php — Barra de navegação principal
$page_atual  = basename($_SERVER['PHP_SELF'], '.php');
$notifs      = db_where_raw('notificacoes', 'usuario_id = ? AND lida = 0', [$user_id], 'id DESC');
$notif_count = count($notifs);
$B = BASE_URL; // shorthand
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@700;800&family=DM+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= $B ?>/assets/css/style.css">
</head>
<body>
<nav class="topnav">
  <div class="topnav-left">
    <a href="<?= $B ?>/index.php" class="topnav-logo">
      <img src="<?= $B ?>/assets/img/logo.webp" alt="Terra System" onerror="this.style.display='none'">
    </a>
    <div class="topnav-sep"></div>
    <div class="topnav-nav">
      <a href="<?= $B ?>/dashboard.php"      class="topnav-link <?= $page_atual==='dashboard'     ?'active':'' ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <a href="<?= $B ?>/comercial.php"      class="topnav-link <?= $page_atual==='comercial'     ?'active':'' ?>"><i class="fa-solid fa-chart-line"></i> Comercial</a>
      <a href="<?= $B ?>/propostas.php"      class="topnav-link <?= $page_atual==='propostas'     ?'active':'' ?>"><i class="fa-solid fa-file-contract"></i> Propostas</a>
      <a href="<?= $B ?>/ordens_servico.php" class="topnav-link <?= $page_atual==='ordens_servico'?'active':'' ?>"><i class="fa-solid fa-screwdriver-wrench"></i> OS</a>
      <a href="<?= $B ?>/financeiro.php"     class="topnav-link <?= $page_atual==='financeiro'    ?'active':'' ?>"><i class="fa-solid fa-coins"></i> Financeiro</a>
      <a href="<?= $B ?>/empresas.php"       class="topnav-link <?= $page_atual==='empresas'      ?'active':'' ?>"><i class="fa-solid fa-building"></i> Empresas</a>
    </div>
  </div>
  <div class="topnav-right">
    <div class="topnav-notif" id="notifBtn">
      <button class="topnav-btn">
        <i class="fa-solid fa-bell"></i>
        <?php if($notif_count > 0): ?><span class="notif-badge"><?= $notif_count ?></span><?php endif; ?>
      </button>
      <div class="notif-drop" id="notifDrop">
        <div class="notif-drop-head">Notificações</div>
        <?php if(empty($notifs)): ?>
          <div class="notif-empty"><i class="fa-solid fa-check-circle"></i> Tudo em dia!</div>
        <?php else: foreach($notifs as $n): ?>
          <div class="notif-item notif-<?= $n['tipo'] ?>">
            <div class="notif-title"><?= htmlspecialchars($n['titulo']) ?></div>
            <div class="notif-msg"><?= htmlspecialchars($n['mensagem'] ?? '') ?></div>
          </div>
        <?php endforeach; endif; ?>
        <div class="notif-drop-foot">
          <button onclick="marcarLidas()">Marcar todas como lidas</button>
        </div>
      </div>
    </div>
    <div class="topnav-user" id="userMenu">
      <div class="topnav-avatar"><?= strtoupper(substr($user_nome, 0, 2)) ?></div>
      <span class="topnav-uname"><?= htmlspecialchars(explode(' ', $user_nome)[0]) ?></span>
      <i class="fa-solid fa-chevron-down" style="font-size:10px;color:rgba(255,255,255,.4)"></i>
      <div class="user-drop">
        <div class="user-drop-head">
          <div class="user-drop-name"><?= htmlspecialchars($user_nome) ?></div>
          <div class="user-drop-email"><?= htmlspecialchars($user_email) ?></div>
        </div>
        <a href="<?= $B ?>/configuracoes.php" class="user-drop-item"><i class="fa-solid fa-gear"></i> Configurações</a>
        <a href="<?= $B ?>/api/auth.php?action=logout" class="user-drop-item danger"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
      </div>
    </div>
  </div>
</nav>
<main class="main-content">

<script>
const BASE_URL = '<?= $B ?>';

document.getElementById('notifBtn').addEventListener('click', function(e) {
  e.stopPropagation();
  document.getElementById('notifDrop').classList.toggle('open');
  document.querySelector('.user-drop').classList.remove('open');
});
document.getElementById('userMenu').addEventListener('click', function(e) {
  e.stopPropagation();
  this.querySelector('.user-drop').classList.toggle('open');
  document.getElementById('notifDrop').classList.remove('open');
});
document.addEventListener('click', function() {
  document.querySelectorAll('.notif-drop, .user-drop').forEach(el => el.classList.remove('open'));
});
function marcarLidas() {
  fetch(BASE_URL + '/api/notifications.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({action:'mark_all_read'})
  }).then(() => location.reload());
}
</script>
