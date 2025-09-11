<?php
require_once '../config/auth.php';

// Verificar se é admin para criar itens
requireLogin('admin', 'criarController.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!empty($_POST['nome']) && !empty($_POST['quantidade']) && !empty($_POST['unidade'])) {
        $nome = trim($_POST['nome']);
        $quantidade = intval($_POST['quantidade']);
        $unidade = trim($_POST['unidade']);
        $marca = trim($_POST['marca'] ?? '');
        $modelo = trim($_POST['modelo'] ?? '');
        
        // Validação adicional
        if ($quantidade > 0) {
            try {
                require_once(__DIR__ . '/../config/db.php');
                require_once(__DIR__ . '/../model/ItensModel.php');
                
                $itensModel = new Itens($pdo);

                
                $sucesso = $itensModel->criar($nome, $quantidade, $unidade, $marca, $modelo);
                
                if ($sucesso) {
                    $_SESSION['mensagem_sucesso'] = "Item cadastrado com sucesso!";
                } else {
                    $_SESSION['erro'] = "Erro ao cadastrar o item";
                }
            } catch (Exception $e) {
                $_SESSION['erro'] = "Erro no banco de dados: " . $e->getMessage();
            }
        } else {
            $_SESSION['erro'] = "Quantidade deve ser maior que zero";
        }
    } else {
        $_SESSION['erro'] = "Nome, quantidade e unidade são obrigatórios";
    }
}

// Redirecionar de volta para a página de cadastro
header("Location: ../view/painel/Admin/itens_cadastro.php");
exit();
?>