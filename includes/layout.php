<?php
// ============================================================
// LAYOUT.PHP — Header, Sidebar e Footer do TopoGest
// ============================================================

function renderHeader(string $title, string $activePage, string $base = '../'): void { ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — TopoGest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <polygon points="14,2 26,24 2,24" fill="none" stroke="#3B82F6" stroke-width="2"/>
                <line x1="14" y1="8" x2="14" y2="24" stroke="#3B82F6" stroke-width="1.5" stroke-dasharray="2,2"/>
                <line x1="8" y1="24" x2="20" y2="24" stroke="#60A5FA" stroke-width="1.5"/>
                <circle cx="14" cy="2" r="1.5" fill="#60A5FA"/>
            </svg>
        </div>
        <div>
            <span class="brand-name">TopoGest</span>
            <span class="brand-sub">Sistema de Operações</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section">Principal</span>
        <a href="<?= $base ?>index.php" class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 1116 0A8 8 0 012 10zm8-7a7 7 0 000 14A7 7 0 0010 3z"/><path d="M10 9a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm0-3a1 1 0 100 2 1 1 0 000-2z"/></svg>
            Dashboard
        </a>

        <span class="nav-section">Operações</span>
        <a href="<?= $base ?>os/index.php" class="nav-item <?= $activePage === 'os' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
            Ordens de Serviço
            <?php if ($activePage === 'os'): ?>
            <span class="nav-badge">Centro</span>
            <?php endif; ?>
        </a>
        <a href="<?= $base ?>propostas/index.php" class="nav-item <?= $activePage === 'propostas' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
            Propostas
        </a>
        <a href="<?= $base ?>clientes/index.php" class="nav-item <?= $activePage === 'clientes' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-5L9 4H4zm7 5a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"/></svg>
            Clientes
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">AD</div>
            <div>
                <div class="user-name">Administrador</div>
                <div class="user-role">Sistema Interno</div>
            </div>
        </div>
    </div>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()" id="menuToggle">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
            </button>
            <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
        </div>
        <div class="topbar-right">
            <span class="topbar-date"><?= date('d \d\e F \d\e Y') ?></span>
        </div>
    </header>
    <main class="main-content">
<?php }

function renderFooter(string $base = '../'): void { ?>
    </main>
</div><!-- /.main-wrapper -->

<script src="<?= $base ?>assets/js/app.js"></script>
</body>
</html>
<?php }
