<?php
require_once '../config/auth.php';

// Se já estiver logado, redirecionar para o dashboard apropriado
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: painel/Admin/dashboard_Admin.php');
    } elseif (isUser()) {
        header('Location: painel/Usuario/dashboard_usuario.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Recuperar Senha</title>
    
    <style>
        /* Adicionando configuração do Tailwind e estilos customizados */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-shapes::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 15%;
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #10b981, #059669);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 7s ease-in-out infinite;
        }
        
        .floating-shapes::after {
            content: '';
            position: absolute;
            bottom: 20%;
            right: 15%;
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #34d399, #10b981);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            opacity: 0.1;
            animation: float 9s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(180deg); }
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 flex items-center justify-center p-4 floating-shapes">

    <!-- Reformulando o formulário com design moderno e Tailwind -->
    <div class="glass-effect rounded-3xl p-8 w-full max-w-md shadow-2xl">
        <form action="senha.php" method="POST" class="space-y-6">
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-r from-emerald-500 to-green-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586l4.293-4.293A6 6 0 0119 9z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Recuperar Senha</h2>
                <p class="text-gray-600">Digite seu email para receber as instruções</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Digite seu email para recuperar a senha" 
                    required
                    class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent input-focus transition-all duration-300"
                >
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-emerald-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-300 btn-hover"
            >
                Recuperar Senha
            </button>

            <p class="text-center text-gray-600 mt-6">
                Lembrou da senha? 
                <a href="index.php" class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors duration-200">
                    Voltar ao login
                </a>
            </p>
        </form>
    </div>

    <?php
    /* Mantendo toda a lógica PHP original */
    require '../config/db.php';

    isset($_POST['email']) ? $email = $_POST['email'] : $email = '';

    if (empty($email)) {
        function isValidEmail($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        alert("<p>Por favor, insira um email válido.</p>");
        }
        exit;
    }
    $stmt = $pdo->prepare("UPDATE `usuario` SET `senha`='' WHERE 'id';");
    $stmt->execute();

    echo "<p>Senha redefinida com sucesso. Verifique seu email para a nova senha.</p>";
    ?>
</body>
</html>
