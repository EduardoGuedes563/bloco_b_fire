<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

exigirLogin('admin');
atualizarStatusExtintores($pdo);

// Filtros via GET
$filtroStatus    = $_GET['status']    ?? '';
$filtroPavimento = $_GET['pavimento'] ?? '';
$filtroBusca     = trim($_GET['busca'] ?? '');

$params = [];
$where  = ['ativo = 1'];

if ($filtroStatus) {
    $where[]          = 'status = :status';
    $params[':status'] = $filtroStatus;
}
if ($filtroPavimento) {
    $where[]             = 'pavimento = :pavimento';
    $params[':pavimento'] = $filtroPavimento;
}
if ($filtroBusca) {
    $where[]              = '(codigo LIKE :busca1 OR localizacao LIKE :busca2)';
    $params[':busca1']    = "%$filtroBusca%";
    $params[':busca2']    = "%$filtroBusca%";
}

$sql = "SELECT * FROM extintores WHERE " . implode(' AND ', $where) . " ORDER BY pavimento, codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$extintores = $stmt->fetchAll();

// Mensagem de feedback (após editar/excluir)
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extintores — Admin</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
</head>
<body>
<div class="wrapper">

    <?php require_once '../includes/sidebar_admin.php'; ?>

    <main class="conteudo">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
            <h2 style="font-size:22px;font-weight:700;margin:0">
                Extintores
                <span style="font-size:14px;font-weight:400;color:#888;margin-left:8px">
                    <?= count($extintores) ?> registros
                </span>
            </h2>
            <a href="/bloco_b_fire/admin/extintor_form.php" class="btn-vermelho">+ Novo extintor</a>
        </div>

        <?php if ($msg === 'salvo'): ?>
            <div style="background:#d5f5e3;border-left:4px solid #1e8449;color:#1e8449;padding:10px 16px;border-radius:8px;margin-bottom:20px;font-size:13px">
                Extintor salvo com sucesso!
            </div>
        <?php elseif ($msg === 'excluido'): ?>
            <div style="background:#fadbd8;border-left:4px solid #c0392b;color:#c0392b;padding:10px 16px;border-radius:8px;margin-bottom:20px;font-size:13px">
                Extintor removido com sucesso.
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="tabela-card" style="margin-bottom:20px">
            <form method="GET" style="display:flex;gap:12px;padding:16px 20px;flex-wrap:wrap;align-items:flex-end">
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#666">Buscar</label>
                    <input
                        type="text"
                        name="busca"
                        value="<?= htmlspecialchars($filtroBusca, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Código ou localização..."
                        style="border:1.5px solid #ddd;border-radius:8px;padding:8px 12px;font-size:13px;width:220px"
                    >
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
                <a href="/bloco_b_fire/admin/extintores.php" class="btn-outline">Limpar</a>
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
                        <th>Ações</th>
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
                        <td style="white-space:nowrap">
                            <a href="/bloco_b_fire/admin/extintor_form.php?id=<?= $e['id'] ?>"
                               class="btn-outline" style="padding:5px 12px;font-size:12px">
                                ✏️ Editar
                            </a>
                            <button
                                onclick="confirmarExclusao(<?= $e['id'] ?>, '<?= htmlspecialchars($e['codigo'], ENT_QUOTES, 'UTF-8') ?>')"
                                class="btn-vermelho" style="padding:5px 12px;font-size:12px;margin-left:4px">
                                🗑️ Excluir
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($extintores)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#888">
                            Nenhum extintor encontrado.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<!-- Modal de confirmação de exclusão -->
<div id="modal-excluir" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:32px;max-width:380px;width:90%;text-align:center">
        <div style="font-size:40px;margin-bottom:12px">⚠️</div>
        <h3 style="margin-bottom:8px">Excluir extintor?</h3>
        <p id="modal-texto" style="color:#666;font-size:14px;margin-bottom:24px"></p>
        <div style="display:flex;gap:12px;justify-content:center">
            <button onclick="fecharModal()" class="btn-outline">Cancelar</button>
            <form id="form-excluir" method="POST" action="/bloco_b_fire/admin/excluir_extintor.php" style="margin:0">
                <input type="hidden" name="id" id="excluir-id">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gerarCsrf(), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn-vermelho">Sim, excluir</button>
            </form>
        </div>
    </div>
</div>

<script src="/bloco_b_fire/assets/js/main.js"></script>
<script>
function confirmarExclusao(id, codigo) {
    document.getElementById('excluir-id').value  = id;
    document.getElementById('modal-texto').textContent = 'O extintor ' + codigo + ' será removido permanentemente.';
    document.getElementById('modal-excluir').style.display = 'flex';
}
function fecharModal() {
    document.getElementById('modal-excluir').style.display = 'none';
}
</script>
</body>
</html>