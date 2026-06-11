<?php
session_start();

if (isset($_SESSION['usuario_id']) && isset($_SESSION['2fa_ok'])) {
    if ($_SESSION['usuario_tipo'] === 'admin') {
        header('Location: /bloco_b_fire/admin/dashboard.php');
    } else {
        header('Location: /bloco_b_fire/usuario/dashboard.php');
    }
} else {
    header('Location: /bloco_b_fire/login_usuario.php');
}
exit;