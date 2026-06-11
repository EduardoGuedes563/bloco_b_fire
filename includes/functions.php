<?php

/**
 * Gera um código OTP de 6 dígitos para o 2FA
 */
function gerarCodigo2FA(): string {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Sanitiza saída para evitar XSS
 */
function h(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

/**
 * Redireciona para uma URL
 */
function redirecionar(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Verifica se o status do extintor precisa ser atualizado
 * baseado na data de próxima inspeção
 */
function calcularStatus(string $dataInspecao): string {
    $hoje    = new DateTime();
    $inspec  = new DateTime($dataInspecao);
    $diff    = $hoje->diff($inspec);

    if ($inspec < $hoje) {
        return 'vencido';
    }
    // Se vence em até 30 dias
    if ($diff->days <= 30 && $inspec >= $hoje) {
        return 'a_vencer';
    }
    return 'normal';
}

/**
 * Atualiza automaticamente o status de todos os extintores
 */
function atualizarStatusExtintores(PDO $pdo): void {
    $stmt = $pdo->query("SELECT id, proxima_inspecao FROM extintores WHERE ativo = 1");
    $extintores = $stmt->fetchAll();

    $update = $pdo->prepare("UPDATE extintores SET status = :status WHERE id = :id");

    foreach ($extintores as $e) {
        $novoStatus = calcularStatus($e['proxima_inspecao']);
        $update->execute([
            ':status' => $novoStatus,
            ':id'     => $e['id']
        ]);
    }
}

/**
 * Retorna a classe CSS do badge de status
 */
function badgeStatus(string $status): string {
    return match($status) {
        'normal'   => 'badge-normal',
        'a_vencer' => 'badge-avencer',
        'vencido'  => 'badge-vencido',
        default    => 'badge-normal'
    };
}

/**
 * Retorna o texto legível do status
 */
function textoStatus(string $status): string {
    return match($status) {
        'normal'   => 'Normal',
        'a_vencer' => 'A vencer',
        'vencido'  => 'Vencido',
        default    => 'Normal'
    };
}

/**
 * Gera e valida tokens CSRF
 */
function gerarCsrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarCsrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}