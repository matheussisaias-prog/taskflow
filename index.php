<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php'); exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $users = db_where('users', ['email' => $email, 'ativo' => 1]);
    if ($users && password_verify($senha, $users[0]['senha'])) {
        $_SESSION['user_id']     = $users[0]['id'];
        $_SESSION['user_nome']   = $users[0]['nome'];
        $_SESSION['user_email']  = $users[0]['email'];
        $_SESSION['user_perfil'] = $users[0]['perfil'];
        header('Location: ' . BASE_URL . '/dashboard.php'); exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
$B = BASE_URL;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — Terra System CRM</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= $B ?>/assets/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-grid"></div>
  <div class="login-glow"></div>
  <div class="login-box">
    <div class="login-logo">
      <img src="<?= $B ?>/assets/img/logo.webp" alt="Terra System" onerror="this.style.display='none'">
    </div>
    <div class="login-tagline">Geologia e Meio Ambiente</div>
    <div class="login-title">Acesse o CRM</div>
    <div class="login-sub">Sistema de gestão comercial e operacional</div>
    <?php if ($erro): ?>
    <div class="login-error" style="display:flex"><i class="fa-solid fa-triangle-exclamation"></i>&nbsp;<?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <form method="POST">
      <label class="login-lbl">E-mail</label>
      <input type="email" name="email" class="login-input" placeholder="seu@email.com" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <label class="login-lbl">Senha</label>
      <input type="password" name="senha" class="login-input" placeholder="••••••••" required>
      <button type="submit" class="login-btn"><i class="fa-solid fa-right-to-bracket"></i> Entrar</button>
    </form>
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,.08);font-size:12px;color:rgba(255,255,255,.3);text-align:center">
      Terra System CRM &copy; <?= date('Y') ?>
    </div>
  </div>
</div>
</body>
</html>
