<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

exigirLogin('usuario');
atualizarStatusExtintores($pdo);

// Totais
$totais = $pdo->query("
    SELECT
        COUNT(*)                   AS total,
        SUM(status = 'normal')     AS normais,
        SUM(status = 'a_vencer')   AS a_vencer,
        SUM(status = 'vencido')    AS vencidos
    FROM extintores WHERE ativo = 1
")->fetch();

// Próximas inspeções
$proximas = $pdo->query("
    SELECT codigo, localizacao, pavimento, proxima_inspecao, status
    FROM extintores
    WHERE ativo = 1 AND proxima_inspecao >= CURDATE()
    ORDER BY proxima_inspecao ASC
    LIMIT 8
")->fetchAll();

// Por pavimento
$porPavimento = $pdo->query("
    SELECT pavimento, COUNT(*) AS qtd
    FROM extintores WHERE ativo = 1
    GROUP BY pavimento ORDER BY pavimento
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Sistema de Incêndio Bloco B</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
</head>
<body>
<div class="wrapper">

    <?php require_once '../includes/sidebar_usuario.php'; ?>

    <main class="conteudo">

        <div style="margin-bottom:24px">
            <h2 style="font-size:22px;font-weight:700;margin:0">Dashboard</h2>
            <p style="color:#888;font-size:13px;margin:4px 0 0">
                Bloco B · <?= date('d/m/Y') ?> · Modo visualização
            </p>
        </div>

        <!-- Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px">

            <div class="stat-card">
                <div class="stat-icon bg-azul">🧯</div>
                <div>
                    <div class="stat-num"><?= $totais['total'] ?></div>
                    <div class="stat-label">Total de extintores</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-verde">✅</div>
                <div>
                    <div class="stat-num" style="color:#1e8449"><?= $totais['normais'] ?></div>
                    <div class="stat-label">Em dia</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-amarelo">⚠️</div>
                <div>
                    <div class="stat-num" style="color:#b7950b"><?= $totais['a_vencer'] ?></div>
                    <div class="stat-label">A vencer (30 dias)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-vermelho">❌</div>
                <div>
                    <div class="stat-num" style="color:#c0392b"><?= $totais['vencidos'] ?></div>
                    <div class="stat-label">Vencidos</div>
                </div>
            </div>

        </div>

        <!-- Grid -->
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">

            <div class="tabela-card">
                <div class="tabela-header">
                    <h5>Próximas inspeções</h5>
                    <a href="/bloco_b_fire/usuario/extintores.php" class="btn-outline">Ver todos</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Localização</th>
                            <th>Pavimento</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proximas as $e): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($e['codigo'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= htmlspecialchars($e['localizacao'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($e['pavimento'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= date('d/m/Y', strtotime($e['proxima_inspecao'])) ?></td>
                            <td><span class="<?= badgeStatus($e['status']) ?>"><?= textoStatus($e['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tabela-card">
                <div class="tabela-header">
                    <h5>Por pavimento</h5>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Pavimento</th>
                            <th>Qtd.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($porPavimento as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['pavimento'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><strong><?= $p['qtd'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>