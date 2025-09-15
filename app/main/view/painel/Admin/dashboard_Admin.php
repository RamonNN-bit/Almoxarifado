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
    $itens_criticos = array_filter($itens, function ($item) {
        return $item['quantidade'] <= 10; // Considerar crítico se <= 10
    });
    $itens_em_falta = array_filter($itens, function ($item) {
        return $item['quantidade'] == 0;
    });

    // Buscar todas as solicitações
    $solicitacoes = $solicitacoesModel->buscarTodasSolicitacoes();

    // Calcular estatísticas de solicitações
    $solicitacoes_hoje = array_filter($solicitacoes, function ($s) {
        return date('Y-m-d', strtotime($s['data'])) == date('Y-m-d');
    });

    $solicitacoes_pendentes = array_filter($solicitacoes, function ($s) {
        return $s['status'] === 'em espera';
    });

    $solicitacoes_aprovadas = array_filter($solicitacoes, function ($s) {
        return $s['status'] === 'aprovado';
    });

    $solicitacoes_recusadas = array_filter($solicitacoes, function ($s) {
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
        <div id="sidebar"
            class="w-64 sidebar-gradient text-white h-screen fixed transition-transform -translate-x-full md:translate-x-0 duration-300 z-50 shadow-xl">
            <div class="p-6 text-center border-b border-green-light border-opacity-20 bg-black bg-opacity-10">
                <div class="flex items-center justify-center">
                    <i class="fas fa-warehouse text-2xl mr-3"></i>
                    <span class="text-xl font-bold">Almoxarifado</span>
                </div>
            </div>

            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="#"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <i class="fas fa-home w-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../estoque.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-boxes w-5 mr-3"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="itens_cadastro.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <li>
                        <a href="../solicitacoes.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-clipboard-list w-5 mr-3"></i>
                            <span>Solicitações</span>
                        </a>
                    </li>
                    <li class="mt-8">
                        <a href="../../../view/logout.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-red-600 hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Conteúdo Principal -->
        <div id="content" class="flex-1 md:ml-64 min-h-screen">
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
                            <?php $pendentesCount = isset($solicitacoes_pendentes) ? count($solicitacoes_pendentes) : 0; ?>
                            <button class="p-2 text-gray-600 hover:text-green-primary relative">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $pendentesCount; ?></span>
                            </button>
                        </div>
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo substr($_SESSION['nome'] ?? 'Admin', 0, 2); ?>&background=059669&color=ffffff"
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

            <!-- Conteúdo do Dashboard -->
            <div class="p-6">
                <!-- Título da Página -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Administrador</h1>
                    <p class="text-gray-600">Visualize de forma resumida as funcionalides do sistema</p>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                    <div class="flex space-x-3">
                        <button id="exportReportBtn"
                            class="px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-download mr-2"></i>Exportar Relatório
                        </button>
                        <a href="../solicitacoes.php"
                            class="px-4 py-2 bg-green-primary text-white rounded-lg hover:bg-green-secondary transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Nova Solicitação
                        </a>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Itens em Estoque</p>
                                <p class="text-2xl font-semibold"><?php echo number_format($total_itens_estoque); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-2 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-clipboard-check text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Solicitações Hoje</p>
                                <p class="text-2xl font-semibold"><?php echo count($solicitacoes_hoje); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-3 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-100">Itens Críticos</p>
                                <p class="text-2xl font-semibold"><?php echo count($itens_criticos); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-4 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-times-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-100">Itens em Falta</p>
                                <p class="text-2xl font-semibold"><?php echo count($itens_em_falta); ?></p>
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
                                        <p class="text-sm font-medium text-red-800"><?php echo count($itens_em_falta); ?>
                                            itens em falta</p>
                                        <p class="text-xs text-red-600">Reabastecimento urgente necessário</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (count($itens_criticos) > 0): ?>
                                <div class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800"><?php echo count($itens_criticos); ?>
                                            itens críticos</p>
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
                                    <div class="text-3xl font-bold text-blue-600">
                                        <?php echo count($solicitacoes_pendentes); ?>
                                    </div>
                                    <p class="text-sm text-gray-600">aguardando aprovação</p>
                                </div>
                                <div class="space-y-2">
                                    <?php foreach (array_slice($solicitacoes_pendentes, 0, 3) as $solicitacao): ?>
                                        <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-user text-blue-500 mr-2 text-xs"></i>
                                                <span
                                                    class="text-xs text-gray-700"><?php echo htmlspecialchars(substr($solicitacao['usuario_nome'], 0, 15)); ?></span>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500"><?php echo date('d/m', strtotime($solicitacao['data'])); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($solicitacoes_pendentes) > 3): ?>
                                    <div class="mt-3 text-center">
                                        <a href="../solicitacoes.php" class="text-xs text-blue-600 hover:text-blue-800">Ver
                                            todas (<?php echo count($solicitacoes_pendentes); ?>)</a>
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
                                <span
                                    class="text-sm font-semibold text-gray-900"><?php echo count($solicitacoes_hoje); ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Aprovadas hoje</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">
                                    <?php
                                    $aprovadas_hoje = array_filter($solicitacoes_hoje, function ($s) {
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
                                <span
                                    class="text-sm font-semibold text-gray-900"><?php echo number_format($total_itens_estoque); ?></span>
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
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Item</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Marca</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estoque</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
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
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <?php echo htmlspecialchars($item['nome']); ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    <?php echo htmlspecialchars($item['marca'] ?? 'N/A'); ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo $item['quantidade']; ?>
                                                    <?php echo htmlspecialchars($item['unidade']); ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?php if ($item['quantidade'] == 0): ?>
                                                        <span
                                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Em
                                                            Falta</span>
                                                    <?php else: ?>
                                                        <span
                                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Crítico</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-6 border-t border-gray-200 text-center">
                            <a href="../estoque.php"
                                class="inline-flex items-center px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
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
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requisição</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Solicitante</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
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
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    #SOL-<?php echo str_pad($solicitacao['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    <?php echo htmlspecialchars($solicitacao['usuario_nome']); ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    <?php echo date('d/m/Y', strtotime($solicitacao['data'])); ?>
                                                </td>
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
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $classe; ?>">
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
                            <a href="../solicitacoes.php"
                                class="inline-flex items-center px-4 py-2 border border-green-primary text-green-primary rounded-lg hover:bg-green-primary hover:text-white transition-colors duration-200">
                                Ver todas as solicitações
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="mt-12 py-6 w-full">
                    <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-500 gap-2">
                        <div class="text-center">
                            Copyright &copy; Sistema de Almoxarifado 2025
                        </div>
                        <span class="hidden sm:inline mx-2">&middot;</span>
                        <div class="text-center">
                            Desenvolvido por: <a href="https://instagram.com/rogercavalcantetz">Roger</a>, Ramon, Larissa e Denilson
                        </div>
                        <span class="hidden sm:inline mx-2">&middot;</span>
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

    <!-- Modal Exportar Relatório -->
    <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Exportar Relatório por Período</h3>
                <button id="closeExportModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="../relatorios/relatorioPorPeriodo.php" method="get" target="_blank" class="px-6 py-4">
                <input type="hidden" name="acao" value="pdf">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data início</label>
                    <input type="date" name="inicio" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data fim</label>
                    <input type="date" name="fim" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-primary">
                </div>
                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4 pb-6">
                    <button type="button" id="cancelExportModal"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-green-primary text-white hover:bg-green-secondary">Gerar
                        PDF</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts JavaScript para interatividade -->
    <script>
        // Script para toggle da sidebar em dispositivos móveis
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
        });

        // Funcionalidade de busca
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[placeholder="Buscar item, material..."]');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        console.log('[v0] Busca realizada:', this.value);
                        // Aqui você pode adicionar a lógica de busca
                    }
                });
            }
        });

        // Modal Exportar Relatório
        (function() {
            const openBtn = document.getElementById('exportReportBtn');
            const modal = document.getElementById('exportModal');
            const closeBtn = document.getElementById('closeExportModal');
            const cancelBtn = document.getElementById('cancelExportModal');

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        })();
    </script>
</body>

</html>