<?php
require_once '../../../config/auth.php';

// Verificar se é admin e redirecionar se necessário
requireLogin('admin', 'dashboard_Admin.php');

// Incluir conexão com banco de dados e modelos
require_once('../../../config/db.php');
require_once('../../../model/ItensModel.php');
require_once('../../../model/SolicitacoesModel.php');

// Instanciar modelos
$itensModel = new Itens($pdo);
$solicitacoesModel = new Solicitacoes($pdo);

// Buscar dados do dashboard
try {
    // Buscar todos os itens
    $itens = $itensModel->buscarTodosItens();
    
    // Calcular estatísticas de itens
    $total_itens_estoque = array_sum(array_column($itens, 'quantidade'));
    $itens_criticos = array_filter($itens, function($item) {
        return $item['quantidade'] <= 10; // Considerar crítico se <= 10
    });
    $itens_em_falta = array_filter($itens, function($item) {
        return $item['quantidade'] == 0;
    });
    
    // Buscar todas as solicitações
    $solicitacoes = $solicitacoesModel->buscarTodasSolicitacoes();
    
    // Calcular estatísticas de solicitações
    $solicitacoes_hoje = array_filter($solicitacoes, function($s) {
        return date('Y-m-d', strtotime($s['data'])) == date('Y-m-d');
    });
    
    $solicitacoes_pendentes = array_filter($solicitacoes, function($s) {
        return $s['status'] === 'em espera';
    });
    
    $solicitacoes_aprovadas = array_filter($solicitacoes, function($s) {
        return $s['status'] === 'aprovado';
    });
    
    $solicitacoes_recusadas = array_filter($solicitacoes, function($s) {
        return $s['status'] === 'recusado';
    });
    
    // Buscar últimas solicitações (limitado a 5)
    $ultimas_solicitacoes = array_slice($solicitacoes, 0, 5);
    
} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $total_itens_estoque = 0;
    $itens_criticos = [];
    $itens_em_falta = [];
    $solicitacoes_hoje = [];
    $solicitacoes_pendentes = [];
    $solicitacoes_aprovadas = [];
    $solicitacoes_recusadas = [];
    $ultimas_solicitacoes = [];
    $erro = $e->getMessage();
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
                        <a href="../estoque.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-boxes w-5 mr-3"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="itens_cadastro.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <li>
                        <a href="../solicitacoes.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-clipboard-list w-5 mr-3"></i>
                            <span>Solicitações</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-chart-bar w-5 mr-3"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li class="mt-8">
                        <a href="../../../view/logout.php" class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-red-600 hover:bg-opacity-20 transition-all duration-200">
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
                            <?php 
                            $userData = getCurrentUser();
                            $nome_admin = $userData['nome'] ?? 'Admin';
                            ?>
                            <span class="hidden lg:block text-sm text-gray-600"><?php echo htmlspecialchars($nome_admin); ?></span>
                            <img class="h-8 w-8 rounded-full border-2 border-green-primary" src="https://ui-avatars.com/api/?name=<?php echo urlencode($nome_admin); ?>&background=059669&color=fff" alt="Profile">
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
                                <p class="text-3xl font-bold mt-2"><?php echo number_format($total_itens_estoque); ?></p>
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
                                <p class="text-3xl font-bold mt-2"><?php echo count($solicitacoes_hoje); ?></p>
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
                                <p class="text-3xl font-bold mt-2"><?php echo count($itens_criticos); ?></p>
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
                                <p class="text-3xl font-bold mt-2"><?php echo count($itens_em_falta); ?></p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas e Notificações -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Alertas de Estoque -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                                Alertas de Estoque
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <?php if (count($itens_em_falta) > 0): ?>
                                <div class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <i class="fas fa-times-circle text-red-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-red-800"><?php echo count($itens_em_falta); ?> itens em falta</p>
                                        <p class="text-xs text-red-600">Reabastecimento urgente necessário</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (count($itens_criticos) > 0): ?>
                                <div class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800"><?php echo count($itens_criticos); ?> itens críticos</p>
                                        <p class="text-xs text-yellow-600">Estoque baixo - atenção necessária</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (count($itens_em_falta) == 0 && count($itens_criticos) == 0): ?>
                                <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-green-800">Estoque em dia</p>
                                        <p class="text-xs text-green-600">Todos os itens com estoque adequado</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Solicitações Pendentes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-clock text-blue-500 mr-2"></i>
                                Solicitações Pendentes
                            </h3>
                        </div>
                        <div class="p-6">
                            <?php if (count($solicitacoes_pendentes) > 0): ?>
                                <div class="text-center mb-4">
                                    <div class="text-3xl font-bold text-blue-600"><?php echo count($solicitacoes_pendentes); ?></div>
                                    <p class="text-sm text-gray-600">aguardando aprovação</p>
                                </div>
                                <div class="space-y-2">
                                    <?php foreach (array_slice($solicitacoes_pendentes, 0, 3) as $solicitacao): ?>
                                        <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-user text-blue-500 mr-2 text-xs"></i>
                                                <span class="text-xs text-gray-700"><?php echo htmlspecialchars(substr($solicitacao['usuario_nome'], 0, 15)); ?></span>
                                            </div>
                                            <span class="text-xs text-gray-500"><?php echo date('d/m', strtotime($solicitacao['data'])); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($solicitacoes_pendentes) > 3): ?>
                                    <div class="mt-3 text-center">
                                        <a href="../solicitacoes.php" class="text-xs text-blue-600 hover:text-blue-800">Ver todas (<?php echo count($solicitacoes_pendentes); ?>)</a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-600">Nenhuma solicitação pendente</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Resumo do Dia -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-calendar-day text-green-500 mr-2"></i>
                                Resumo do Dia
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Solicitações hoje</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900"><?php echo count($solicitacoes_hoje); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Aprovadas hoje</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">
                                    <?php 
                                    $aprovadas_hoje = array_filter($solicitacoes_hoje, function($s) {
                                        return $s['status'] === 'aprovado';
                                    });
                                    echo count($aprovadas_hoje);
                                    ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-boxes text-purple-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Total em estoque</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900"><?php echo number_format($total_itens_estoque); ?></span>
                            </div>
                            
                            <div class="pt-3 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Última atualização</span>
                                    <span class="text-xs text-gray-500"><?php echo date('H:i'); ?></span>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($itens_criticos)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                <i class="fas fa-check-circle text-4xl mb-2 block text-green-500"></i>
                                                Nenhum item com estoque crítico
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($itens_criticos, 0, 5) as $item): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['nome']); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($item['marca'] ?? 'N/A'); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo $item['quantidade']; ?> <?php echo htmlspecialchars($item['unidade']); ?></td>
                                                <td class="px-6 py-4">
                                                    <?php if ($item['quantidade'] == 0): ?>
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Em Falta</span>
                                                    <?php else: ?>
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Crítico</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-gray-200 text-center">
                            <a href="../estoque.php" class="inline-flex items-center px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
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
                                    <?php if (empty($ultimas_solicitacoes)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                <i class="fas fa-clipboard-list text-4xl mb-2 block"></i>
                                                Nenhuma solicitação encontrada
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ultimas_solicitacoes as $solicitacao): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">#SOL-<?php echo str_pad($solicitacao['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($solicitacao['usuario_nome']); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($solicitacao['data'])); ?></td>
                                                <td class="px-6 py-4">
                                                    <?php
                                                    $status = $solicitacao['status'];
                                                    $status_classes = [
                                                        'em espera' => 'bg-blue-100 text-blue-800',
                                                        'aprovado' => 'bg-green-100 text-green-800',
                                                        'recusado' => 'bg-red-100 text-red-800'
                                                    ];
                                                    $status_texto = [
                                                        'em espera' => 'Pendente',
                                                        'aprovado' => 'Aprovada',
                                                        'recusado' => 'Rejeitada'
                                                    ];
                                                    $classe = $status_classes[$status] ?? 'bg-gray-100 text-gray-800';
                                                    $texto = $status_texto[$status] ?? ucfirst($status);
                                                    ?>
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $classe; ?>">
                                                        <?php echo $texto; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-gray-200 text-center">
                            <a href="../solicitacoes.php" class="inline-flex items-center px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
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
                labels: ['Pendentes', 'Aprovadas', 'Rejeitadas'],
                datasets: [{
                    data: [<?php echo $pendentes_count; ?>, <?php echo $aprovadas_count; ?>, <?php echo $recusadas_count; ?>],
                    backgroundColor: [
                        '#3b82f6',
                        '#059669',
                        '#dc2626'
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
