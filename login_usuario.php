<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/mailer.php';

// Se já está logado com 2FA ok, redireciona
if (isset($_SESSION['usuario_id'], $_SESSION['2fa_ok'])) {
    header('Location: /bloco_b_fire/usuario/dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validação CSRF
    if (!validarCsrf($_POST['csrf_token'] ?? '')) {
        $erro = 'Requisição inválida. Tente novamente.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $erro = 'Preencha todos os campos.';
        } else {
            // PDO com prepared statement — protegido contra SQL Injection
           $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND tipo = 'usuario' LIMIT 1");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Credenciais corretas — gera código 2FA
                $codigo    = gerarCodigo2FA();
                $expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                $upd = $pdo->prepare("UPDATE usuarios SET codigo_2fa = :codigo, expiracao_2fa = :exp WHERE id = :id");
                $upd->execute([
                    ':codigo' => $codigo,
                    ':exp'    => $expiracao,
                    ':id'     => $usuario['id']
                ]);

                // Salva na sessão temporária (ainda não está logado de verdade)
                $_SESSION['2fa_usuario_id']   = $usuario['id'];
                $_SESSION['2fa_usuario_nome'] = $usuario['nome'];
                $_SESSION['2fa_usuario_tipo'] = $usuario['tipo'];
                $_SESSION['2fa_email']        = $usuario['email'];

                // Envia e-mail com o código
                enviarCodigo2FA($usuario['email'], $usuario['nome'], $codigo);

                header('Location: /bloco_b_fire/verificar_2fa.php');
                exit;
            } else {
                $erro = 'E-mail ou senha incorretos.';
            }
        }
    }
}

$csrf = gerarCsrf();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistema de Incêndio Bloco B</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">

        <div class="login-header">
            <span class="icone-logo">🔥</span>
            <h1>Sistema de Incêndio</h1>
            <p>Bloco B — UNESC</p>
        </div>

        <div class="login-body">
            <?php if ($erro): ?>
                <div class="alerta-erro"><?= h($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="" novalidate>
                <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">

                <div class="mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="seu@email.com"
                        value="<?= h($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn-login">
                    Entrar
                </button>
            </form>
        </div>

        <div class="login-footer">
            É administrador?
            <a href="/bloco_b_fire/login_admin.php">Acesse aqui</a>
        </div>
    </div>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>