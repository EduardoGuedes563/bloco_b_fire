<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../includes/db.php';
require_once '../includes/functions.php';

// Autenticação por token no header
// Authorization: Bearer SEU_TOKEN_AQUI
function autenticar(): void {
    $headers = getallheaders();
    $token   = $headers['Authorization'] ?? '';

    if ($token !== 'Bearer bloco_b_fire_token_2024') {
        http_response_code(401);
        echo json_encode(['erro' => 'Token inválido ou ausente.']);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// =====================
// GET — Listar / Buscar
// =====================
if ($method === 'GET') {
    atualizarStatusExtintores($pdo);

    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM extintores WHERE id = :id AND ativo = 1");
        $stmt->execute([':id' => $id]);
        $extintor = $stmt->fetch();

        if (!$extintor) {
            http_response_code(404);
            echo json_encode(['erro' => 'Extintor não encontrado.']);
            exit;
        }
        echo json_encode($extintor);
        exit;
    }

    // Filtros opcionais via query string
    $where  = ['ativo = 1'];
    $params = [];

    if (!empty($_GET['status'])) {
        $where[]           = 'status = :status';
        $params[':status'] = $_GET['status'];
    }
    if (!empty($_GET['pavimento'])) {
        $where[]              = 'pavimento = :pavimento';
        $params[':pavimento'] = $_GET['pavimento'];
    }

    $sql  = "SELECT * FROM extintores WHERE " . implode(' AND ', $where) . " ORDER BY pavimento, codigo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $extintores = $stmt->fetchAll();

    echo json_encode([
        'total'      => count($extintores),
        'extintores' => $extintores
    ]);
    exit;
}

// =====================
// POST — Criar
// =====================
if ($method === 'POST') {
    autenticar();

    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['codigo']) || empty($dados['tipo']) || empty($dados['localizacao']) || empty($dados['proxima_inspecao'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Campos obrigatórios: codigo, tipo, localizacao, proxima_inspecao']);
        exit;
    }

    $dados['status'] = calcularStatus($dados['proxima_inspecao']);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO extintores (codigo, tipo, capacidade, fabricante, numero_serie,
                                    data_instalacao, localizacao, pavimento, proxima_inspecao, status)
            VALUES (:codigo, :tipo, :capacidade, :fabricante, :numero_serie,
                    :data_instalacao, :localizacao, :pavimento, :proxima_inspecao, :status)
        ");
        $stmt->execute([
            ':codigo'          => strtoupper($dados['codigo']),
            ':tipo'            => $dados['tipo'],
            ':capacidade'      => $dados['capacidade']      ?? null,
            ':fabricante'      => $dados['fabricante']      ?? null,
            ':numero_serie'    => $dados['numero_serie']    ?? null,
            ':data_instalacao' => $dados['data_instalacao'] ?? null,
            ':localizacao'     => $dados['localizacao'],
            ':pavimento'       => $dados['pavimento']       ?? 'Térreo',
            ':proxima_inspecao'=> $dados['proxima_inspecao'],
            ':status'          => $dados['status'],
        ]);

        http_response_code(201);
        echo json_encode([
            'mensagem' => 'Extintor criado com sucesso.',
            'id'       => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode(['erro' => 'Código já existe ou erro no banco.']);
    }
    exit;
}

// =====================
// PUT — Atualizar
// =====================
if ($method === 'PUT') {
    autenticar();

    if ($id === 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'Informe o id na URL. Ex: ?id=1']);
        exit;
    }

    $dados = json_decode(file_get_contents('php://input'), true);

    if (!empty($dados['proxima_inspecao'])) {
        $dados['status'] = calcularStatus($dados['proxima_inspecao']);
    }

    $campos  = [];
    $params  = [':id' => $id];
    $allowed = ['tipo','capacidade','fabricante','numero_serie','data_instalacao',
                'localizacao','pavimento','proxima_inspecao','status'];

    foreach ($allowed as $campo) {
        if (isset($dados[$campo])) {
            $campos[]         = "$campo = :$campo";
            $params[":$campo"] = $dados[$campo];
        }
    }

    if (empty($campos)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nenhum campo válido para atualizar.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE extintores SET " . implode(', ', $campos) . " WHERE id = :id AND ativo = 1");
    $stmt->execute($params);

    echo json_encode(['mensagem' => 'Extintor atualizado com sucesso.']);
    exit;
}

// =====================
// DELETE — Remover
// =====================
if ($method === 'DELETE') {
    autenticar();

    if ($id === 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'Informe o id na URL. Ex: ?id=1']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE extintores SET ativo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['mensagem' => 'Extintor removido com sucesso.']);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);