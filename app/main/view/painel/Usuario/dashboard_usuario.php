<?php
session_start();
if (!isset($_SESSION ["usuariologado"])) {
header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuário - Almoxarifado</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
        }
        
        .user-welcome {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }
        
        .card-user {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .card-user:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .card-user .card-header {
            background: white;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            color: var(--dark-color);
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .stat-card-user {
            border-radius: 10px;
            padding: 20px;
            color: white;
            height: 100%;
            text-align: center;
        }
        
        .stat-card-user .stat-icon {
            font-size: 2.2rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stat-card-user .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 5px 0;
        }
        
        .stat-card-user .stat-title {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .bg-primary-user {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3c9de3 100%);
        }
        
        .bg-success-user {
            background: linear-gradient(135deg, var(--success-color) 0%, #2ecc71 100%);
        }
        
        .bg-warning-user {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f1c40f 100%);
        }
        
        .bg-info-user {
            background: linear-gradient(135deg, #00cec9 0%, #00a8a8 100%);
        }
        
        .quick-action {
            text-align: center;
            padding: 20px 15px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            height: 100%;
        }
        
        .quick-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .quick-action .action-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .quick-action .action-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .quick-action .action-desc {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .table-user {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-user th {
            background-color: #f8f9fa;
            color: var(--dark-color);
            font-weight: 600;
            padding: 12px 15px;
        }
        
        .table-user td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #eaeaea;
        }
        
        .badge-user {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .notification-item {
            padding: 15px;
            border-left: 4px solid var(--primary-color);
            background: white;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .notification-item.warning {
            border-left: 4px solid var(--warning-color);
        }
        
        .notification-item.urgent {
            border-left: 4px solid var(--accent-color);
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 40px;
            border-radius: 20px;
            border: 1px solid #ddd;
        }
        
        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .footer {
            background: white;
            padding: 20px 0;
            margin-top: 40px;
            border-top: 1px solid #eaeaea;
        }
        
        @media (max-width: 768px) {
            .user-avatar {
                width: 60px;
                height: 60px;
            }
            
            .user-welcome {
                text-align: center;
            }
            
            .user-info {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-warehouse me-2"></i>Almoxarifado
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-home me-1"></i> Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-box me-1"></i> Itens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-clipboard-list me-1"></i> Minhas Solicitações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-history me-1"></i> Histórico</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="search-box me-3">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Buscar itens..." style="width: 250px;">
                    </div>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-white text-decoration-none" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=&background=ffffff&color=3498db" class="user-avatar me-2">
                            <span id="nomeUsuario" class="d-none d-md-inline"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Meu Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configurações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container mt-4">

        <div class="user-welcome">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>Olá,<span id="nomeUsuario"><?= $nomeUsuario ?></span>!</h2>
                    <p class="mb-0">Bem-vindo(a) ao sistema de almoxarifado. Aqui você pode solicitar itens, acompanhar seus pedidos e verificar disponibilidade.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <img src="https://ui-avatars.com/api/?name=&background=ffffff&color=3498db" class="user-avatar">
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card-user bg-primary-user">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number">5</div>
                    <div class="stat-title">Solicitações Pendentes</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card-user bg-success-user">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">12</div>
                    <div class="stat-title">Solicitações Aprovadas</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card-user bg-warning-user">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-number">28</div>
                    <div class="stat-title">Itens Solicitados</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card-user bg-info-user">
                    <div class="stat-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stat-number">342</div>
                    <div class="stat-title">Itens Disponíveis</div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="row mb-4">
            <div class="col-lg-12 mb-3">
                <h4 class="mb-3">Ações Rápidas</h4>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Nova Solicitação</div>
                    <div class="action-desc">Solicite novos itens do almoxarifado</div>
                    <button class="btn btn-primary btn-sm">Acessar</button>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="action-title">Histórico</div>
                    <div class="action-desc">Consulte seu histórico de solicitações</div>
                    <button class="btn btn-outline-primary btn-sm">Acessar</button>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="action-title">Itens Disponíveis</div>
                    <div class="action-desc">Verifique os itens disponíveis no estoque</div>
                    <button class="btn btn-outline-primary btn-sm">Acessar</button> 
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="action-title">Ajuda</div>
                    <div class="action-desc">Tutorial e informações sobre o sistema</div>
                    <button class="btn btn-outline-primary btn-sm">Acessar</button>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="row">
            <!-- Minhas Solicitações Recentes -->
            <div class="col-lg-8 mb-4">
                <div class="card-user">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Minhas Solicitações Recentes</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-user">
                                <thead>
                                    <tr>
                                        <th>Nº Solicitação</th>
                                        <th>Data</th>
                                        <th>Itens</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#SOL-0087</td>
                                        <td>25/06/2023</td>
                                        <td>3 itens</td>
                                        <td><span class="badge bg-primary badge-user">Pendente</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#SOL-0086</td>
                                        <td>24/06/2023</td>
                                        <td>5 itens</td>
                                        <td><span class="badge bg-success badge-user">Aprovada</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#SOL-0085</td>
                                        <td>23/06/2023</td>
                                        <td>2 itens</td>
                                        <td><span class="badge bg-warning badge-user">Em Análise</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#SOL-0084</td>
                                        <td>20/06/2023</td>
                                        <td>1 item</td>
                                        <td><span class="badge bg-success badge-user">Entregue</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#SOL-0083</td>
                                        <td>18/06/2023</td>
                                        <td>4 itens</td>
                                        <td><span class="badge bg-danger badge-user">Rejeitada</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notificações e Avisos -->
            <div class="col-lg-4 mb-4">
                <div class="card-user">
                    <div class="card-header">
                        Notificações e Avisos
                    </div>
                    <div class="card-body">
                        <div class="notification-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">Nova atualização no sistema</h6>
                                <small>Hoje</small>
                            </div>
                            <p class="mb-0">O sistema de almoxarifado foi atualizado com novas funcionalidades.</p>
                        </div>
                        
                        <div class="notification-item warning">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">Solicitação 








