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
    <title>Cadastro</title>

    <style>
        /* Adicionando configuração do Tailwind e estilos customizados */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #d1fae5 0%, #10b981 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-shapes::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 10%;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #10b981, #059669);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-shapes::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: 10%;
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, #34d399, #10b981);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            opacity: 0.1;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
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

<body class="min-h-screen flex items-center justify-center p-4 floating-shapes" style="opacity:0; transition: opacity 1s;" onload="document.body.style.opacity='1'">
   
    <!-- Reformulando o formulário com design moderno e Tailwind -->
    <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl">
        <form action="../model/cadastrousuarios.php" method="POST" class="space-y-6">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Cadastro</h2>
                <p class="text-gray-600">Crie sua conta para continuar</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        placeholder="Digite seu nome completo" 
                        required
                        class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent input-focus transition-all duration-300"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="nome@exemplo.com" 
                        required
                        class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent input-focus transition-all duration-300"
                    >
                </div>

                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Digite sua senha" 
                        required
                        class="w-full px-4 py-3 pr-12 bg-white/70 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent input-focus transition-all duration-300"
                    >
                    <button 
                        type="button"
                        id="mostrarSenhaBtn" 
                        onclick="mostrarSenha()"
                        class="absolute right-4 top-[38px] text-gray-500 hover:text-emerald-600 transition-colors duration-200"
                    >
                        <i class="bi-eye-fill text-lg"></i>
                    </button>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-emerald-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-300 btn-hover"
            >
                Cadastrar
            </button>

            <p class="text-center text-gray-600 mt-6">
                Já tem uma conta? 
                <a href="index.php" class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors duration-200">
                    Entrar
                </a>
            </p>
        </form>
    </div>

    <script src="../js/mostrarSenha.js"></script> 
</body>
</html>
