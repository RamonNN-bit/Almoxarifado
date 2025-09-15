<?php
require_once '../../config/auth.php';

// Verificar se está logado e redirecionar se necessário
requireLogin(null, 'estoque.php');

// Incluir arquivos necessários
require_once '../../config/db.php';
require_once '../../model/ItensModel.php';
require_once '../../model/SolicitacoesModel.php';

// Instanciar modelo de solicitações
$solicitacoesModel = new Solicitacoes($pdo);

// Buscar dados do usuário logado   
$id_usuario = $_SESSION['id'];
$nome_usuario = $_SESSION['nome'];

// Buscar estatísticas do dashboard
try {
    $estatisticas = $solicitacoesModel->buscarEstatisticasUsuario($_SESSION['id']);
    $solicitacoes_recentes = $solicitacoesModel->buscarSolicitacoesRecentesUsuario($_SESSION['id'], 5);
} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $estatisticas = [
        'solicitacoes_pendentes' => 0,
        'solicitacoes_aprovadas' => 0,
        'itens_solicitados' => 0,
        'itens_disponiveis' => 0
    ];
    $solicitacoes_recentes = [];
}
// Processar adição de quantidade como movimentação de ENTRADA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'incrementar') {
    $idItem = isset($_POST['id_item']) ? (int)$_POST['id_item'] : 0;
    $qtdAdd = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
    $idUsuario = $_SESSION['id'] ?? null;

    if ($idItem > 0 && $qtdAdd > 0) {
        try {
            $pdo->beginTransaction();

            // Atualiza a tabela de itens
            $stmt = $pdo->prepare('UPDATE itens SET quantidade = quantidade + :qtd WHERE id = :id');
            $stmt->execute([':qtd' => $qtdAdd, ':id' => $idItem]);

            // Insere registro na tabela movimentacoes (tipo entrada)
            $stmt2 = $pdo->prepare('INSERT INTO movimentacoes (id_item, tipo, quantidade, timestamp, id_usuario) VALUES (:id_item, :tipo, :quantidade, NOW(), :id_usuario)');
            $stmt2->execute([
                ':id_item' => $idItem,
                ':tipo' => 'entrada',
                ':quantidade' => $qtdAdd,
                ':id_usuario' => $idUsuario
            ]);

            $pdo->commit();
            header('Location: estoque.php?ok=1');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $erros[] = 'Erro ao adicionar quantidade: ' . $e->getMessage();
        }
    } else {
        $erros[] = 'Item ou quantidade inválidos.';
    }
}

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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque - Almoxarifado</title>
        <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../../assets/images/brasao.png" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/icons.css">
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
        
        /* Mobile sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Mobile sidebar improvements */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
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
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">
    <div class="flex flex-1">
        <!-- Sidebar Overlay for Mobile -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar w-64 sidebar-gradient text-white h-screen fixed transition-transform -translate-x-full md:translate-x-0 duration-300 z-50 shadow-xl">
            <div class="p-6 text-center border-b border-green-light border-opacity-20 bg-black bg-opacity-10">
                <div class="flex items-center justify-center">
                    <img src="../../assets/images/brasao.png" alt="Brasão" class="w-8 h-8 mr-3 object-contain">
                    <span class="text-xl font-bold">Almoxarifado</span>
                </div>
            </div>
            
            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="./Admin/dashboard_Admin.php" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-home w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="estoque.php" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <svg class="icon icon-boxes w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['admin']){?>
                    <li>
                        <a href="./Admin/itens_cadastro.php" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-tools w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <?php }?>
                    <li>
                        <a href="solicitacoes.php" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-clipboard-list w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
                                <rect x="9" y="3" width="6" height="4" rx="2" ry="2"/>
                                <line x1="9" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="15" y2="16"/>
                                <line x1="9" y1="20" x2="15" y2="20"/>
                            </svg>
                            <span>Solicitações</span>
                        </a>
                    </li>

                    <li class="mt-8">
                        <button onclick="showLogoutModal()"
                            class="flex items-center px-6 py-3 text-red-500 font-semibold hover:text-white hover:bg-red-600 transition-all duration-200 w-full text-left">
                            <svg class="icon icon-sign-out w-5 mr-3" viewBox="0 0 24 24" style="stroke-width: 2.5;">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16,17 21,12 16,7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span>Sair</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Conteúdo Principal -->
        <div id="content" class="flex-1 md:ml-64 min-h-screen w-full overflow-x-hidden">
            <!-- Topbar -->
            <nav class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <button id="sidebarToggle" class="md:hidden p-2 rounded-lg text-gray-800 hover:bg-gray-100">
                        <svg class="icon icon-menu text-xl" viewBox="0 0 24 24">
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center flex-1 max-w-md mx-4">
                        <div class="relative w-full">
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <?php 
                                try {
                                    $allS = $solicitacoesModel->buscarTodasSolicitacoes();
                                    $pendentesCount = 0; foreach ($allS as $s) { if (($s['status'] ?? '') === 'em espera') { $pendentesCount++; } }
                                } catch (Exception $e) { $pendentesCount = 0; }
                            ?>
                            <button class="p-2 text-gray-600 hover:text-green-primary relative">
                                <svg class="icon icon-bell text-lg" viewBox="0 0 24 24">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $pendentesCount; ?></span>
                            </button>
                        </div>
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo substr($nome_usuario, 0, 2); ?>&background=059669&color=ffffff"
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
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Total de Itens</p>
                                <p class="text-3xl font-bold mt-2"><?php echo count($itens); ?></p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="icon icon-boxes text-2xl" viewBox="0 0 24 24">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-gradient-4 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium uppercase tracking-wide">Estoque Crítico</p>
                                <p class="text-3xl font-bold mt-2">
                                    <?php 
                                    $criticos = array_filter($itens, function($item) { return $item['quantidade'] <= 5; });
                                    echo count($criticos);
                                    ?>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="icon icon-exclamation-triangle text-2xl" viewBox="0 0 24 24">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-gradient-3 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Estoque Baixo</p>
                                <p class="text-3xl font-bold mt-2">
                                    <?php 
                                    $baixos = array_filter($itens, function($item) { return $item['quantidade'] > 5 && $item['quantidade'] <= 15; });
                                    echo count($baixos);
                                    ?>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="icon icon-exclamation-circle text-2xl" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card-gradient-2 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Estoque Normal</p>
                                <p class="text-3xl font-bold mt-2">
                                    <?php 
                                    $normais = array_filter($itens, function($item) { return $item['quantidade'] > 15; });
                                    echo count($normais);
                                    ?>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="icon icon-check-circle text-2xl" viewBox="0 0 24 24">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22,4 12,14.01 9,11.01"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 min-w-0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar por nome</label>
                    <input type="text" id="filterNome" class="w-full max-w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Digite o nome do item...">
                </div>
                <div class="md:w-48 w-full">
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
                        <svg class="icon icon-times mr-2 inline" viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>Limpar
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
                    <svg class="icon icon-box-open text-6xl text-gray-300 mb-4 mx-auto" viewBox="0 0 24 24">
                        <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2-2z"/>
                        <path d="M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/>
                        <path d="M12 3v18"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-500 mb-2">Nenhum item encontrado</h3>
                    <p class="text-gray-400">Cadastre o primeiro item para começar a gerenciar o estoque.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full" id="estoqueTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(0)">
                                    <svg class="icon icon-sort mr-1 inline" viewBox="0 0 24 24">
                                        <path d="M3 6h18"/>
                                        <path d="M7 12h10"/>
                                        <path d="M10 18h4"/>
                                    </svg>ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(1)">
                                    <svg class="icon icon-sort mr-1 inline" viewBox="0 0 24 24">
                                        <path d="M3 6h18"/>
                                        <path d="M7 12h10"/>
                                        <path d="M10 18h4"/>
                                    </svg>Item
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(2)">
                                    <svg class="icon icon-sort mr-1 inline" viewBox="0 0 24 24">
                                        <path d="M3 6h18"/>
                                        <path d="M7 12h10"/>
                                        <path d="M10 18h4"/>
                                    </svg>Estoque
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTable(3)">
                                    <svg class="icon icon-sort mr-1 inline" viewBox="0 0 24 24">
                                        <path d="M3 6h18"/>
                                        <path d="M7 12h10"/>
                                        <path d="M10 18h4"/>
                                    </svg>Unidade
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
                                                <svg class="icon icon-exclamation-triangle text-red-500 ml-2 status-critical" viewBox="0 0 24 24">
                                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                                </svg>
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
                            <svg class="icon icon-list mr-2 inline" viewBox="0 0 24 24">
                                <line x1="8" y1="6" x2="21" y2="6"/>
                                <line x1="8" y1="12" x2="21" y2="12"/>
                                <line x1="8" y1="18" x2="21" y2="18"/>
                                <line x1="3" y1="6" x2="3.01" y2="6"/>
                                <line x1="3" y1="12" x2="3.01" y2="12"/>
                                <line x1="3" y1="18" x2="3.01" y2="18"/>
                            </svg>Ver todos os itens
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Logout -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="icon icon-sign-out w-6 h-6 text-red-600" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16,17 21,12 16,7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirmar Saída</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Tem certeza que deseja sair do sistema? Você precisará fazer login novamente.
                </p>
                <div class="flex space-x-3">
                    <button onclick="hideLogoutModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </button>
                    <a href="../logout.php" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-center">
                        Sim, Sair
                    </a>
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
                    const overlay = document.getElementById('sidebarOverlay');
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('active');
                });
                
                // Close sidebar when clicking outside
                document.getElementById('sidebarOverlay').addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    this.classList.remove('active');
                });
                
                // Close sidebar on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        sidebar.classList.remove('open');
                        document.getElementById('sidebarOverlay').classList.remove('active');
                    }
                });
            }

            // Funcionalidade de busca e filtros
            const filterNome = document.getElementById('filterNome');
            const filterStatus = document.getElementById('filterStatus');
            const clearFilters = document.getElementById('clearFilters');
            const rows = document.querySelectorAll('.item-row');

            function filterTable() {
                const nomeFilter = filterNome.value.toLowerCase();
                const statusFilter = filterStatus.value.toLowerCase();

                rows.forEach(row => {
                    const nome = row.dataset.nome;
                    const status = row.dataset.status;

                    const nomeMatch = nome.includes(nomeFilter);
                    const statusMatch = !statusFilter || status === statusFilter;

                    if (nomeMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Event listeners
            filterNome.addEventListener('input', filterTable);
            filterStatus.addEventListener('change', filterTable);

            clearFilters.addEventListener('click', function() {
                filterNome.value = '';
                filterStatus.value = '';
                filterTable();
            });
        });

        // Modal de Logout
        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Fechar modal ao clicar fora
        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutModal();
            }
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
                if (columnIndex === 0 || columnIndex === 2) {
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