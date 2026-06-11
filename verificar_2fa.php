<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Se não veio do fluxo de login, rejeita
if (!isset($_SESSION['2fa_usuario_id'])) {
    header('Location: /bloco_b_fire/login_usuario.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarCsrf($_POST['csrf_token'] ?? '')) {
        $erro = 'Requisição inválida.';
    } else {
        // Junta os 6 campos em uma string
        $digitos = '';
        for ($i = 1; $i <= 6; $i++) {
            $digitos .= preg_replace('/\D/', '', $_POST["d$i"] ?? '');
        }

        if (strlen($digitos) !== 6) {
            $erro = 'Digite todos os 6 dígitos do código.';
        } else {
            $id = $_SESSION['2fa_usuario_id'];

            $stmt = $pdo->prepare("SELECT codigo_2fa, expiracao_2fa FROM usuarios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            if (!$row || $row['codigo_2fa'] !== $digitos) {
                $erro = 'Código incorreto. Tente novamente.';
            } elseif (new DateTime() > new DateTime($row['expiracao_2fa'])) {
                $erro = 'Código expirado. Faça login novamente.';
                // Limpa sessão temporária para forçar novo login
                unset($_SESSION['2fa_usuario_id']);
                session_regenerate_id(true);
            } else {
                // 2FA OK — inicia sessão real
                session_regenerate_id(true); // Proteção contra session fixation

                $_SESSION['usuario_id']   = $_SESSION['2fa_usuario_id'];
                $_SESSION['usuario_nome'] = $_SESSION['2fa_usuario_nome'];
                $_SESSION['usuario_tipo'] = $_SESSION['2fa_usuario_tipo'];
                $_SESSION['2fa_ok']       = true;

                // Limpa dados temporários e o código do banco
                unset($_SESSION['2fa_usuario_id'], $_SESSION['2fa_usuario_nome'],
                      $_SESSION['2fa_usuario_tipo'], $_SESSION['2fa_email']);

                $pdo->prepare("UPDATE usuarios SET codigo_2fa = NULL, expiracao_2fa = NULL WHERE id = :id")
                    ->execute([':id' => $_SESSION['usuario_id']]);

                // Redireciona para o dashboard correto
                if ($_SESSION['usuario_tipo'] === 'admin') {
                    header('Location: /bloco_b_fire/admin/dashboard.php');
                } else {
                    header('Location: /bloco_b_fire/usuario/dashboard.php');
                }
                exit;
            }
        }
    }
}

$csrf = gerarCsrf();
$email = $_SESSION['2fa_email'] ?? '';
// Mascara o e-mail: jo**@email.com
$emailMascarado = preg_replace('/(?<=.{2}).(?=.*@)/', '*', $email);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA — Sistema de Incêndio Bloco B</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">

        <div class="login-header">
            <span class="icone-logo">✉️</span>
            <h1>Verificação em dois fatores</h1>
            <p>Código enviado para <?= h($emailMascarado) ?></p>
        </div>

        <div class="login-body">
            <?php if ($erro): ?>
                <div class="alerta-erro"><?= h($erro) ?></div>
            <?php endif; ?>

            <p style="font-size:13px;color:#666;margin-bottom:18px;text-align:center">
                Digite o código de 6 dígitos enviado ao seu e-mail. Ele expira em 10 minutos.
            </p>

            <form method="POST" action="" id="form2fa" novalidate>
                <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">

                <div class="codigo-2fa">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <input
                            type="text"
                            name="d<?= $i ?>"
                            id="d<?= $i ?>"
                            maxlength="1"
                            inputmode="numeric"
                            pattern="[0-9]"
                            autocomplete="one-time-code"
                        >
                    <?php endfor; ?>
                </div>

                <button type="submit" class="btn-login" style="margin-top:18px">
                    Verificar código
                </button>
            </form>
        </div>

        <div class="login-footer">
            <a href="/bloco_b_fire/login_usuario.php">← Voltar ao login</a>
        </div>
    </div>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>