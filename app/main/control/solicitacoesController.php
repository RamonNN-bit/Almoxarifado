<?php
session_start();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se o usuário está logado
    if (!isset($_SESSION["usuariologado"])) {
        $_SESSION['erro'] = "Você precisa estar logado para fazer solicitações";
        header("Location: ../view/painel/Admin/solicitacoes.php");
        exit();
    }

    // Validar dados do formulário
    if (!empty($_POST['id_item']) && !empty($_POST['quantidade_solicitada'])) {
        $id_item = intval($_POST['id_item']);
        $quantidade_solicitada = intval($_POST['quantidade_solicitada']);
        $observacoes = trim($_POST['observacoes'] ?? '');
        $id_usuario = $_SESSION["usuariologado"]['id'];
        
        // Validação adicional
        if ($quantidade_solicitada > 0) {
            try {
                require_once(__DIR__ . '/../config/db.php');
                require_once(__DIR__ . '/../model/SolicitacoesModel.php');
                
                $solicitacoesModel = new Solicitacoes($pdo);
                $sucesso = $solicitacoesModel->criarSolicitacao($id_item, $quantidade_solicitada, $id_usuario, $observacoes);
                
                if ($sucesso) {
                    $_SESSION['mensagem_sucesso'] = "Solicitação criada com sucesso! Aguarde aprovação.";
                } else {
                    $_SESSION['erro'] = "Erro ao criar a solicitação";
                }
            } catch (Exception $e) {
                $_SESSION['erro'] = "Erro: " . $e->getMessage();
            }
        } else {
            $_SESSION['erro'] = "Quantidade deve ser maior que zero";
        }
    } else {
        $_SESSION['erro'] = "Todos os campos obrigatórios devem ser preenchidos";
    }
}

// Redirecionar de volta para a página de solicitações
header("Location: ../view/painel/Admin/solicitacoes.php");
exit();
?>

