<?php
require_once('../../../config/auth.php');

// Verificar se é usuário e redirecionar se necessário
requireLogin('user', 'dashboard_usuario.php');

// Incluir conexão com banco de dados e modelos
require_once('../../../config/db.php');
require_once('../../../model/SolicitacoesModel.php');
require_once('../../../model/ItensModel.php');

// Instanciar modelo de solicitações
$solicitacoesModel = new Solicitacoes($pdo);

// Buscar dados do usuário logado
$userData = getCurrentUser();
$id_usuario = $userData['id'];
$nome_usuario = $userData['nome'];

// Buscar estatísticas do dashboard
try {
    $estatisticas = $solicitacoesModel->buscarEstatisticasUsuario($id_usuario);
    $solicitacoes_recentes = $solicitacoesModel->buscarSolicitacoesRecentesUsuario($id_usuario, 5);
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
// Buscar todos os itens disponíveis
try {
    $itensModel = new Itens($pdo);
    $itens = $itensModel->buscarTodosItens();

    // Filtrar apenas itens com estoque disponível
    $itens_disponiveis = array_filter($itens, function ($item) {
        return $item['quantidade'] > 0;
    });
} catch (Exception $e) {
    $erros[] = "Erro ao buscar itens: " . $e->getMessage();
    $itens_disponiveis = [];
}

// Buscar todas as solicitações com status 'em espera'
try {
    $solicitacoesModel = new Solicitacoes($pdo);
    $solicitacoes = $solicitacoesModel->buscarTodasSolicitacoes();
} catch (Exception $e) {
    $erros[] = "Erro ao buscar solicitações: " . $e->getMessage();
    $solicitacoes = [];
}

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
    <title>Dashboard Usuário - Almoxarifado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../../../assets/images/brasao.png" type="image/x-icon">
        <link rel="stylesheet" href="../../../assets/css/icons.css">
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
    <div class="flex">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 sidebar-gradient text-white h-screen fixed transition-transform -translate-x-full md:translate-x-0 duration-300 z-50 shadow-xl">
            <div class="p-6 text-center border-b border-green-light border-opacity-20 bg-black bg-opacity-10">
                <div class="flex items-center justify-center">
                    <svg class="icon icon-warehouse text-2xl mr-3" viewBox="0 0 24 24">
                        <path d="M3 21h18l-1-7H4l-1 7z"/>
                        <path d="M3 10h18l-1-7H4l-1 7z"/>
                        <path d="M9 10v11"/>
                        <path d="M15 10v11"/>
                    </svg>
                    <span class="text-xl font-bold">Almoxarifado</span>
                </div>
            </div>
            
            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <svg class="icon icon-home w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../estoque.php" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-boxes w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="../solicitacoes.php" class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
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
                    <button id="sidebarToggle" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fa fa-bars"></i>
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

            <!-- Conteúdo do Dashboard -->
            <div class="p-6">

                <!-- Título da Página -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4 sm:mb-0">Dashboard do Usuário</h1>
                    <div class="flex space-x-3">
                        <a href="../solicitacoes.php" class="px-4 py-2 bg-green-primary text-white rounded-lg hover:bg-green-secondary transition-colors duration-200">
                            <svg class="icon icon-plus mr-2 inline" viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>Solicitação
                        </a>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Solicitações Pendentes</p>
                                <p class="text-2xl font-semibold"><?php echo $pendentesCount;?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-2 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Solicitações Aprovadas</p>
                                <p class="text-2xl font-semibold"><?php echo $estatisticas['solicitacoes_aprovadas']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-3 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-tools text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-100">Itens Solicitados</p>
                                <p class="text-2xl font-semibold"><?php echo $estatisticas['itens_solicitados']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-4 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-100">Itens Disponíveis</p>
                                <p class="text-2xl font-semibold"><?php echo $estatisticas['itens_disponiveis']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabelas de Solicitações -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <!-- Minhas Solicitações Recentes -->
                     <!-- Últimas Solicitações -->
                     <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover xl:col-span-2 w-full">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Últimas Solicitações</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 sm:px-6 md:px-8 lg:px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requisição</th>
                                        <th
                                            class="px-4 sm:px-6 md:px-8 lg:px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Solicitante</th>
                                        <th
                                            class="px-4 sm:px-6 md:px-8 lg:px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data</th>
                                        <th
                                            class="px-4 sm:px-6 md:px-8 lg:px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($ultimas_solicitacoes)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                <svg class="icon icon-clipboard-list text-4xl mb-2 block mx-auto" viewBox="0 0 24 24">
                                                    <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
                                                    <rect x="9" y="3" width="6" height="4" rx="2" ry="2"/>
                                                    <line x1="9" y1="12" x2="15" y2="12"/>
                                                    <line x1="9" y1="16" x2="15" y2="16"/>
                                                    <line x1="9" y1="20" x2="15" y2="20"/>
                                                </svg>
                                                Nenhuma solicitação encontrada
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ultimas_solicitacoes as $solicitacao): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 sm:px-6 md:px-8 lg:px-10 py-4 text-sm font-medium text-gray-900">
                                                    #SOL-<?php echo str_pad($solicitacao['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                <td class="px-4 sm:px-6 md:px-8 lg:px-10 py-4 text-sm text-gray-600">
                                                    <?php echo htmlspecialchars($nome_usuario); ?>
                                                </td>
                                                <td class="px-4 sm:px-6 md:px-8 lg:px-10 py-4 text-sm text-gray-600">
                                                    <?php echo date('d/m/Y', strtotime($solicitacao['data'])); ?>
                                                </td>
                                                <td class="px-4 sm:px-6 md:px-8 lg:px-10 py-4">
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
                </div>
                
                <!-- Footer -->
                <footer class="mt-12 py-6 w-full">
                    <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-500 gap-2">
                        <div class="text-center">
                            Copyright &copy; Prefeitura de Maranguape Sistema de Almoxarifado 2025
                        </div>
                        <span class="hidden sm:inline mx-2">&middot;</span>
                        <div class="text-center">
                            Desenvolvido por: Fulano, Beltrano e Ciclano
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
                    <a href="../../../view/logout.php" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-center">
                        Sim, Sair
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Exportar Relatório -->
    <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Exportar Relatório por Período</h3>
                <button id="closeExportModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="icon icon-times" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="../relatorios/relatorioPorPeriodo.php" method="get" target="_blank" class="px-6 py-4">
                <input type="hidden" name="acao" value="pdf">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data início</label>
                    <input type="date" name="inicio" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data fim</label>
                    <input type="date" name="fim" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-primary">
                </div>
                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4 pb-6">
                    <button type="button" id="cancelExportModal" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-green-primary text-white hover:bg-green-secondary">Gerar PDF</button>
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

        // Modal Exportar Relatório
        (function(){
            const openBtn = document.getElementById('exportReportBtn');
            const modal = document.getElementById('exportModal');
            const closeBtn = document.getElementById('closeExportModal');
            const cancelBtn = document.getElementById('cancelExportModal');

            function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
            function closeModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });
        })();
    </script>
</body>
</html>
