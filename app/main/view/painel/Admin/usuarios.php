<?php
require_once '../../../config/auth.php';
print_r($_SESSION);
// Verificar se é admin e redirecionar se necessário
requireLogin('admin', 'usuarios.php');

require_once '../../../config/db.php';
require_once '../../../model/UsuariosModel.php';



$usuariosModel = new UsuariosModel($pdo);
$usuarios = $usuariosModel->buscarTodosUsuarios();
$estatisticas = $usuariosModel->buscarEstatisticasUsuarios();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Almoxarifado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-admin {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-usuario {
            background-color: #dbeafe;
            color: #1e40af;
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
                    <li>
                        <a href="usuarios.php"
                            class="flex items-center px-6 py-3 text-green-100 hover:text-white hover:bg-green-light hover:bg-opacity-20 transition-all duration-200 sidebar-link-active">
                            <i class="fas fa-users w-5 mr-3"></i>
                            <span>Usuários</span>
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
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Gerenciar Usuários</h1>
                            <p class="text-gray-600 mt-1">Gerencie os usuários do sistema e suas permissões</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                Logado como: <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-6 mt-4" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-check-circle mr-2"></i>
                        </div>
                        <div>
                            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['mensagem_sucesso']); ?></span>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-6 mt-4" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                        </div>
                        <div>
                            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['erro']); ?></span>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['erro']); ?>
            <?php endif; ?>

            <!-- Estatísticas -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total de Usuários</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estatisticas['total_usuarios']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-shield text-yellow-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Administradores</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estatisticas['total_admins']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-green-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Usuários Normais</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estatisticas['total_usuarios_normais']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Usuários -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Lista de Usuários</h3>
                        <p class="text-sm text-gray-500 mt-1">Clique em "Alterar" para modificar o tipo de usuário</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Atual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($usuario['id']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($usuario['nome']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($usuario['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?php echo $usuario['TIPO'] === 'admin' ? 'status-admin' : 'status-usuario'; ?>">
                                                <?php echo htmlspecialchars($usuario['tipo_descricao']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <?php if ($usuario['id'] != $_SESSION['id']): ?>
                                                <form method="POST" action="../../../control/usuariosController.php" class="inline">
                                                    <input type="hidden" name="action" value="alterar_tipo">
                                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                                    <select name="novo_tipo" class="text-sm border border-gray-300 rounded px-2 py-1 mr-2" onchange="this.form.submit()">
                                                        <option value="">Alterar para...</option>
                                                        <option value="admin" <?php echo $usuario['TIPO'] === 'admin' ? 'disabled' : ''; ?>>Administrador</option>
                                                        <option value="usuario" <?php echo $usuario['TIPO'] === 'usuario' ? 'disabled' : ''; ?>>Usuário</option>
                                                    </select>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-sm">Você mesmo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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

    <!-- Script para confirmação -->
    <script>
        // Adicionar confirmação antes de alterar tipo de usuário
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('select[name="novo_tipo"]');
            selects.forEach(select => {
                select.addEventListener('change', function(e) {
                    if (e.target.value) {
                        const nomeUsuario = e.target.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
                        const novoTipo = e.target.value === 'admin' ? 'Administrador' : 'Usuário';
                        
                        if (confirm(`Tem certeza que deseja alterar ${nomeUsuario} para ${novoTipo}?`)) {
                            e.target.form.submit();
                        } else {
                            e.target.value = '';
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
