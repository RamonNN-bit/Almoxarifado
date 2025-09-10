<?php
session_start();
if (!isset($_SESSION ["usuariologado"])) {
header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Almoxarifado</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-primary': '#059669',
                        'green-secondary': '#047857',
                        'green-light': '#10b981',
                        'green-dark': '#065f46',
                        'green-accent': '#34d399',
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #065f46 0%, #047857 100%);
        }
        
        .stat-card-gradient-1 {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        
        .stat-card-gradient-2 {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
        }
        
        .stat-card-gradient-3 {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        }
        
        .stat-card-gradient-4 {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .sidebar-link-active {
            background: rgba(16, 185, 129, 0.1);
            border-left: 3px solid #10b981;
        }
        
        .badge-green {
            background-color: #059669;
        }
        
        .badge-orange {
            background-color: #f59e0b;
        }
        
        .badge-red {
            background-color: #dc2626;
        }
        
        .badge-blue {
            background-color: #2563eb;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 sidebar-gradient text-white h-screen fixed transition-all duration-300 z-50 shadow-xl">
            <div class="p-6 text-center border-b border-green-light border-opacity-20 bg-black bg-opacity-10">
                <div class="flex items-center justify-center">
                    <i class="fas fa-warehouse text-2xl mr-3"></i>
                    <span class="text-xl font-bold">Almoxarifado</span>
                </div>
            </div>
            
            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <i class="fas fa-home w-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-boxes w-5 mr-3"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Materiais</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-clipboard-list w-5 mr-3"></i>
                            <span>Solicitações</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-truck-loading w-5 mr-3"></i>
                            <span>Entradas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-external-link-alt w-5 mr-3"></i>
                            <span>Saídas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-chart-bar w-5 mr-3"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-cog w-5 mr-3"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                    <li class="mt-8">
                        <a href="../logout.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-red-600 hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Conteúdo Principal -->
        <div id="content" class="flex-1 ml-64 min-h-screen">
            <!-- Topbar -->
            <nav class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <button id="sidebarToggle" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fa fa-bars"></i>
                    </button>

                    <div class="hidden sm:flex items-center flex-1 max-w-md mx-4">
                        <div class="relative w-full">
                            <input type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent" placeholder="Buscar item, material...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <button class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-search text-green-primary"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="p-2 text-gray-600 hover:text-green-primary relative">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                        </div>
                        <div class="relative">
                            <button class="p-2 text-gray-600 hover:text-green-primary relative">
                                <i class="fas fa-envelope text-lg"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">2</span>
                            </button>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="hidden lg:block text-sm text-gray-600">João Silva</span>
                            <img class="h-8 w-8 rounded-full border-2 border-green-primary" src="https://ui-avatars.com/api/?name=João+Silva&background=059669&color=fff" alt="Profile">
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Conteúdo do Dashboard -->
            <div class="p-6">
                <!-- Título da Página -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4 sm:mb-0">Dashboard do Almoxarifado</h1>
                    <div class="flex space-x-3">
                        <button class="px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-download mr-2"></i>Exportar Relatório
                        </button>
                        <button class="px-4 py-2 bg-green-primary text-white rounded-lg hover:bg-green-secondary transition-colors duration-200">
                            <a href="itens_cadastro.php"><i class="fas fa-plus mr-2"></i>Novo Item</a>
                        </button>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Itens em Estoque</p>
                                <p class="text-3xl font-bold mt-2">1,248</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <i class="fas fa-boxes text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-2 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Solicitações Hoje</p>
                                <p class="text-3xl font-bold mt-2">42</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <i class="fas fa-clipboard-check text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-3 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Itens Críticos</p>
                                <p class="text-3xl font-bold mt-2">18</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-4 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium uppercase tracking-wide">Itens em Falta</p>
                                <p class="text-3xl font-bold mt-2">7</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos e Tabelas -->
               
                    
                    <!-- Status de Solicitações -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Status das Solicitações</h3>
                        </div>
                        <div class="p-6">
                            <canvas id="requestChart" height="250"></canvas>
                            <div class="mt-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs font-medium rounded-full mr-3">42</span>
                                        <span class="text-sm text-gray-600">Pendentes</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">48%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white text-xs font-medium rounded-full mr-3">32</span>
                                        <span class="text-sm text-gray-600">Aprovadas</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">36%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-red-500 text-white text-xs font-medium rounded-full mr-3">8</span>
                                        <span class="text-sm text-gray-600">Rejeitadas</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">9%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-500 text-white text-xs font-medium rounded-full mr-3">7</span>
                                        <span class="text-sm text-gray-600">Em análise</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">8%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabelas de Itens e Solicitações -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <!-- Itens com Estoque Crítico -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Itens com Estoque Crítico</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Parafuso 5mm Aço Inox</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Fixadores</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">12/100</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Crítico</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Fita Isolante Preta</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Elétrica</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">3/50</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Crítico</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Resistor 10kΩ</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Componentes</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">22/200</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Baixo</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Chave de Fenda Philips</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Ferramentas</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">2/15</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Crítico</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">Luvas de Proteção</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">EPI</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">8/40</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Baixo</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-gray-200 text-center">
                            <a href="#" class="inline-flex items-center px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
                                Ver todos os itens
                            </a>
                        </div>
                    </div>
                    
                    <!-- Últimas Solicitações -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Últimas Solicitações</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requisição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#REQ-0042</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Carlos Oliveira</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">20/06/2023</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pendente</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#REQ-0041</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Maria Santos</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">20/06/2023</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aprovada</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#REQ-0040</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Pedro Alves</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">19/06/2023</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejeitada</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#REQ-0039</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Ana Costa</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">19/06/2023</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aprovada</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#REQ-0038</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">João Silva</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">18/06/2023</td>
                                        <td class="px-6 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Em análise</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-gray-200 text-center">
                            <a href="#" class="inline-flex items-center px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
                                Ver todas as solicitações
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <footer class="mt-12 py-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500">
                        <div>Copyright &copy; Sistema de Almoxarifado 2023</div>
                        <div class="mt-2 sm:mt-0">
                            <a href="#" class="hover:text-green-primary">Política de Privacidade</a>
                            <span class="mx-2">&middot;</span>
                            <a href="#" class="hover:text-green-primary">Termos de Uso</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Script para toggle da sidebar em dispositivos móveis
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
        });
        
        // Gráfico de movimentação de estoque
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        const stockChart = new Chart(stockCtx, {
            type: 'line',
            data: {
                labels: ['01/06', '05/06', '10/06', '15/06', '20/06', '25/06', '30/06'],
                datasets: [{
                    label: 'Entradas',
                    data: [120, 115, 130, 140, 145, 150, 160],
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 3
                }, {
                    label: 'Saídas',
                    data: [100, 105, 110, 120, 125, 130, 135],
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
        
        // Gráfico de status de solicitações
        const requestCtx = document.getElementById('requestChart').getContext('2d');
        const requestChart = new Chart(requestCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pendentes', 'Aprovadas', 'Rejeitadas', 'Em análise'],
                datasets: [{
                    data: [42, 32, 8, 7],
                    backgroundColor: [
                        '#3b82f6',
                        '#059669',
                        '#dc2626',
                        '#6366f1'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '60%'
            }
        });
    </script>
</body>
</html>
