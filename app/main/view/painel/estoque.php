<?php
require_once '../../config/auth.php';

// Verificar se está logado e redirecionar se necessário
requireLogin(null, 'estoque.php');

// Incluir arquivos necessários
require_once '../../config/db.php';
require_once '../../model/ItensModel.php';

// Buscar todos os itens do banco de dados
try {
    $itensModel = new Itens($pdo);
    $itens = $itensModel->buscarTodosItens();
} catch (Exception $e) {
    $erros[] = "Erro ao buscar itens: " . $e->getMessage();
    $itens = [];
}

// Função para determinar o status do estoque
function getStatusEstoque($quantidade) {
    if ($quantidade <= 5) {
        return ['status' => 'Crítico', 'class' => 'bg-red-100 text-red-800'];
    } elseif ($quantidade <= 15) {
        return ['status' => 'Baixo', 'class' => 'bg-yellow-100 text-yellow-800'];
    } else {
        return ['status' => 'Normal', 'class' => 'bg-green-100 text-green-800'];
    }
}

// Função para determinar a categoria baseada no nome
function getCategoria($nome) {
    $nome = strtolower($nome);
    if (strpos($nome, 'parafuso') !== false || strpos($nome, 'porca') !== false || strpos($nome, 'arruela') !== false) {
        return 'Fixadores';
    } elseif (strpos($nome, 'fita') !== false || strpos($nome, 'cabo') !== false || strpos($nome, 'resistor') !== false) {
        return 'Elétrica';
    } elseif (strpos($nome, 'chave') !== false || strpos($nome, 'martelo') !== false || strpos($nome, 'alicate') !== false) {
        return 'Ferramentas';
    } elseif (strpos($nome, 'luva') !== false || strpos($nome, 'capacete') !== false || strpos($nome, 'óculos') !== false) {
        return 'EPI';
    } else {
        return 'Componentes';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque - Almoxarifado</title>
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
        
        .status-critical {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
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
                        <a href="./Admin/dashboard_Admin.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-home w-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="estoque.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <i class="fas fa-boxes w-5 mr-3"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['admin']){?>
                    <li>
                        <a href="./Admin/itens_cadastro.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <?php }?>
                    <li>
                        <a href="solicitacoes.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-clipboard-list w-5 mr-3"></i>
                            <span>Solicitações</span>
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
                            <img src="https://ui-avatars.com/api/?name=Admin&background=059669&color=ffffff"
                                class="w-8 h-8 rounded-full">
                            <div class="hidden md:block">
                                <?php if ($_SESSION['admin']) { ?>
                                    <p class="text-sm font-medium text-gray-700">Administrador</p>
                                <?php } else { ?>
                                    <p class="text-sm font-medium text-gray-700">Usuário</p>
                                <?php } ?>
                                <p class="text-xs text-gray-500"><?php echo ($_SESSION['email']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Conteúdo Principal -->
            <div class="p-6">
        
        <!-- Header da página -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Controle de Estoque</h1>
            <p class="text-gray-600">Visualize e gerencie todos os itens do almoxarifado</p>
        </div>

                <!-- Cards de estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Total de Itens</p>
                                <p class="text-2xl font-semibold"><?php echo count($itens); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-gradient-4 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-100">Estoque Crítico</p>
                                <p class="text-2xl font-semibold">
                                    <?php 
                                    $criticos = array_filter($itens, function($item) { return $item['quantidade'] <= 5; });
                                    echo count($criticos);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-gradient-3 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-exclamation-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-100">Estoque Baixo</p>
                                <p class="text-2xl font-semibold">
                                    <?php 
                                    $baixos = array_filter($itens, function($item) { return $item['quantidade'] > 5 && $item['quantidade'] <= 15; });
                                    echo count($baixos);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-gradient-2 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Estoque Normal</p>
                                <p class="text-2xl font-semibold">
                                    <?php 
                                    $normais = array_filter($itens, function($item) { return $item['quantidade'] > 15; });
                                    echo count($normais);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar por nome</label>
                    <input type="text" id="filterNome" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Digite o nome do item...">
                </div>
                <div class="md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Todos</option>
                        <option value="critico">Crítico</option>
                        <option value="baixo">Baixo</option>
                        <option value="normal">Normal</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="clearFilters" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpar
                    </button>
                </div>
            </div>
        </div>

                <!-- Tabela de itens -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Itens do Estoque</h3>
            </div>
            
            <?php if (empty($itens)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-500 mb-2">Nenhum item encontrado</h3>
                    <p class="text-gray-400">Cadastre o primeiro item para começar a gerenciar o estoque.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full" id="estoqueTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(0)">
                                    <i class="fas fa-sort mr-1"></i>ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(1)">
                                    <i class="fas fa-sort mr-1"></i>Item
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(2)">
                                    <i class="fas fa-sort mr-1"></i>Estoque
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(3)">
                                    <i class="fas fa-sort mr-1"></i>Unidade
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($itens as $item): 
                                $status = getStatusEstoque($item['quantidade']);
                                $statusKey = strtr(mb_strtolower($status['status']), ['á'=>'a','ã'=>'a','â'=>'a','à'=>'a','ç'=>'c','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u']);
                            ?>
                                <tr class="hover:bg-gray-50 item-row" 
                                    data-nome="<?php echo strtolower($item['nome']); ?>"
                                    data-status="<?php echo $statusKey; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?php echo str_pad($item['id'], 3, '0', STR_PAD_LEFT); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <span class="font-semibold"><?php echo $item['quantidade']; ?></span>
                                            <?php if ($item['quantidade'] <= 5): ?>
                                                <i class="fas fa-exclamation-triangle text-red-500 ml-2 status-critical"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars((isset($item['marca']) && trim($item['marca']) !== '' ? $item['marca'] : 'Marca não informada'), ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars((isset($item['modelo']) && trim($item['modelo']) !== '' ? $item['modelo'] : 'Modelo não informado'), ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $status['class']; ?>">
                                            <?php echo $status['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

                <!-- Botão para ver todos os itens (se houver muitos) -->
                <?php if (count($itens) > 10): ?>
                    <div class="text-center mt-6">
                        <button class="bg-green-primary hover:bg-green-secondary text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-list mr-2"></i>Ver todos os itens
                        </button>
                    </div>
                <?php endif; ?>
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

        // Funcionalidade de busca e filtros
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterNome = document.getElementById('filterNome');
            const filterStatus = document.getElementById('filterStatus');
            const filterCategoria = document.getElementById('filterCategoria');
            const clearFilters = document.getElementById('clearFilters');
            const rows = document.querySelectorAll('.item-row');

            function filterTable() {
                const nomeFilter = filterNome.value.toLowerCase();
                const statusFilter = filterStatus.value.toLowerCase();
                const categoriaFilter = filterCategoria.value;

                rows.forEach(row => {
                    const nome = row.dataset.nome;
                    const status = row.dataset.status;
                    const categoria = row.dataset.categoria;

                    const nomeMatch = nome.includes(nomeFilter);
                    const statusMatch = !statusFilter || status === statusFilter;
                    const categoriaMatch = !categoriaFilter || categoria === categoriaFilter;

                    if (nomeMatch && statusMatch && categoriaMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Event listeners
            filterNome.addEventListener('input', filterTable);
            filterStatus.addEventListener('change', filterTable);
            filterCategoria.addEventListener('change', filterTable);

            clearFilters.addEventListener('click', function() {
                filterNome.value = '';
                filterStatus.value = '';
                filterCategoria.value = '';
                filterTable();
            });

            // Busca global
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const nome = row.dataset.nome;
                    if (nome.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Funcionalidade de ordenação
        function sortTable(columnIndex) {
            const table = document.getElementById('estoqueTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim();
                const bText = b.cells[columnIndex].textContent.trim();
                
                // Para números (ID e quantidade)
                if (columnIndex === 0 || columnIndex === 3) {
                    return parseInt(aText.replace('#', '')) - parseInt(bText.replace('#', ''));
                }
                
                // Para texto
                return aText.localeCompare(bText);
            });

            // Limpar tbody e adicionar linhas ordenadas
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        }
    </script>
</body>
</html>
