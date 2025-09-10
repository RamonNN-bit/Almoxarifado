<?php
session_start();
if (!isset($_SESSION["usuariologado"])) {
header("Location: ../../view/index.php");
}

// Incluir o controller para processar o formulário
require_once '../../../config/db.php';
require_once '../../../model/ItensModel.php';

// Buscar todos os itens para exibir na tabela
try {
    $itensModel = new Itens($pdo);
    $itens = $itensModel->buscarTodosItens();
} catch (Exception $e) {
    $erros[] = "Erro ao buscar itens: " . $e->getMessage();
    $itens = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Itens</title>
    <style>
    body{
        font-family: Arial, sans-serif;
        height: 100vh;
        margin: 0;
        padding: 20px;
        background-color: #f5f5f5;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    h1, h2{
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }
    
    .form-container {
        background-color: #f9f9f9;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid #ddd;
    }
    
    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    label {
        font-weight: bold;
        color: #555;
        font-size: 14px;
    }
    
    input {
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }
    
    input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }
    
    .btn {
        font-size: 16px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: bold;
        margin-top: 10px;
    }

    .btn:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .alert {
        padding: 15px;
        margin: 15px 0;
        border-radius: 8px;
        font-weight: bold;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .alert ul {
        margin: 10px 0 0 0;
        padding-left: 20px;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    
    th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }
    
    tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    tbody tr:hover {
        background-color: #e3f2fd;
        transition: background-color 0.3s ease;
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cadastro de Itens</h1>
        
        <!-- Exibir mensagens de sucesso ou erro -->
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8'); ?>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['erro'], ENT_QUOTES, 'UTF-8'); ?>
                <?php unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($erros) && !empty($erros)): ?>
            <div class="alert alert-error">
                <strong>Erro(s) encontrado(s):</strong>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="../../control/criarController.php">
                <div class="form-group">
                    <label for="nome">Nome do Item:</label>
                    <input type="text" id="nome" name="nome" required 
                           value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" required min="1"
                           value="<?php echo isset($_POST['quantidade']) ? htmlspecialchars($_POST['quantidade'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="unidade">Unidade:</label>
                    <input type="text" id="unidade" name="unidade" required placeholder="Ex: kg, litros, unidades"
                           value="<?php echo isset($_POST['unidade']) ? htmlspecialchars($_POST['unidade'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" placeholder="Opcional"
                           value="<?php echo isset($_POST['marca']) ? htmlspecialchars($_POST['marca'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" placeholder="Opcional"
                           value="<?php echo isset($_POST['modelo']) ? htmlspecialchars($_POST['modelo'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>
                
                <button type="submit" class="btn">Cadastrar Item</button>
            </form>
        </div>

        <h2>Itens Cadastrados</h2>
        
        <?php if (empty($itens)): ?>
            <div class="alert alert-info">
                <p>Nenhum item encontrado. Cadastre o primeiro item usando o formulário acima.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Quantidade</th>
                            <th>Unidade</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['quantidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['unidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['marca'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['modelo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div> 
