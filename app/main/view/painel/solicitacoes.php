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
    <title>Solicitações - Almoxarifado</title>
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
                        <a href="./admin/dashboard_Admin.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-home w-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="estoque.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-boxes w-5 mr-3"></i>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <?php if($_SESSION['admin']){?>
                    <li>
                        <a href="./admin/itens_cadastro.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <?php }?>
                    <li>
                        <a href="solicitacoes.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <i class="fas fa-clipboard-list w-5 mr-3"></i>
                            <span>Solicitações</span>
                        </a>
                    </li>
                    <?php if($_SESSION['admin']){?>
                    <li>
                        <a href="./admin/usuarios.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-users w-5 mr-3"></i>
                            <span>Usuários</span>
                        </a>
                    </li>
                    <?php }?>
                    <li class="mt-8">
                        <a href="../logout.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-red-600 hover:bg-opacity-20 transition-all duration-200">
                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                            <span>Sair</span>
                        </a>
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
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $pendentesCount; ?></span>
                            </button>
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
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Solicitações de Retirada</h1>
                    <p class="text-gray-600">Gerencie as solicitações de retirada de itens do almoxarifado</p>
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

                <!-- Cards de estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card-gradient-1 rounded-xl p-6 text-white card-hover">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-clipboard-list text-xl"></i>
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
                                <i class="fas fa-clock text-xl"></i>
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
                                <i class="fas fa-check-circle text-xl"></i>
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
                                <i class="fas fa-times-circle text-xl"></i>
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
                                    <i class="fas fa-box mr-2 text-gray-500"></i> Selecionar Item
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
                                    <i class="fas fa-hashtag mr-2 text-gray-500"></i> Quantidade Solicitada
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
                                <i class="fas fa-paper-plane"></i>
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
                            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
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
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="../../control/solicitacoesController.php"
                                                            class="inline">
                                                            <input type="hidden" name="id_mov"
                                                                value="<?php echo $solicitacao['id']; ?>">
                                                            <input type="hidden" name="action" value="recusar">
                                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                                title="Rejeitar">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <button class="text-blue-600 hover:text-blue-900" title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
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

    <!-- Scripts JavaScript -->
    <script>
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