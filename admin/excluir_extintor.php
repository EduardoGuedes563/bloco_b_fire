<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

exigirLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validarCsrf($_POST['csrf_token'] ?? '')) {
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        // Soft delete — apenas marca como inativo, não apaga do banco
        $stmt = $pdo->prepare("UPDATE extintores SET ativo = 0 WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}

header('Location: /bloco_b_fire/admin/extintores.php?msg=excluido');
exit;