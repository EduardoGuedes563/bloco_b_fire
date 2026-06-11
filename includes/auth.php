<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

/**
 * Exige que o usuário esteja logado.
 * Se não estiver, redireciona para o login correto.
 */
function exigirLogin(string $tipo = 'usuario'): void {
    if (!isset($_SESSION['usuario_id'])) {
        $destino = ($tipo === 'admin') ? '/bloco_b_fire/login_admin.php' : '/bloco_b_fire/login_usuario.php';
        redirecionar($destino);
    }

    // Verifica se o 2FA foi concluído
    if (!isset($_SESSION['2fa_ok']) || $_SESSION['2fa_ok'] !== true) {
        redirecionar('/bloco_b_fire/verificar_2fa.php');
    }

    // Verifica se o tipo bate (admin acessando área de admin, etc.)
    if ($_SESSION['usuario_tipo'] !== $tipo) {
        // Admin pode acessar tudo, usuário comum não acessa área admin
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            redirecionar('/bloco_b_fire/login_usuario.php');
        }
    }
}

/**
 * Verifica se o usuário logado é admin
 */
function isAdmin(): bool {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
}

/**
 * Destrói a sessão completamente
 */
function logout(): void {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}