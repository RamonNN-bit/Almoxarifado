<?php

require_once '../../config/auth.php';

// Verificar se é admin e redirecionar se necessário
requireLogin(null, 'solicitacoes.php');

// Incluir arquivos necessários
require_once '../../config/db.php';
require_once '../../model/ItensModel.php';
require_once('../../model/SolicitacoesModel.php');

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

// Função para determinar a classe do status
function getStatusClass($status)
{
    switch ($status) {
        case 'em espera':
            return 'bg-yellow-100 text-yellow-800';
        case 'aceito':
            return 'bg-green-100 text-green-800';
        case 'recusado':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/images/brasao.png" type="image/x-icon">
    <title>Solicitações - Almoxarifado</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CSS para ícones SVG -->
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
    </style>
</head>

<body class="bg-gray-50 font-sans min-h-screen flex flex-col">
    <div class="flex flex-1">
        <!-- Sidebar -->
        <div id="sidebar"
            class="w-64 sidebar-gradient text-white h-screen fixed transition-transform -translate-x-full md:translate-x-0 duration-300 z-50 shadow-xl">
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
                        <a href="./admin/dashboard_Admin.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-home w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="estoque.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-boxes w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <?php if($_SESSION['admin']){?>
                    <li>
                        <a href="./admin/itens_cadastro.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-tools w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <?php }?>
                    <li>
                        <a href="solicitacoes.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
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
                                $pendentesCount = 0; 
                                if (isset($solicitacoes) && is_array($solicitacoes)) { 
                                    foreach ($solicitacoes as $s) { if (($s['status'] ?? '') === 'em espera') { $pendentesCount++; } }
                                }
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
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Solicitações de Retirada</h1>
                    <p class="text-gray-600">Gerencie as solicitações de retirada de itens do almoxarifado</p>
                </div>

                <!-- Exibir mensagens de sucesso ou erro -->
                <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <svg class="icon icon-check-circle mr-2" viewBox="0 0 24 24">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <?php echo htmlspecialchars($_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php unset($_SESSION['mensagem_sucesso']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <svg class="icon icon-exclamation-circle mr-2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($_SESSION['erro'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php unset($_SESSION['erro']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cards de estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <svg class="icon icon-clipboard-list text-xl" viewBox="0 0 24 24">
                                    <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
                                    <rect x="9" y="3" width="6" height="4" rx="2" ry="2"/>
                                    <line x1="9" y1="12" x2="15" y2="12"/>
                                    <line x1="9" y1="16" x2="15" y2="16"/>
                                    <line x1="9" y1="20" x2="15" y2="20"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Total de Solicitações</p>
                                <p class="text-2xl font-semibold"><?php echo count($solicitacoes); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-3 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <svg class="icon icon-clock text-xl" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-100">Em Espera</p>
                                <p class="text-2xl font-semibold">
                                    <?php
                                    $em_espera = array_filter($solicitacoes, function ($s) {
                                        return $s['status'] === 'em espera';
                                    });
                                    echo count($em_espera);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-2 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <svg class="icon icon-check-circle text-xl" viewBox="0 0 24 24">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22,4 12,14.01 9,11.01"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-100">Aprovadas</p>
                                <p class="text-2xl font-semibold">
                                    <?php
                                    $aprovadas = array_filter($solicitacoes, function ($s) {
                                        return $s['status'] === 'aprovado';
                                    });
                                    echo count($aprovadas);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-gradient-4 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <svg class="icon icon-times-circle text-xl" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-100">Recusadas</p>
                                <p class="text-2xl font-semibold">
                                    <?php
                                    $recusadas = array_filter($solicitacoes, function ($s) {
                                        return $s['status'] === 'recusado';
                                    });
                                    echo count($recusadas);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulário de Nova Solicitação -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Nova Solicitação</h2>
                    <form method="POST" action="../../control/solicitacoesController.php" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="id_item"
                                    class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="icon icon-box mr-2 text-gray-500" viewBox="0 0 24 24">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                                    </svg> Selecionar Item
                                </label>
                                <select id="id_item" name="id_item" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors appearance-none bg-white">
                                    <option value="">Selecione um item...</option>
                                    <?php foreach ($itens_disponiveis as $item): ?>
                                        <option value="<?php echo $item['id']; ?>"
                                            data-quantidade="<?php echo $item['quantidade']; ?>"
                                            data-unidade="<?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                            (<?php echo $item['quantidade']; ?>
                                            <?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="quantidade_solicitada"
                                    class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="icon icon-hashtag mr-2 text-gray-500" viewBox="0 0 24 24">
                                        <line x1="4" y1="9" x2="20" y2="9"/>
                                        <line x1="4" y1="15" x2="20" y2="15"/>
                                        <line x1="10" y1="3" x2="8" y2="21"/>
                                        <line x1="16" y1="3" x2="14" y2="21"/>
                                    </svg> Quantidade Solicitada
                                </label>
                                <input type="number" id="quantidade_solicitada" name="quantidade_solicitada" required
                                    min="1"
                                    class="w-full max-w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                    placeholder="Digite a quantidade">
                                <p class="text-sm text-gray-500 mt-1" id="estoque-info"></p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="action" value="criar"
                                class="bg-green-primary hover:bg-green-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center space-x-2">
                                <svg class="icon icon-paper-plane" viewBox="0 0 24 24">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                                </svg>
                                <span>Enviar Solicitação</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de Solicitações -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Solicitações Recentes</h2>
                    </div>

                    <?php if (empty($solicitacoes)): ?>
                        <div class="p-8 text-center">
                            <svg class="icon icon-clipboard-list text-6xl text-gray-300 mb-4 mx-auto" viewBox="0 0 24 24">
                                <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
                                <rect x="9" y="3" width="6" height="4" rx="2" ry="2"/>
                                <line x1="9" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="15" y2="16"/>
                                <line x1="9" y1="20" x2="15" y2="20"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-500 mb-2">Nenhuma solicitação encontrada</h3>
                            <p class="text-gray-400">Faça uma nova solicitação usando o formulário acima.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Item</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantidade</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Solicitante</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($solicitacoes as $solicitacao): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #<?php echo str_pad($solicitacao['id'], 3, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($solicitacao['item_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <?php echo $solicitacao['quantidade']; ?>
                                                    <?php echo htmlspecialchars($solicitacao['unidade'], ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo htmlspecialchars($solicitacao['usuario_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo date('d/m/Y', strtotime($solicitacao['data'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo getStatusClass($solicitacao['status']); ?>">
                                                    <?php echo ucfirst($solicitacao['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <?php if ($_SESSION['admin'] && $solicitacao['status'] === 'em espera'): ?>
                                                        <form method="POST" action="../../control/solicitacoesController.php"
                                                            class="inline">
                                                            <input type="hidden" name="id_mov"
                                                                value="<?php echo $solicitacao['id']; ?>">
                                                            <input type="hidden" name="action" value="aceitar">
                                                            <button type="submit" class="text-green-600 hover:text-green-900"
                                                                title="Aprovar">
                                                                <svg class="icon icon-check" viewBox="0 0 24 24">
                                                                    <polyline points="20,6 9,17 4,12"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="../../control/solicitacoesController.php"
                                                            class="inline">
                                                            <input type="hidden" name="id_mov"
                                                                value="<?php echo $solicitacao['id']; ?>">
                                                            <input type="hidden" name="action" value="recusar">
                                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                                title="Rejeitar">
                                                                <svg class="icon icon-times" viewBox="0 0 24 24">
                                                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <button class="text-blue-600 hover:text-blue-900" title="Ver detalhes">
                                                        <svg class="icon icon-eye" viewBox="0 0 24 24">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                    </button>
                                                </div>
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
        // Modal de Logout - Funções globais
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

        // Toggle da sidebar
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('-translate-x-full');
                    content.classList.toggle('ml-0');
                });
            }

            // Fechar modal ao clicar fora
            document.getElementById('logoutModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideLogoutModal();
                }
            });

            // Atualizar informações de estoque quando item for selecionado
            const selectItem = document.getElementById('id_item');
            const quantidadeInput = document.getElementById('quantidade_solicitada');
            const estoqueInfo = document.getElementById('estoque-info');

            selectItem.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const quantidade = selectedOption.dataset.quantidade;
                    const unidade = selectedOption.dataset.unidade;
                    estoqueInfo.textContent = `Estoque disponível: ${quantidade} ${unidade}`;
                    quantidadeInput.max = quantidade;
                } else {
                    estoqueInfo.textContent = '';
                    quantidadeInput.max = '';
                }
            });
        });
    </script>
</body>

</html>