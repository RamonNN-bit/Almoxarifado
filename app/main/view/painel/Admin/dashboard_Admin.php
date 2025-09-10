<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Almoxarifado</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7f9;
            color: #333;
        }
        
        #wrapper {
            display: flex;
        }
        
        #sidebar {
            width: 250px;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            height: 100vh;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        #sidebar .sidebar-brand {
            padding: 1.5rem 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        #sidebar .sidebar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        
        #sidebar .sidebar-nav {
            padding: 0;
            list-style: none;
            margin-top: 20px;
        }
        
        #sidebar .sidebar-item {
            position: relative;
            margin: 5px 0;
        }
        
        #sidebar .sidebar-link {
            padding: 12px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        #sidebar .sidebar-link:hover, 
        #sidebar .sidebar-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--accent-color);
        }
        
        #sidebar .sidebar-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        #content {
            width: calc(100% - 250px);
            margin-left: 250px;
            min-height: 100vh;
        }
        
        .topbar {
            height: 70px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            background: white;
            padding: 0 20px;
        }
        
        .card-almox {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        
        .card-almox:hover {
            transform: translateY(-5px);
        }
        
        .card-almox .card-header {
            background: white;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            color: var(--primary-color);
            padding: 15px 20px;
            border-radius: 8px 8px 0 0 !important;
        }
        
        .stat-card {
            border-radius: 8px;
            padding: 20px;
            color: white;
            height: 100%;
        }
        
        .stat-card .stat-card-icon {
            font-size: 2.5rem;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .stat-card .stat-card-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 5px 0;
        }
        
        .stat-card .stat-card-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            opacity: 0.9;
            letter-spacing: 1px;
        }
        
        .bg-primary-almox {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3c5575 100%);
        }
        
        .bg-success-almox {
            background: linear-gradient(135deg, var(--success-color) 0%, #2ecc71 100%);
        }
        
        .bg-warning-almox {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f1c40f 100%);
        }
        
        .bg-danger-almox {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c0392b 100%);
        }
        
        .bg-info-almox {
            background: linear-gradient(135deg, var(--accent-color) 0%, #2980b9 100%);
        }
        
        .table-almox {
            width: 100%;
        }
        
        .table-almox th {
            background-color: #f8f9fa;
            color: var(--primary-color);
            font-weight: 600;
            padding: 12px 15px;
        }
        
        .table-almox td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .badge-almox {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .progress-almox {
            height: 8px;
            border-radius: 4px;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #eee;
        }
        
        .page-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            #content {
                width: 100%;
                margin-left: 0;
            }
            
            #content.active {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-warehouse"></i> Almoxarifado
            </div>
            
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link active">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-boxes"></i> Estoque
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-tools"></i> Materiais
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-clipboard-list"></i> Solicitações
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-truck-loading"></i> Entradas
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-external-link-alt"></i> Saídas
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i> Relatórios
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-cog"></i> Configurações
                    </a>
                </li>
                <li class="sidebar-item mt-4">
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </li>
            </ul>
        </div>

        <!-- Conteúdo Principal -->
        <div id="content">
            <!-- Topbar -->
            <nav class="topbar navbar navbar-expand navbar-light bg-white mb-4 static-top shadow">
                <div class="container-fluid">
                    <button id="sidebarToggle" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar item, material..." aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge bg-danger badge-counter">3</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge bg-danger badge-counter">2</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">João Silva</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=João+Silva&background=3498db&color=fff">
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Conteúdo do Dashboard -->
            <div class="container-fluid">
                <!-- Título da Página -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 page-title mb-0">Dashboard do Almoxarifado</h1>
                    <div>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> Exportar Relatório
                        </button>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Novo Item
                        </button>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card bg-primary-almox">
                            <div class="stat-card-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="stat-card-number">1,248</div>
                            <div class="stat-card-title">Itens em Estoque</div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card bg-success-almox">
                            <div class="stat-card-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="stat-card-number">42</div>
                            <div class="stat-card-title">Solicitações Hoje</div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card bg-warning-almox">
                            <div class="stat-card-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-card-number">18</div>
                            <div class="stat-card-title">Itens Críticos</div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card bg-danger-almox">
                            <div class="stat-card-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-card-number">7</div>
                            <div class="stat-card-title">Itens em Falta</div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos e Tabelas -->
                <div class="row">
                    <!-- Gráfico de Estoque -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card-almox">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Movimentação do Estoque</span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Últimos 30 dias
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="stockChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status de Solicitações -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card-almox">
                            <div class="card-header">
                                Status das Solicitações
                            </div>
                            <div class="card-body">
                                <canvas id="requestChart" height="250"></canvas>
                                <div class="mt-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary badge-almox me-2">42</span>
                                            <span>Pendentes</span>
                                        </div>
                                        <div class="fw-bold">48%</div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success badge-almox me-2">32</span>
                                            <span>Aprovadas</span>
                                        </div>
                                        <div class="fw-bold">36%</div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-danger badge-almox me-2">8</span>
                                            <span>Rejeitadas</span>
                                        </div>
                                        <div class="fw-bold">9%</div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-info badge-almox me-2">7</span>
                                            <span>Em análise</span>
                                        </div>
                                        <div class="fw-bold">8%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabelas de Itens e Solicitações -->
                <div class="row">
                    <!-- Itens com Estoque Crítico -->
                    <div class="col-xl-6 col-lg-6">
                        <div class="card-almox">
                            <div class="card-header">
                                Itens com Estoque Crítico
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-almox table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Categoria</th>
                                                <th>Estoque Atual</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Parafuso 5mm Aço Inox</td>
                                                <td>Fixadores</td>
                                                <td>12/100</td>
                                                <td><span class="badge bg-danger badge-almox">Crítico</span></td>
                                            </tr>
                                            <tr>
                                                <td>Fita Isolante Preta</td>
                                                <td>Elétrica</td>
                                                <td>3/50</td>
                                                <td><span class="badge bg-danger badge-almox">Crítico</span></td>
                                            </tr>
                                            <tr>
                                                <td>Resistor 10kΩ</td>
                                                <td>Componentes</td>
                                                <td>22/200</td>
                                                <td><span class="badge bg-warning badge-almox">Baixo</span></td>
                                            </tr>
                                            <tr>
                                                <td>Chave de Fenda Philips</td>
                                                <td>Ferramentas</td>
                                                <td>2/15</td>
                                                <td><span class="badge bg-danger badge-almox">Crítico</span></td>
                                            </tr>
                                            <tr>
                                                <td>Luvas de Proteção</td>
                                                <td>EPI</td>
                                                <td>8/40</td>
                                                <td><span class="badge bg-warning badge-almox">Baixo</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="#" class="btn btn-sm btn-outline-primary">Ver todos os itens</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Últimas Solicitações -->
                    <div class="col-xl-6 col-lg-6">
                        <div class="card-almox">
                            <div class="card-header">
                                Últimas Solicitações
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-almox table-hover">
                                        <thead>
                                            <tr>
                                                <th>Requisição</th>
                                                <th>Solicitante</th>
                                                <th>Data</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#REQ-0042</td>
                                                <td>Carlos Oliveira</td>
                                                <td>20/06/2023</td>
                                                <td><span class="badge bg-primary badge-almox">Pendente</span></td>
                                            </tr>
                                            <tr>
                                                <td>#REQ-0041</td>
                                                <td>Maria Santos</td>
                                                <td>20/06/2023</td>
                                                <td><span class="badge bg-success badge-almox">Aprovada</span></td>
                                            </tr>
                                            <tr>
                                                <td>#REQ-0040</td>
                                                <td>Pedro Alves</td>
                                                <td>19/06/2023</td>
                                                <td><span class="badge bg-danger badge-almox">Rejeitada</span></td>
                                            </tr>
                                            <tr>
                                                <td>#REQ-0039</td>
                                                <td>Ana Costa</td>
                                                <td>19/06/2023</td>
                                                <td><span class="badge bg-success badge-almox">Aprovada</span></td>
                                            </tr>
                                            <tr>
                                                <td>#REQ-0038</td>
                                                <td>João Silva</td>
                                                <td>18/06/2023</td>
                                                <td><span class="badge bg-info badge-almox">Em análise</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="#" class="btn btn-sm btn-outline-primary">Ver todas as solicitações</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <footer class="py-3 mt-5">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Sistema de Almoxarifado 2023</div>
                            <div>
                                <a href="#">Política de Privacidade</a> &middot;
                                <a href="#">Termos de Uso</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Bootstrap & ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Script para toggle da sidebar em dispositivos móveis
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
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
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Saídas',
                    data: [100, 105, 110, 120, 125, 130, 135],
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
        
        // Gráfico de status de solicitações
        const requestCtx = document.getElementById('requestChart').getContext('2d');
        const requestChart = new Chart(requestCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pendentes', 'Aprovadas', 'Rejeitadas', 'Em análise'],
                datasets: [{
                    data: [42, 32, 8, 7],
                    backgroundColor: [
                        '#3498db',
                        '#27ae60',
                        '#e74c3c',
                        '#2980b9'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>







