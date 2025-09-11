<?php
require_once '../config/db.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email'] ?? '');
    $senhaDigitada = $_POST['password'] ?? '';

    // Validar campos obrigatórios
    if (empty($email) || empty($senhaDigitada)) {
        $_SESSION['erro_login'] = "Preencha todos os campos.";
        header('Location: ../view/index.php');
        exit;
    }

    try {
        // Buscar usuário no banco de dados
        $stmt = $pdo->prepare("SELECT id, nome, email, senha, TIPO FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar credenciais
        if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {
            
            // Definir sessão baseada no tipo de usuário
            if ($usuario['TIPO'] === 'admin') {
                setAdminSession($usuario);
                $_SESSION['email'] = $usuario['email'];
                header('Location: ../view/painel/Admin/dashboard_Admin.php');
            } else {
                setUserSession($usuario);
                $_SESSION['email'] = $usuario['email'];
                header('Location: ../view/painel/Usuario/dashboard_usuario.php');
            }
            exit;
        } else {
            $_SESSION['erro_login'] = "Email ou senha inválidos.";
            header('Location: ../view/index.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['erro_login'] = "Erro no sistema. Tente novamente.";
        header('Location: ../view/index.php');
        exit;
    }
} 
?>
