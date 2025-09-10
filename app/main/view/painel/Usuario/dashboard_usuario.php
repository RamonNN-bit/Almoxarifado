<?php
session_start();
if (!isset($_SESSION ["usuariologado"])) {
header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuário - Almoxarifado</title>
    <!-- Substituindo Bootstrap por Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-primary': '#10b981',
                        'green-secondary': '#059669',
                        'green-light': '#d1fae5',
                        'green-dark': '#047857',
                        'emerald-gradient': '#10b981',
                        'teal-gradient': '#0d9488'
                    }
                }
            }
        }
    </script>
    <style>
        /* Estilos customizados para gradientes verdes e animações */
        .gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .gradient-green-light {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }
        
        .gradient-emerald {
            background: linear-gradient(135deg, #10b981 0%, #0d9488 100%);
        }
        
        .gradient-teal {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
        }
        
        .card-shadow {
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.08);
        }
        
        .notification-border-green {
            border-left: 4px solid #10b981;
        }
        
        .notification-border-yellow {
            border-left: 4px solid #f59e0b;
        }
        
        .notification-border-red {
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navbar com design verde e Tailwind -->
    <nav class="gradient-green shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="#" class="flex items-center text-white font-bold text-xl">
                        <i class="fas fa-warehouse mr-3"></i>Almoxarifado
                    </a>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="#" class="text-white hover:text-green-200 px-3 py-2 rounded-md text-sm font-medium bg-green-600 bg-opacity-50">
                                <i class="fas fa-home mr-1"></i> Início
                            </a>
                             <a href="../estoque.php" class="text-white hover:text-green-200 px-3 py-2 rounded-md text-sm font-medium hover:bg-green-600 hover:bg-opacity-30 transition-colors">
                                 <i class="fas fa-box mr-1"></i> Estoque
                             </a>
                            <a href="#" class="text-white hover:text-green-200 px-3 py-2 rounded-md text-sm font-medium hover:bg-green-600 hover:bg-opacity-30 transition-colors">
                                <i class="fas fa-clipboard-list mr-1"></i> Minhas Solicitações
                            </a>
                            <a href="#" class="text-white hover:text-green-200 px-3 py-2 rounded-md text-sm font-medium hover:bg-green-600 hover:bg-opacity-30 transition-colors">
                                <i class="fas fa-history mr-1"></i> Histórico
                            </a>
                            <a href="../logout.php" class="text-white hover:text-green-200 px-3 py-2 rounded-md text-sm font-medium hover:bg-green-600 hover:bg-opacity-30 transition-colors">
                                <i class="fas fa-sign-out-alt mr-3"></i> Sair
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Barra de pesquisa estilizada -->
                    <div class="relative hidden md:block">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" class="bg-white bg-opacity-20 text-white placeholder-green-200 pl-10 pr-4 py-2 rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:bg-opacity-30 transition-all" placeholder="Buscar itens...">
                    </div>
                    
                    <!-- Dropdown do usuário -->
                    <div class="relative">
                        <button class="flex items-center text-white hover:text-green-200 focus:outline-none focus:ring-2 focus:ring-white rounded-full p-1">
                            <img src="https://ui-avatars.com/api/?name=Usuario&background=ffffff&color=10b981" class="w-8 h-8 rounded-full border-2 border-white mr-2">
                            <span class="hidden md:block font-medium">Usuário</span>
                            <i class="fas fa-chevron-down ml-2 text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal com layout Tailwind -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Card de boas-vindas com gradiente verde -->
        <div class="gradient-green rounded-xl p-6 mb-8 text-white card-shadow">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-2/3 mb-4 md:mb-0">
                    <h2 class="text-3xl font-bold mb-2">Olá, Usuário!</h2>
                    <p class="text-green-100 text-lg">Bem-vindo(a) ao sistema de almoxarifado. Aqui você pode solicitar itens, acompanhar seus pedidos e verificar disponibilidade.</p>
                </div>
                <div class="md:w-1/3 text-center">
                    <img src="https://ui-avatars.com/api/?name=Usuario&background=ffffff&color=10b981" class="w-20 h-20 rounded-full border-4 border-white mx-auto">
                </div>
            </div>
        </div>

        <!-- Cards de estatísticas com gradientes verdes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="gradient-green rounded-xl p-6 text-white text-center hover-lift">
                <div class="text-4xl mb-4 opacity-90">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="text-3xl font-bold mb-2">5</div>
                <div class="text-green-100">Solicitações Pendentes</div>
            </div>
            
            <div class="gradient-emerald rounded-xl p-6 text-white text-center hover-lift">
                <div class="text-4xl mb-4 opacity-90">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="text-3xl font-bold mb-2">12</div>
                <div class="text-green-100">Solicitações Aprovadas</div>
            </div>
            
            <div class="gradient-teal rounded-xl p-6 text-white text-center hover-lift">
                <div class="text-4xl mb-4 opacity-90">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="text-3xl font-bold mb-2">28</div>
                <div class="text-green-100">Itens Solicitados</div>
            </div>
            
            <div class="gradient-green-light rounded-xl p-6 text-white text-center hover-lift">
                <div class="text-4xl mb-4 opacity-90">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="text-3xl font-bold mb-2">342</div>
                <div class="text-green-100">Itens Disponíveis</div>
            </div>
        </div>

        <!-- Seção de ações rápidas -->
        <div class="mb-8">
            <h4 class="text-2xl font-bold text-gray-800 mb-6">Ações Rápidas</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 text-center card-shadow hover-lift">
                    <div class="text-4xl text-green-500 mb-4">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h5 class="font-semibold text-gray-800 mb-2">Nova Solicitação</h5>
                    <p class="text-gray-600 text-sm mb-4">Solicite novos itens do almoxarifado</p>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">Acessar</button>
                </div>
                
                <div class="bg-white rounded-xl p-6 text-center card-shadow hover-lift">
                    <div class="text-4xl text-green-500 mb-4">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5 class="font-semibold text-gray-800 mb-2">Histórico</h5>
                    <p class="text-gray-600 text-sm mb-4">Consulte seu histórico de solicitações</p>
                    <button class="border border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-4 py-2 rounded-lg transition-colors">Acessar</button>
                </div>
                
                <div class="bg-white rounded-xl p-6 text-center card-shadow hover-lift">
                    <div class="text-4xl text-green-500 mb-4">
                        <i class="fas fa-boxes"></i>
                    </div>
                     <h5 class="font-semibold text-gray-800 mb-2">Itens Disponíveis</h5>
                     <p class="text-gray-600 text-sm mb-4">Verifique os itens disponíveis no estoque</p>
                     <a href="../estoque.php" class="border border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-4 py-2 rounded-lg transition-colors inline-block">Acessar</a>
                </div>
                
                <div class="bg-white rounded-xl p-6 text-center card-shadow hover-lift">
                    <div class="text-4xl text-green-500 mb-4">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h5 class="font-semibold text-gray-800 mb-2">Ajuda</h5>
                    <p class="text-gray-600 text-sm mb-4">Tutorial e informações sobre o sistema</p>
                    <button class="border border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-4 py-2 rounded-lg transition-colors">Acessar</button>
                </div>
            </div>
        </div>

        <!-- Layout principal com grid Tailwind -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Tabela de solicitações recentes -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl card-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h5 class="text-lg font-semibold text-gray-800">Minhas Solicitações Recentes</h5>
                        <a href="#" class="text-green-500 hover:text-green-600 text-sm font-medium">Ver Todas</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Solicitação</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itens</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SOL-0087</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">25/06/2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 itens</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pendente</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SOL-0086</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">24/06/2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5 itens</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aprovada</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SOL-0085</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">23/06/2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 itens</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Em Análise</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SOL-0084</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">20/06/2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 item</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Entregue</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SOL-0083</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">18/06/2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4 itens</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejeitada</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Painel de notificações -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl card-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-800">Notificações e Avisos</h5>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="notification-border-green bg-green-50 p-4 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h6 class="font-semibold text-gray-800">Nova atualização no sistema</h6>
                                <small class="text-gray-500">Hoje</small>
                            </div>
                            <p class="text-gray-600 text-sm">O sistema de almoxarifado foi atualizado com novas funcionalidades.</p>
                        </div>
                        
                        <div class="notification-border-yellow bg-yellow-50 p-4 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h6 class="font-semibold text-gray-800">Solicitação em análise</h6>
                                <small class="text-gray-500">2h atrás</small>
                            </div>
                            <p class="text-gray-600 text-sm">Sua solicitação #SOL-0087 está sendo analisada pela equipe.</p>
                        </div>
                        
                        <div class="notification-border-green bg-green-50 p-4 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h6 class="font-semibold text-gray-800">Item disponível</h6>
                                <small class="text-gray-500">1 dia</small>
                            </div>
                            <p class="text-gray-600 text-sm">O item "Caneta Azul" que você solicitou está novamente disponível.</p>
                        </div>
                        
                        <div class="notification-border-red bg-red-50 p-4 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h6 class="font-semibold text-gray-800">Prazo de retirada</h6>
                                <small class="text-gray-500">2 dias</small>
                            </div>
                            <p class="text-gray-600 text-sm">Lembre-se de retirar os itens aprovados até sexta-feira.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer simples -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-500 text-sm">
                © 2023 Sistema de Almoxarifado. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- Scripts JavaScript para interatividade -->
    <script>
        // Funcionalidade de busca
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[placeholder="Buscar itens..."]');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        console.log('[v0] Busca realizada:', this.value);
                        // Aqui você pode adicionar a lógica de busca
                    }
                });
            }
        });
    </script>
</body>
</html>
