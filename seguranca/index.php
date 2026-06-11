<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

exigirLogin('admin');

$resultadoVulneravel  = null;
$resultadoSeguro      = null;
$tentativaVulneravel  = '';
$tentativaSegura      = '';
$erroVulneravel       = '';

// ============================================
// SIMULAÇÃO VULNERÁVEL (apenas para demonstrar
// — nunca use isso em produção!)
// ============================================
if (isset($_POST['testar_vulneravel'])) {
    $entrada = $_POST['entrada_vulneravel'] ?? '';
    $tentativaVulneravel = $entrada;

    try {
        // Query INSEGURA — concatena direto na string
        $sql = "SELECT id, nome, email, tipo FROM usuarios WHERE email = '$entrada'";
        $stmt = $pdo->query($sql);
        $resultadoVulneravel = $stmt->fetchAll();
    } catch (PDOException $e) {
        $erroVulneravel = $e->getMessage();
    }
}

// ============================================
// VERSÃO SEGURA COM PDO + PREPARED STATEMENT
// ============================================
if (isset($_POST['testar_seguro'])) {
    $entrada = $_POST['entrada_segura'] ?? '';
    $tentativaSegura = $entrada;

    // Query SEGURA — PDO trata o input como dado, nunca como SQL
    $stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $entrada]);
    $resultadoSeguro = $stmt->fetchAll();
}

$csrf = gerarCsrf();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segurança — Sistema de Incêndio Bloco B</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
    <style>
        .sec-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }
        .sec-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .sec-card-header {
            padding: 18px 24px;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sec-card-header.vermelho { background: #fadbd8; color: #c0392b; }
        .sec-card-header.verde    { background: #d5f5e3; color: #1e8449; }
        .sec-card-body { padding: 24px; }
        .codigo-sql {
            background: #1e1e2e;
            color: #cdd6f4;
            padding: 14px 18px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 12px 0;
            overflow-x: auto;
            white-space: pre;
        }
        .codigo-sql .destaque { color: #f38ba8; font-weight: 700; }
        .resultado-box {
            margin-top: 14px;
            border-radius: 8px;
            padding: 14px;
            font-size: 13px;
        }
        .resultado-perigo  { background: #fadbd8; border-left: 4px solid #c0392b; color: #922b21; }
        .resultado-seguro  { background: #d5f5e3; border-left: 4px solid #1e8449; color: #1e8449; }
        .resultado-neutro  { background: #f4f6f7; border-left: 4px solid #bbb; color: #555; }
        .badge-tecnica {
            display: inline-block;
            background: #eaf3de;
            color: #3b6d11;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            margin: 4px 4px 4px 0;
        }
        .input-demo {
            width: 100%;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 9px 13px;
            font-size: 13px;
            font-family: 'Courier New', monospace;
            margin-bottom: 10px;
        }
        .input-demo:focus {
            border-color: #c0392b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(192,57,43,.12);
        }
        .tabela-resultado { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
        .tabela-resultado th { background: #f4f6f7; padding: 8px 12px; text-align: left; font-size: 11px; text-transform: uppercase; color: #888; }
        .tabela-resultado td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php require_once '../includes/sidebar_admin.php'; ?>

    <main class="conteudo">

        <!-- Cabeçalho -->
        <div style="margin-bottom:28px">
            <h2 style="font-size:22px;font-weight:700;margin:0">Segurança do Sistema</h2>
            <p style="color:#888;font-size:13px;margin:4px 0 0">
                Demonstração prática das proteções implementadas
            </p>
        </div>

        <!-- Cards de técnicas usadas -->
        <div style="background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);padding:24px;margin-bottom:32px">
            <h5 style="font-size:16px;font-weight:700;margin:0 0 16px">Técnicas de segurança implementadas</h5>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">

                <div style="border:1.5px solid #eee;border-radius:10px;padding:16px">
                    <div style="font-size:24px;margin-bottom:8px">🛡️</div>
                    <div style="font-weight:700;font-size:14px;margin-bottom:4px">PDO Prepared Statements</div>
                    <div style="font-size:12px;color:#888">Toda query usa parâmetros vinculados. O input nunca é concatenado diretamente no SQL.</div>
                </div>

                <div style="border:1.5px solid #eee;border-radius:10px;padding:16px">
                    <div style="font-size:24px;margin-bottom:8px">🔐</div>
                    <div style="font-weight:700;font-size:14px;margin-bottom:4px">Bcrypt (password_hash)</div>
                    <div style="font-size:12px;color:#888">Senhas nunca são salvas em texto puro. O bcrypt adiciona salt automático e é resistente a brute-force.</div>
                </div>

                <div style="border:1.5px solid #eee;border-radius:10px;padding:16px">
                    <div style="font-size:24px;margin-bottom:8px">✉️</div>
                    <div style="font-weight:700;font-size:14px;margin-bottom:4px">2FA por E-mail</div>
                    <div style="font-size:12px;color:#888">Código OTP de 6 dígitos com expiração de 10 minutos. Mesmo com a senha, sem o código não entra.</div>
                </div>

                <div style="border:1.5px solid #eee;border-radius:10px;padding:16px">
                    <div style="font-size:24px;margin-bottom:8px">🎫</div>
                    <div style="font-weight:700;font-size:14px;margin-bottom:4px">Token CSRF</div>
                    <div style="font-size:12px;color:#888">Todo formulário tem um token único por sessão. Impede ataques de requisição forjada entre sites.</div>
                </div>

                <div style="border:1.5px solid #eee;border-radius:10px;padding:16px">
                    <div style="font-size:24px;margin-bottom:8px">🚫</div>
                    <div style="font-weight:700;font-size:14px;margin-bottom:4px">XSS — htmlspecialchars</div>
                    <div style="font-size:12px;color:#888">Todo dado exibido na tela passa por htmlspecialchars(). Scripts maliciosos são neutralizados.</div>
                </div>

                <div style="border:1.5px solid #eee;border-radius:10px;padding:16px">
                    <div style="font-size:24px;margin-bottom:8px">🔑</div>
                    <div style="font-weight:700;font-size:14px;margin-bottom:4px">Controle de Acesso</div>
                    <div style="font-size:12px;color:#888">Usuário comum só visualiza. Admin tem acesso ao CRUD. Toda página verifica a sessão antes de carregar.</div>
                </div>

            </div>
        </div>

        <!-- Demonstração SQL Injection -->
        <h4 style="font-size:18px;font-weight:700;margin:0 0 16px">
            Demonstração prática — SQL Injection
        </h4>

        <div class="sec-grid">

            <!-- VULNERÁVEL -->
            <div class="sec-card">
                <div class="sec-card-header vermelho">
                    ❌ Sistema SEM proteção (vulnerável)
                </div>
                <div class="sec-card-body">
                    <p style="font-size:13px;color:#666;margin:0 0 12px">
                        A query abaixo concatena o input diretamente no SQL. Um atacante pode manipulá-la.
                    </p>

                    <div class="codigo-sql">SELECT * FROM usuarios
WHERE email = '<span class="destaque">$entrada</span>'</div>

                    <p style="font-size:12px;font-weight:700;color:#888;margin:14px 0 6px">
                        TESTE — tente injetar esse código:
                    </p>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                        <input
                            type="text"
                            name="entrada_vulneravel"
                            class="input-demo"
                            value="<?= h($tentativaVulneravel) ?>"
                            placeholder="admin' OR '1'='1"
                        >
                        <button type="submit" name="testar_vulneravel"
                                style="background:#c0392b;color:#fff;border:none;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;width:100%">
                            Executar query vulnerável
                        </button>
                    </form>

                    <?php if ($tentativaVulneravel !== ''): ?>
                        <div class="resultado-box <?= !empty($resultadoVulneravel) ? 'resultado-perigo' : 'resultado-neutro' ?>">
                            <?php if ($erroVulneravel): ?>
                                <strong>Erro SQL:</strong> <?= h($erroVulneravel) ?>
                            <?php elseif (!empty($resultadoVulneravel)): ?>
                                <strong>⚠️ BRECHA! Retornou <?= count($resultadoVulneravel) ?> registro(s) sem autenticação válida:</strong>
                                <table class="tabela-resultado">
                                    <thead>
                                        <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Tipo</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultadoVulneravel as $r): ?>
                                        <tr>
                                            <td><?= h((string)$r['id']) ?></td>
                                            <td><?= h($r['nome']) ?></td>
                                            <td><?= h($r['email']) ?></td>
                                            <td><?= h($r['tipo']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <strong>Nenhum resultado.</strong> Tente: <code>admin' OR '1'='1</code>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SEGURO -->
            <div class="sec-card">
                <div class="sec-card-header verde">
                    ✅ Sistema COM proteção (PDO Prepared Statement)
                </div>
                <div class="sec-card-body">
                    <p style="font-size:13px;color:#666;margin:0 0 12px">
                        O PDO trata o input como dado puro, nunca como código SQL. A injeção é impossível.
                    </p>

                    <div class="codigo-sql">$stmt = $pdo->prepare(
  "SELECT * FROM usuarios
   WHERE email = <span class="destaque">:email</span>"
);
$stmt->execute([':email' => $entrada]);</div>

                    <p style="font-size:12px;font-weight:700;color:#888;margin:14px 0 6px">
                        TESTE — tente o mesmo ataque:
                    </p>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                        <input
                            type="text"
                            name="entrada_segura"
                            class="input-demo"
                            value="<?= h($tentativaSegura) ?>"
                            placeholder="admin' OR '1'='1"
                        >
                        <button type="submit" name="testar_seguro"
                                style="background:#1e8449;color:#fff;border:none;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;width:100%">
                            Executar query segura
                        </button>
                    </form>

                    <?php if ($tentativaSegura !== ''): ?>
                        <div class="resultado-box <?= !empty($resultadoSeguro) ? 'resultado-neutro' : 'resultado-seguro' ?>">
                            <?php if (!empty($resultadoSeguro)): ?>
                                <strong>Encontrou <?= count($resultadoSeguro) ?> registro(s)</strong> — porque o e-mail realmente existe no banco.
                            <?php else: ?>
                                <strong>✅ Protegido!</strong> Nenhum resultado. O ataque foi neutralizado — o input foi tratado como texto, não como SQL.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

        <!-- Explicação técnica -->
        <div style="background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);padding:24px">
            <h5 style="font-size:16px;font-weight:700;margin:0 0 16px">Como o ataque funciona e por que é bloqueado</h5>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
                <div>
                    <p style="font-size:13px;font-weight:700;color:#c0392b;margin:0 0 8px">O que o atacante digita:</p>
                    <div class="codigo-sql">admin' OR '1'='1</div>
                    <p style="font-size:13px;font-weight:700;color:#c0392b;margin:12px 0 8px">SQL gerado sem proteção:</p>
                    <div class="codigo-sql">SELECT * FROM usuarios
WHERE email = '<span class="destaque">admin' OR '1'='1</span>'

-- A condição OR '1'='1' é sempre
-- verdadeira → retorna TODOS os
-- usuários do banco!</div>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#1e8449;margin:0 0 8px">Com PDO Prepared Statement:</p>
                    <div class="codigo-sql">-- O PDO envia separado:
-- Query:  WHERE email = ?
-- Dados:  "admin' OR '1'='1"
--
-- O banco trata como string literal
-- Busca e-mail que seja exatamente
-- "admin' OR '1'='1" → não existe
-- → retorna vazio → ✅ seguro!</div>
                    <div style="margin-top:16px;padding:14px;background:#d5f5e3;border-radius:8px;font-size:13px;color:#1e8449">
                        <strong>Resumo:</strong> Prepared Statements separam o código SQL dos dados. O banco compila a query primeiro, depois substitui os parâmetros — tornando injeção impossível.
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>