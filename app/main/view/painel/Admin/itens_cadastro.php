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
    <link rel="icon" href="../../../assets/images/brasao.png" type="image/x-icon">
    <link rel="stylesheet" href="../../../assets/css/icons.css">
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
                    <img src="../../../assets/images/brasao.png" alt="Brasão" class="w-8 h-8 mr-3 object-contain">
                    <span class="text-xl font-bold">Almoxarifado</span>
                </div>
            </div>

            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard_Admin.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-home w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../estoque.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
                            <svg class="icon icon-boxes w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                            <span>Estoque</span>
                        </a>
                    </li>
                    <li>
                        <a href="itens_cadastro.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <svg class="icon icon-tools w-5 mr-3" viewBox="0 0 24 24">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                            <span>Cadastrar Itens</span>
                        </a>
                    </li>
                    <li>
                        <a href="../solicitacoes.php"
                            class="flex items-center px-6 py-3 text-white font-semibold hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200">
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Cadastro de Itens</h1>
                    <p class="text-gray-600">Cadastre novos itens no sistema de almoxarifado</p>
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

                <?php if (isset($erros) && !empty($erros)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center mb-2">
                            <svg class="icon icon-exclamation-triangle mr-2" viewBox="0 0 24 24">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
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
                                    <svg class="icon icon-tag mr-2" viewBox="0 0 24 24">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                                    </svg>Nome do Item
                                </label>
                                <input type="text" id="nome" name="nome" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                    placeholder="Digite o nome do item"
                                    value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>

                            <div>
                                <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="icon icon-hash mr-2" viewBox="0 0 24 24">
                                        <line x1="4" y1="9" x2="20" y2="9"/>
                                        <line x1="4" y1="15" x2="20" y2="15"/>
                                        <line x1="10" y1="3" x2="8" y2="21"/>
                                        <line x1="16" y1="3" x2="14" y2="21"/>
                                    </svg>Quantidade
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
                                    <svg class="icon icon-ruler mr-2" viewBox="0 0 24 24">
                                        <path d="M21.3 8.7l-5.6-5.6c-.4-.4-1-.4-1.4 0L2.7 14.3c-.4.4-.4 1 0 1.4l5.6 5.6c.4.4 1 .4 1.4 0L21.3 10.1c.4-.4.4-1 0-1.4z"/>
                                        <line x1="14.5" y1="9.5" x2="19.5" y2="14.5"/>
                                        <line x1="16.5" y1="11.5" x2="17.5" y2="12.5"/>
                                        <line x1="10.5" y1="5.5" x2="11.5" y2="6.5"/>
                                        <line x1="7.5" y1="8.5" x2="8.5" y2="9.5"/>
                                        <line x1="4.5" y1="11.5" x2="5.5" y2="12.5"/>
                                    </svg>Unidade
                                </label>
                                <select id="unidade" name="unidade" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors bg-white">
                                    <option value="">Selecione uma unidade</option>
                                    <option value="unidades" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'unidades') ? 'selected' : ''; ?>>Unidades</option>
                                    <option value="kg" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'kg') ? 'selected' : ''; ?>>Quilogramas (kg)</option>
                                    <option value="g" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'g') ? 'selected' : ''; ?>>Gramas (g)</option>
                                    <option value="litros" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'litros') ? 'selected' : ''; ?>>Litros</option>
                                    <option value="ml" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'ml') ? 'selected' : ''; ?>>Mililitros (ml)</option>
                                    <option value="metros" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'metros') ? 'selected' : ''; ?>>Metros (m)</option>
                                    <option value="cm" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'cm') ? 'selected' : ''; ?>>Centímetros (cm)</option>
                                    <option value="mm" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'mm') ? 'selected' : ''; ?>>Milímetros (mm)</option>
                                    <option value="caixas" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'caixas') ? 'selected' : ''; ?>>Caixas</option>
                                    <option value="pacotes" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'pacotes') ? 'selected' : ''; ?>>Pacotes</option>
                                    <option value="frascos" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'frascos') ? 'selected' : ''; ?>>Frascos</option>
                                    <option value="tubos" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'tubos') ? 'selected' : ''; ?>>Tubos</option>
                                    <option value="pares" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'pares') ? 'selected' : ''; ?>>Pares</option>
                                    <option value="conjuntos" <?php echo (isset($_POST['unidade']) && $_POST['unidade'] === 'conjuntos') ? 'selected' : ''; ?>>Conjuntos</option>
                                </select>
                            </div>

                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="icon icon-building mr-2" viewBox="0 0 24 24">
                                        <path d="M3 21h18"/>
                                        <path d="M5 21V7l8-4v18"/>
                                        <path d="M19 21V11l-6-4"/>
                                        <path d="M9 9v.01"/>
                                        <path d="M9 12v.01"/>
                                        <path d="M9 15v.01"/>
                                        <path d="M9 18v.01"/>
                                    </svg>Marca
                                </label>
                                <input type="text" id="marca" name="marca"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                    placeholder="Marca do produto (opcional)"
                                    value="<?php echo isset($_POST['marca']) ? htmlspecialchars($_POST['marca'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                        </div>

                        <div>
                            <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="icon icon-box mr-2" viewBox="0 0 24 24">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>Modelo
                            </label>
                            <input type="text" id="modelo" name="modelo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-primary focus:border-transparent transition-colors"
                                placeholder="Modelo do produto (opcional)"
                                value="<?php echo isset($_POST['modelo']) ? htmlspecialchars($_POST['modelo'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-green-primary hover:bg-green-secondary text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center">
                                <svg class="icon icon-plus mr-2" viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>Cadastrar Item
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
                                <svg class="icon icon-plus mr-2" viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>Adicionar ao Estoque
                            </button>
                        </div>
                    </form>
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
                    <a href="../../../view/logout.php" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-center">
                        Sim, Sair
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
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
    </script>
</body>

</html>