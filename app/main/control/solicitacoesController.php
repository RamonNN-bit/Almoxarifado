<?php

require_once '../config/auth.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se o usuário está logado
    if (!isLoggedIn()) {
        $_SESSION['erro'] = "Você precisa estar logado para realizar esta ação";
        header("Location: ../view/painel/Admin/solicitacoes.php");
        exit();
    }

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../model/SolicitacoesModel.php');
    
    $solicitacoesModel = new Solicitacoes($pdo);
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'criar') {
            // Verificar se o usuário tem permissão para criar solicitações
            $id_item = intval($_POST['id_item']);
            $quantidade_solicitada = intval($_POST['quantidade_solicitada']);
            $id_usuario = $_SESSION["usuariologado"]['id'];
            if (!empty($_POST['id_usuario']))
            {
                $id_usuario_s = $_POST['id_usuario'];
            } else {
                $id_usuario_s = $_SESSION["usuariologado"]['id'];
            }
            
            
            if (!$id_item || !$quantidade_solicitada || $quantidade_solicitada <= 0 || !$id_usuario_s ) {
                throw new Exception("Todos os campos obrigatórios devem ser preenchidos corretamente");
            }
            
            $sucesso = $solicitacoesModel->criarSolicitacao($id_item, $quantidade_solicitada, $id_usuario, $id_usuario_s);
            
            if ($sucesso) {
                $_SESSION['mensagem_sucesso'] = "Solicitação criada com sucesso! Aguarde aprovação.";
            } else {
                $_SESSION['erro'] = "Erro ao criar a solicitação";
            }
        } elseif ($action === 'aceitar' || $action === 'recusar') {
            // Verificar se o usuário é administrador
            if ($_SESSION["usuariologado"]['TIPO'] !== 'admin') {
                throw new Exception("Acesso negado: apenas administradores podem aprovar ou rejeitar solicitações");
            }
            
            $id_solicitacao = intval($_POST['id_mov']);
            if (!$id_solicitacao) {
                throw new Exception("ID da solicitação inválido");
            }
            
            if ($action === 'aceitar') {
                $sucesso = $solicitacoesModel->aprovarSolicitacao($id_solicitacao);
                if ($sucesso) {
                    $_SESSION['mensagem_sucesso'] = "Solicitação aprovada com sucesso!";
                } else {
                    $_SESSION['erro'] = "Erro ao aprovar a solicitação";
                }
            } elseif ($action === 'recusar') {
                $sucesso = $solicitacoesModel->rejeitarSolicitacao($id_solicitacao);
                if ($sucesso) {
                    $_SESSION['mensagem_sucesso'] = "Solicitação rejeitada com sucesso!";
                } else {
                    $_SESSION['erro'] = "Erro ao rejeitar a solicitação";
                }
            }
        } else {
            throw new Exception("Ação inválida");
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
    }
}

// Redirecionar de volta para a página de solicitações
header("Location: ../view/painel/solicitacoes.php");
exit();

?>