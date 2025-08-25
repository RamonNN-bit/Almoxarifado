<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email'] ?? '');
    $senhaDigitada = $_POST['password'] ?? '';

  
    if (empty($email) || empty($senhaDigitada)) {
        $_SESSION['erro_login'] = "Preencha todos os campos.";
        header('Location: ../view/index.php');
        exit;
    }

    
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, tipo FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  
    if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {
      
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['tipo'] = $usuario['tipo'];

       
        if ($usuario['tipo'] === 'admin') {
            header('Location: ../painel/admin/dashboard_Admin.php');
        } else {
            header('Location: ../painel/usuario/dashboard_Usuario.php');
        }
        exit;
    } else {
        $_SESSION['erro_login'] = "Email ou senha inválidos.";
        header('Location: ../view/index.php');
        exit;
    }
}
?>
