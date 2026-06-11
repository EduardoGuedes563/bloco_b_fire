<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

exigirLogin('usuario');
atualizarStatusExtintores($pdo);

$filtroStatus    = $_GET['status']    ?? '';
$filtroPavimento = $_GET['pavimento'] ?? '';
$filtroBusca     = trim($_GET['busca'] ?? '');

$params = [];
$where  = ['ativo = 1'];

if ($filtroStatus) {
    $where[]           = 'status = :status';
    $params[':status'] = $filtroStatus;
}
if ($filtroPavimento) {
    $where[]              = 'pavimento = :pavimento';
    $params[':pavimento'] = $filtroPavimento;
}
if ($filtroBusca) {
    $where[]          = '(codigo LIKE :busca OR localizacao LIKE :busca)';
    $params[':busca'] = "%$filtroBusca%";
}

$sql  = "SELECT * FROM extintores WHERE " . implode(' AND ', $where) . " ORDER BY pavimento, codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$extintores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extintores — Sistema de Incêndio Bloco B</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
</head>
<body>
<div class="wrapper">

    <?php require_once '../includes/sidebar_usuario.php'; ?>

    <main class="conteudo">

        <div style="margin-bottom:24px">
            <h2 style="font-size:22px;font-weight:700;margin:0">
                Extintores
                <span style="font-size:14px;font-weight:400;color:#888;margin-left:8px">
                    <?= count($extintores) ?> registros
                </span>
            </h2>
            <p style="color:#888;font-size:13px;margin:4px 0 0">Modo visualização — somente leitura</p>
        </div>

        <!-- Filtros -->
        <div class="tabela-card" style="margin-bottom:20px">
            <form method="GET" style="display:flex;gap:12px;padding:16px 20px;flex-wrap:wrap;align-items:flex-end">
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#666">Buscar</label>
                    <input type="text" name="busca"
                           value="<?= htmlspecialchars($filtroBusca, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Código ou localização..."
                           style="border:1.5px solid #ddd;border-radius:8px;padding:8px 12px;font-size:13px;width:220px">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#666">Status</label>
                    <select name="status" style="border:1.5px solid #ddd;border-radius:8px;padding:8px 12px;font-size:13px">
                        <option value="">Todos</option>
                        <option value="normal"   <?= $filtroStatus==='normal'   ? 'selected':'' ?>>Normal</option>
                        <option value="a_vencer" <?= $filtroStatus==='a_vencer' ? 'selected':'' ?>>A vencer</option>
                        <option value="vencido"  <?= $filtroStatus==='vencido'  ? 'selected':'' ?>>Vencido</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#666">Pavimento</label>
                    <select name="pavimento" style="border:1.5px solid #ddd;border-radius:8px;padding:8px 12px;font-size:13px">
                        <option value="">Todos</option>
                        <option value="Térreo"   <?= $filtroPavimento==='Térreo'   ? 'selected':'' ?>>Térreo</option>
                        <option value="1º Andar" <?= $filtroPavimento==='1º Andar' ? 'selected':'' ?>>1º Andar</option>
                        <option value="2º Andar" <?= $filtroPavimento==='2º Andar' ? 'selected':'' ?>>2º Andar</option>
                    </select>
                </div>
                <button type="submit" class="btn-vermelho">Filtrar</button>
                <a href="/bloco_b_fire/usuario/extintores.php" class="btn-outline">Limpar</a>
            </form>
        </div>

        <!-- Tabela -->
        <div class="tabela-card">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Capacidade</th>
                        <th>Localização</th>
                        <th>Pavimento</th>
                        <th>Próx. Inspeção</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($extintores as $e): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($e['codigo'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td><?= htmlspecialchars($e['tipo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['capacidade'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['localizacao'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['pavimento'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= date('d/m/Y', strtotime($e['proxima_inspecao'])) ?></td>
                        <td><span class="<?= badgeStatus($e['status']) ?>"><?= textoStatus($e['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($extintores)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#888">
                            Nenhum extintor encontrado.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>