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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-primary': '#10b981',
                        'green-secondary': '#059669',
                        'green-light': '#d1fae5',
                        'green-dark': '#047857',
                    }
                }
            }
        }
    </script>
    <title>Login</title>
    <style>
        body {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            border-color: #10b981;
        }
        
        .btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .floating-shapes::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 10%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-shapes::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: 10%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 floating-shapes">
    <!-- Login Form -->
    <div class="fade-in w-full max-w-md">
        <form id="loginForm" action="../config/rota.php" method="POST" 
              class="glass-effect rounded-2xl shadow-2xl p-8 space-y-6">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Bem-vindo</h2>
                <p class="text-gray-600">Faça login em sua conta</p>
            </div>
            
            <!-- Mensagens de erro/sucesso -->
            <?php displaySessionMessages(); ?>
            
            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-gray-700">
                    Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       placeholder="nome@gmail.com" 
                       required 
                       autocomplete="off"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none input-focus transition-all duration-300 bg-white/80">
            </div>
            
            <!-- Password Field -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Senha
                </label>
                <div class="relative">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Digite sua senha" 
                           required 
                           autocomplete="off"
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:outline-none input-focus transition-all duration-300 bg-white/80">
                    <button type="button" 
                            id="mostrarSenhaBtn" 
                            onclick="mostrarSenha()"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-primary transition-colors duration-200">
                        <i class="bi-eye-fill text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Options -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2 text-gray-600">
                    <input type="checkbox" 
                           name="rememberMe" 
                           id="remember"
                           class="w-4 h-4 text-green-primary border-gray-300 rounded focus:ring-green-primary focus:ring-2">
                    <span>Lembrar-me</span>
                </label>
                <a href="senha.php" 
                   class="text-green-primary hover:text-green-secondary font-medium transition-colors duration-200">
                    Esqueci a senha
                </a>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                    id="btn"
                    class="w-full bg-green-primary hover:bg-green-secondary text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 btn-hover focus:outline-none focus:ring-4 focus:ring-green-primary/30">
                Entrar
            </button>
            
            <!-- Register Link -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-gray-600">
                    Não tem uma conta? 
                    <a href="cadastro.php" 
                       class="text-green-primary hover:text-green-secondary font-semibold transition-colors duration-200">
                        Cadastre-se
                    </a>
                </p>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-sm border-t border-gray-200 py-4">
        <div class="flex justify-center">
            <img src="../assets/images/logo.png" 
                 alt="Logo" 
                 class="h-12 object-contain opacity-80">
        </div>
    </footer>

    <!-- Scripts -->
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/mostrarSenha.js"></script>
</body>
</html>
