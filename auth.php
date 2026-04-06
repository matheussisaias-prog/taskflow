<?php
session_start();
require_once '../includes/db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'logout') {
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

if ($action === 'login') {
    header('Content-Type: application/json');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $users = db_where('users', ['email' => $email, 'ativo' => 1]);
    if ($users && password_verify($senha, $users[0]['senha'])) {
        $_SESSION['user_id']     = $users[0]['id'];
        $_SESSION['user_nome']   = $users[0]['nome'];
        $_SESSION['user_email']  = $users[0]['email'];
        $_SESSION['user_perfil'] = $users[0]['perfil'];
        echo json_encode(['success' => true]); exit;
    }
    echo json_encode(['error' => 'Credenciais inválidas']); exit;
}

header('Location: ' . BASE_URL . '/index.php');
