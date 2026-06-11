<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

exigirLogin('admin');

$id        = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$extintor  = null;
$erro      = '';
$editando  = false;

// Se tem ID, carrega os dados para edição
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM extintores WHERE id = :id AND ativo = 1 LIMIT 1");
    $stmt->execute([':id' => $id]);
    $extintor = $stmt->fetch();

    if (!$extintor) {
        header('Location: /bloco_b_fire/admin/extintores.php');
        exit;
    }
    $editando = true;
}

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarCsrf($_POST['csrf_token'] ?? '')) {
        $erro = 'Requisição inválida.';
    } else {
        $dados = [
            'codigo'           => strtoupper(trim($_POST['codigo'] ?? '')),
            'tipo'             => trim($_POST['tipo'] ?? ''),
            'capacidade'       => trim($_POST['capacidade'] ?? ''),
            'fabricante'       => trim($_POST['fabricante'] ?? ''),
            'numero_serie'     => trim($_POST['numero_serie'] ?? ''),
            'data_instalacao'  => $_POST['data_instalacao'] ?: null,
            'localizacao'      => trim($_POST['localizacao'] ?? ''),
            'pavimento'        => trim($_POST['pavimento'] ?? ''),
            'proxima_inspecao' => $_POST['proxima_inspecao'] ?? '',
        ];

        // Validação básica
        if (empty($dados['codigo']) || empty($dados['tipo']) || empty($dados['localizacao']) || empty($dados['proxima_inspecao'])) {
            $erro = 'Preencha todos os campos obrigatórios (*)';
        } else {
            $dados['status'] = calcularStatus($dados['proxima_inspecao']);

            if ($editando) {
                $sql = "UPDATE extintores SET
                            codigo = :codigo, tipo = :tipo, capacidade = :capacidade,
                            fabricante = :fabricante, numero_serie = :numero_serie,
                            data_instalacao = :data_instalacao, localizacao = :localizacao,
                            pavimento = :pavimento, proxima_inspecao = :proxima_inspecao,
                            status = :status
                        WHERE id = :id";
                $dados[':id'] = $id;
            } else {
                $sql = "INSERT INTO extintores
                            (codigo, tipo, capacidade, fabricante, numero_serie,
                             data_instalacao, localizacao, pavimento, proxima_inspecao, status)
                        VALUES
                            (:codigo, :tipo, :capacidade, :fabricante, :numero_serie,
                             :data_instalacao, :localizacao, :pavimento, :proxima_inspecao, :status)";
            }

            try {
                $stmt = $pdo->prepare($sql);
                // Adiciona os dois pontos nas chaves para o execute
                $bindDados = [];
                foreach ($dados as $k => $v) {
                    $bindDados[strpos($k, ':') === 0 ? $k : ":$k"] = $v;
                }
                $stmt->execute($bindDados);
                header('Location: /bloco_b_fire/admin/extintores.php?msg=salvo');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $erro = 'Já existe um extintor com esse código. Use outro.';
                } else {
                    $erro = 'Erro ao salvar. Tente novamente.';
                    error_log($e->getMessage());
                }
            }
        }
    }
}

$csrf = gerarCsrf();
// Usa dados do POST em caso de erro, senão usa os dados carregados do banco
$v = ($erro && $_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : ($extintor ?? []);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editando ? 'Editar' : 'Novo' ?> Extintor — Admin</title>
    <link rel="stylesheet" href="/bloco_b_fire/assets/css/style.css">
</head>
<body>
<div class="wrapper">

    <?php require_once '../includes/sidebar_admin.php'; ?>

    <main class="conteudo">
        <div style="max-width:680px">

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
                <a href="/bloco_b_fire/admin/extintores.php" class="btn-outline" style="padding:6px 12px">← Voltar</a>
                <h2 style="font-size:22px;font-weight:700;margin:0">
                    <?= $editando ? 'Editar extintor' : 'Novo extintor' ?>
                </h2>
            </div>

            <?php if ($erro): ?>
                <div class="alerta-erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="tabela-card" style="padding:28px">
                <form method="POST" action="" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Código *
                            </label>
                            <input type="text" name="codigo" class="form-control"
                                   value="<?= htmlspecialchars($v['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="EXT-B-28" style="text-transform:uppercase"
                                   <?= $editando ? 'readonly' : '' ?>>
                            <?php if ($editando): ?>
                                <small style="color:#888;font-size:11px">O código não pode ser alterado.</small>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Tipo *
                            </label>
                            <select name="tipo" class="form-control">
                                <option value="">Selecione...</option>
                                <?php foreach (['PQS','CO2','ÁGUA','ESPUMA'] as $t): ?>
                                    <option value="<?= $t ?>" <?= ($v['tipo'] ?? '') === $t ? 'selected' : '' ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Capacidade
                            </label>
                            <input type="text" name="capacidade" class="form-control"
                                   value="<?= htmlspecialchars($v['capacidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="6kg / 4kg / 10L">
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Fabricante
                            </label>
                            <input type="text" name="fabricante" class="form-control"
                                   value="<?= htmlspecialchars($v['fabricante'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="Kidde, Amerex...">
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Número de série
                            </label>
                            <input type="text" name="numero_serie" class="form-control"
                                   value="<?= htmlspecialchars($v['numero_serie'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="SN-028">
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Data de instalação
                            </label>
                            <input type="date" name="data_instalacao" class="form-control"
                                   value="<?= htmlspecialchars($v['data_instalacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div style="grid-column:1/-1">
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Localização *
                            </label>
                            <input type="text" name="localizacao" class="form-control"
                                   value="<?= htmlspecialchars($v['localizacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="Ex: Corredor principal – lado esquerdo">
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Pavimento *
                            </label>
                            <select name="pavimento" class="form-control">
                                <?php foreach (['Térreo','1º Andar','2º Andar'] as $p): ?>
                                    <option value="<?= $p ?>" <?= ($v['pavimento'] ?? 'Térreo') === $p ? 'selected' : '' ?>>
                                        <?= $p ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:5px">
                                Próxima inspeção *
                            </label>
                            <input type="date" name="proxima_inspecao" class="form-control"
                                   value="<?= htmlspecialchars($v['proxima_inspecao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                    </div>

                    <div style="margin-top:28px;display:flex;gap:12px">
                        <button type="submit" class="btn-vermelho" style="padding:11px 28px;font-size:14px">
                            <?= $editando ? '💾 Salvar alterações' : '➕ Cadastrar extintor' ?>
                        </button>
                        <a href="/bloco_b_fire/admin/extintores.php" class="btn-outline" style="padding:11px 20px">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </main>
</div>
<script src="/bloco_b_fire/assets/js/main.js"></script>
</body>
</html>