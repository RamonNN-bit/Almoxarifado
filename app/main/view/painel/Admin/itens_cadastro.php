<?php
session_start();
if (!isset($_SESSION ["usuariologado"])) {
header("Location: index.php");
}

// Incluir o controller para processar o formulário

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Itens - Almoxarifado</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
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
                        <a href="dashboard_Admin.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-home w-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../estoque.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-boxes w-5 mr-3"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="itens_cadastro.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <li>
                        <a href="solicitacoes.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
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
                        <a href="relatorios.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
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
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-bell"></i>
                            </button>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=059669&color=ffffff" class="w-8 h-8 rounded-full">
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">Administrador</p>
                                <p class="text-xs text-gray-500">admin@almoxarifado.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Conteúdo Principal -->
            <div class="p-6">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Cadastro de Itens</h1>
                    <p class="text-gray-600">Cadastre novos itens no sistema de almoxarifado</p>
                </div>
        
                <!-- Exibir mensagens de sucesso ou erro -->
                <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo htmlspecialchars($_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php unset($_SESSION['mensagem_sucesso']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo htmlspecialchars($_SESSION['erro'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php unset($_SESSION['erro']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($erros) && !empty($erros)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Erro(s) encontrado(s):</strong>
                        </div>
                        <ul class="list-disc list-inside ml-4">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
        
                <!-- Formulário de Cadastro -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Novo Item</h2>
                    <form method="POST" action="../../control/criarController.php" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2"></i>Nome do Item
                                </label>
                                <input type="text" id="nome" name="nome" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                       placeholder="Digite o nome do item"
                                       value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                            
                            <div>
                                <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hashtag mr-2"></i>Quantidade
                                </label>
                                <input type="number" id="quantidade" name="quantidade" required min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                       placeholder="Quantidade em estoque"
                                       value="<?php echo isset($_POST['quantidade']) ? htmlspecialchars($_POST['quantidade'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="unidade" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-ruler mr-2"></i>Unidade
                                </label>
                                <input type="text" id="unidade" name="unidade" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                       placeholder="Ex: kg, litros, unidades"
                                       value="<?php echo isset($_POST['unidade']) ? htmlspecialchars($_POST['unidade'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                            
                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-industry mr-2"></i>Marca
                                </label>
                                <input type="text" id="marca" name="marca" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                       placeholder="Marca do produto (opcional)"
                                       value="<?php echo isset($_POST['marca']) ? htmlspecialchars($_POST['marca'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                        </div>
                        
                        <div>
                            <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cube mr-2"></i>Modelo
                            </label>
                            <input type="text" id="modelo" name="modelo" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                   placeholder="Modelo do produto (opcional)"
                                   value="<?php echo isset($_POST['modelo']) ? htmlspecialchars($_POST['modelo'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-green-primary hover:bg-green-secondary text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center">
                                <i class="fas fa-plus mr-2"></i>Cadastrar Item
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de Itens Cadastrados -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Itens Cadastrados</h2>
                    </div>
                    
                    <?php if (empty($itens)): ?>
                        <div class="p-8 text-center">
                            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-500 mb-2">Nenhum item encontrado</h3>
                            <p class="text-gray-400">Cadastre o primeiro item usando o formulário acima.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($itens as $item): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #<?php echo str_pad($item['id'], 3, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    <?php echo $item['quantidade'] <= 5 ? 'bg-red-100 text-red-800' : ($item['quantidade'] <= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); ?>">
                                                    <?php echo htmlspecialchars($item['quantidade'], ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo htmlspecialchars($item['marca'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo htmlspecialchars($item['modelo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        // Toggle da sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    content.classList.toggle('ml-0');
                });
            }
        });
    </script>
</body>
</html>