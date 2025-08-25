<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = md5($_POST['senha'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
    $stmt->execute([$email, $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($admin) {
$_SESSION['usuario_id'] = $admin['id'];
$_SESSION['nome'] = $admin['nome'];
$_SESSION['email'] = $admin['email'];
$_SESSION['senha'] = $admin['senha'];
$_SESSION['tipo'] = $admin['tipo'];
 header('Location: painel/' . $admin['tipo'] . '/dashboard_Admin.php');
        exit;
    } else {
     $_SESSION['erro_login'] = "Email ou senha inválidos.";
        header('Location: index.php');
        exit;
    }

if ($usuario) {
$_SESSION['usuario_id'] = $usuario['id']; 
$_SESSION['nome'] = $usuario['nome'];
$_SESSION['email'] = $usuario['email'];
$_SESSION['senha'] = $usuario['senha'];
$_SESSION['tipo'] = $usuario['tipo'];
    header('Location: painel/' . $usuario['tipo'] . '/dashboard_Usuario.php');
    exit;
    } else {
$_SESSION['erro_login'] = "Email ou senha inválidos.";
    header('Location: index.php');
    exit;
}
 ?>
