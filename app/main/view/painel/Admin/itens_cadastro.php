<?php
require_once '../../../config/auth.php';

// Verificar se é admin e redirecionar se necessário
requireLogin(null, 'itens_cadastro.php');

// Incluir o controller para processar o formulário
?>
<?php
// Conexão e modelo para listar/atualizar itens
require_once '../../../config/db.php';
require_once '../../../model/ItensModel.php';
require_once('../../../model/SolicitacoesModel.php');

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
$pendentesCount = 0;
try {
    $__allSolic = $solicitacoesModel->buscarTodasSolicitacoes();
    foreach ($__allSolic as $__s) { if ((($__s['status']) ?? '') === 'em espera') { $pendentesCount++; } }
} catch (Exception $e) { $pendentesCount = 0; }
$erros = [];
$itens = [];

try {
    $itensModel = new Itens($pdo);

    // Incrementar quantidade (adicionar itens a um item existente)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'incrementar') {
        $idItem = (int) ($_POST['id_item'] ?? 0);
        $qtdAdd = (int) ($_POST['quantidade'] ?? 0);
        if ($idItem > 0 && $qtdAdd > 0) {
            if ($itensModel->incrementarQuantidade($idItem, $qtdAdd)) {
                $_SESSION['mensagem_sucesso'] = 'Quantidade adicionada com sucesso!';
                header('Location: itens_cadastro.php');
                exit;
            } else {
                $erros[] = 'Não foi possível adicionar a quantidade.';
            }
        } else {
            $erros[] = 'Selecione um item e informe uma quantidade válida.';
        }
    }

    // Buscar itens para listar e popular o select
    $itens = $itensModel->buscarTodosItens();
} catch (Exception $e) {
    $erros[] = 'Erro ao carregar itens: ' . $e->getMessage();
}
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
        <div id="sidebar"
            class="w-64 sidebar-gradient text-white h-screen fixed transition-all duration-300 z-50 shadow-xl">
            <div class="p-6 text-center border-b border-green-light border-opacity-20 bg-black bg-opacity-10">
                <div class="flex items-center justify-center">
                    <i class="fas fa-warehouse text-2xl mr-3"></i>
                    <span class="text-xl font-bold">Almoxarifado</span>
                </div>
            </div>

            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard_Admin.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
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
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
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
                    <form method="POST" action="../../../control/criarController.php" class="space-y-6">
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
                            <button type="submit"
                                class="bg-green-primary hover:bg-green-secondary text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center">
                                <i class="fas fa-plus mr-2"></i>Cadastrar Item
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Adicionar quantidade a item existente -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Adicionar Itens ao Estoque</h2>
                    <form method="POST" action="../../../control/movimentacaoController.php" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        <input type="hidden" name="acao" value="incrementar">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selecionar Item</label>
                            <select name="id_item"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent"
                                required>
                                <option value="">-- Escolha um item --</option>
                                <?php foreach ($itens as $it): ?>
                                    <option value="<?php echo $it['id']; ?>">
                                        #<?php echo str_pad($it['id'], 3, '0', STR_PAD_LEFT); ?> -
                                        <?php echo htmlspecialchars($it['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        (<?php echo (int) $it['quantidade']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade a adicionar</label>
                            <input type="number" name="quantidade" min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent"
                                placeholder="Ex: 10" required>
                        </div>
                        <div class="flex justify-end md:justify-start w-full">
                            <button type="submit"
                                class="w-full md:w-auto bg-green-primary hover:bg-green-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>Adicionar ao Estoque
                            </button>
                        </div>
                    </form>
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
        });
    </script>
</body>

</html>