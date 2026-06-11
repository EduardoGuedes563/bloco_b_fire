<?php
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🔥</div>
        <span>Sistema de<br>Incêndio – B</span>
    </div>

    <nav class="sidebar-menu">
        <a href="/bloco_b_fire/usuario/dashboard.php"
           class="<?= $paginaAtual === 'dashboard.php' ? 'ativo' : '' ?>">
            📊 Dashboard
        </a>
        <a href="/bloco_b_fire/usuario/extintores.php"
           class="<?= $paginaAtual === 'extintores.php' ? 'ativo' : '' ?>">
            🧯 Extintores
        </a>
    </nav>

    <div class="sidebar-usuario">
        <div class="avatar">
            <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)) ?>
        </div>
        <div>
            <div style="font-size:13px;font-weight:600">
                <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
            <a href="/bloco_b_fire/logout.php"
               style="font-size:11px;color:rgba(255,255,255,.5)">
                Sair
            </a>
        </div>
    </div>
</aside>