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
    <link rel="icon" href="../assets/images/brasao.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/icons.css">
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
                        'green-forest': '#065f46',
                        'green-sage': '#6b7280',
                    }
                }
            }
        }
    </script>
    <title>Cadastro - Almoxarifado</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #d1fae5 0%, #10b981 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            transform: translateY(-2px);
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }
        
        .fade-in {
            animation: fadeIn 1s ease-out;
        }
        
        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }
        
        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .floating-shapes::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 10%;
            width: 120px;
            height: 120px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        .floating-shapes::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: 10%;
            width: 180px;
            height: 180px;
            background: rgba(16, 185, 129, 0.05);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        
        .enhanced-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(16, 185, 129, 0.2);
        }
        
        .enhanced-input:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: #10b981;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
        }
        
        .btn-enhanced {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-enhanced:hover::before {
            left: 100%;
        }
        
        .green-accent {
            color: #10b981;
        }
        
        .green-bg-light {
            background-color: #d1fae5;
        }
        
        .green-border {
            border-color: #10b981;
        }
        
        /* Enhanced responsive styles */
        @media (max-width: 640px) {
            .main-container {
                width: 95%;
                margin: 1rem auto;
            }
        }
        
        @media (max-width: 475px) {
            .main-container {
                width: 100%;
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-0 sm:p-4 floating-shapes">
    
    <div class="main-container w-full max-w-6xl bg-white rounded-none sm:rounded-3xl shadow-2xl overflow-hidden fade-in relative z-10">
        <div class="flex flex-col lg:flex-row min-h-[100vh] sm:min-h-[600px]">
            
            <!-- Criando seção lateral com informações da escola em verde -->
            <div class="hidden md:block lg:flex-1 bg-gradient-to-br from-green-primary to-green-secondary relative overflow-hidden slide-in-left">
                <div class="absolute inset-0 bg-black/20"></div>
                
                <!-- Elementos decorativos minimalistas -->
                <div class="absolute top-10 left-10 w-20 h-20 border-2 border-white/30 rounded-full animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-16 h-16 border-2 border-white/30 rounded-full" style="animation: float 6s ease-in-out infinite;"></div>
                <div class="absolute top-1/3 right-20 w-12 h-12 bg-white/20 rounded-full" style="animation: float 8s ease-in-out infinite reverse;"></div>
                
                <div class="relative z-10 h-full flex flex-col justify-center items-center p-8 lg:p-12 text-center text-white">
                    <div class="mb-8">
                        <svg class="icon text-6xl lg:text-8xl mb-6 text-green-light" viewBox="0 0 24 24">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    
                    <h1 class="text-3xl lg:text-5xl font-bold mb-6 leading-tight">
                     <span class="text-green-light">Secretaria Da Educação</span>
                    </h1>
                    
                    <p class="text-lg lg:text-xl mb-8 max-w-md leading-relaxed opacity-90">
                        Portal Educacional - Transformando o futuro através da educação e tecnologia
                    </p>
                    
                    <div class="flex space-x-6 text-sm opacity-90">
                        <div class="flex items-center">
                            <svg class="icon mr-2 text-green-light" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Comunidade</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="icon mr-2 text-green-light" viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
                            </svg>
                            <span>Excelência</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="icon mr-2 text-green-light" viewBox="0 0 24 24">
                                <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                                <path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                                <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/>
                                <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                            </svg>
                            <span>Inovação</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reformulando o formulário principal com layout similar ao login -->
            <div class="w-full lg:flex-1 p-4 sm:p-6 lg:p-8 flex flex-col justify-center slide-in-right relative">
                
                <!-- Header do formulário -->
                <div class="text-center mb-4 sm:mb-6">
                    <div class="inline-block p-3 sm:p-4 rounded-2xl mb-6">
                        <img src="../assets/images/brasao.png" 
                             alt="Logo EEEP Salaberga" 
                             class="w-12 h-12 sm:w-16 sm:h-16 object-contain">
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-forest mb-2">
                        Cadastro
                    </h2>
                    <p class="text-green-sage text-base sm:text-lg">
                        Crie sua conta para continuar
                    </p>
                </div>

                <!-- Formulário principal -->
                <form id="registerForm" action="../model/cadastrousuarios.php" method="POST" 
                      class="space-y-4 sm:space-y-6">
                    
                    <!-- Mensagens de erro/sucesso -->
                    <?php displaySessionMessages(); ?>
                    
                    <!-- Campo Nome -->
                    <div class="space-y-2">
                        <label for="nome" class="block text-sm font-semibold text-green-forest">
                            <svg class="icon mr-2 green-accent inline" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>Nome
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="nome" 
                                   name="nome" 
                                   placeholder="Seu nome completo" 
                                   required 
                                   autocomplete="off"
                                   class="enhanced-input w-full px-4 py-2 sm:py-3 pl-12 rounded-xl focus:outline-none input-focus transition-all duration-300 text-sm sm:text-base">
                            <svg class="icon absolute left-4 top-1/2 transform -translate-y-1/2 text-green-primary text-lg" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>

                    </div>

                    <!-- Campo Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-green-forest">
                            <svg class="icon mr-2 green-accent inline" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>Email
                        </label>
                        <div class="relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   placeholder="nome@gmail.com" 
                                   required 
                                   autocomplete="off"
                                   class="enhanced-input w-full px-4 py-2 sm:py-3 pl-12 rounded-xl focus:outline-none input-focus transition-all duration-300 text-sm sm:text-base">
                            <svg class="icon absolute left-4 top-1/2 transform -translate-y-1/2 text-green-primary text-lg" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Campo Senha -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-semibold text-green-forest">
                            <svg class="icon mr-2 green-accent inline" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <circle cx="12" cy="16" r="1"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>Senha
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Digite sua senha" 
                                   required 
                                   autocomplete="off"
                                   class="enhanced-input w-full px-4 py-2 sm:py-3 pl-12 pr-12 rounded-xl focus:outline-none input-focus transition-all duration-300 text-sm sm:text-base">
                            <svg class="icon absolute left-4 top-1/2 transform -translate-y-1/2 text-green-primary text-lg" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <circle cx="12" cy="16" r="1"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <button type="button" 
                                    id="mostrarSenhaBtn" 
                                    onclick="mostrarSenha()"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-green-sage hover:text-green-primary transition-colors duration-200">
                                <i class="bi-eye-fill text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    
                    
                    <!-- Botão de Submit -->
                    <button type="submit" 
                            id="btn"
                            class="btn-enhanced btn-hover w-full text-white font-semibold py-2 sm:py-3 px-6 rounded-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-primary/30 text-sm sm:text-base">
                        <svg class="icon mr-2 inline" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/>
                            <line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                        <span>Cadastrar</span>
                    </button>
                    
                    <!-- Link de Login -->
                    <div class="text-center pt-4 border-t border-green-primary/20">
                        <p class="text-green-sage text-sm sm:text-base">
                            Já tem uma conta? 
                            <a href="index.php" 
                               class="text-green-primary hover:text-green-secondary font-semibold transition-colors duration-200">
                                Entrar
                            </a>
                        </p>
                    </div>
                </form>
                
                <!-- Links adicionais -->
                <div class="mt-6 sm:mt-8 text-center space-y-3 sm:space-y-4">
                    <div class="pt-3 sm:pt-4 border-t border-green-primary/20">
                        <p class="text-green-sage text-xs sm:text-sm">
                            Precisa de ajuda? 
                            <a href="mailto:suporte@eeepsalaberga.edu.br" 
                               class="text-green-primary hover:text-green-secondary transition-colors duration-300">
                                Contate o suporte
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/mostrarSenha.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const submitButton = form.querySelector('button[type="submit"]');
            const inputs = document.querySelectorAll('input');

            // Enhanced form submission with loading state
            form.addEventListener('submit', function(e) {
                submitButton.classList.add('loading');
                submitButton.disabled = true;
                
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<svg class="icon mr-2 inline animate-spin" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>Cadastrando...';
                
                setTimeout(() => {
                    if (submitButton.disabled) {
                        submitButton.classList.remove('loading');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }, 5000);
            });

            // Enhanced input animations
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Auto-focus first input
            const nomeInput = document.getElementById('nome');
            if (nomeInput) nomeInput.focus();
        });
    </script>
</body>
</html>
