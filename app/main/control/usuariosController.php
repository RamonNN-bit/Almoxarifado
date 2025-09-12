<?php

// Debug temporário (remova em produção)
file_put_contents('/tmp/debug_usuarios.log', "Sessão:\n" . print_r($_SESSION, true), FILE_APPEND);
file_put_contents('/tmp/debug_usuarios.log', "POST:\n" . print_r($_POST, true), FILE_APPEND);

require_once '../config/auth.php';
session_start();
// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se o usuário está logado e é admin
    if (!isLoggedIn()) {
        $_SESSION['erro'] = "Você precisa estar logado para realizar esta ação";
        header("Location: ../../view/painel/Admin/usuarios.php");
        exit();
    }

    if (!isset($_SESSION['TIPO']) || $_SESSION['TIPO'] !== 'admin') {
        $_SESSION['erro'] = "Acesso negado: apenas administradores podem gerenciar usuários";
        header("Location: ../view/painel/Admin/usuarios.php");
        exit();
    }

    // Protege os includes
    try {
        require_once('../config/db.php');
        require_once('../model/UsuariosModel.php');
    } catch (Throwable $e) {
        $_SESSION['erro'] = "Erro ao carregar dependências: " . $e->getMessage();
        header("Location: ../view/painel/Admin/usuarios.php");
        exit();
    }

    $usuariosModel = new UsuariosModel($pdo);
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'alterar_tipo') {
            $id_usuario = intval($_POST['id_usuario'] ?? 0);
            $novo_tipo = $_POST['novo_tipo'] ?? '';
            $id_usuario_logado = $_SESSION['id'] ?? null;

            if (!$id_usuario || !$novo_tipo) {
                throw new Exception("Dados inválidos");
            }

            // Verificar se o usuário pode alterar a si mesmo
            if (!$usuariosModel->podeAlterarUsuario($id_usuario_logado, $id_usuario)) {
                throw new Exception("Você não pode alterar seu próprio tipo de usuário");
            }

            $usuariosModel->atualizarTipoUsuario($id_usuario, $novo_tipo);

            // Se chegou até aqui, a operação foi bem-sucedida
            $tipo_descricao = $novo_tipo === 'admin' ? 'Administrador' : 'Usuário';
            $_SESSION['mensagem_sucesso'] = "Tipo do usuário alterado para {$tipo_descricao} com sucesso!";
        } else {
            throw new Exception("Ação inválida: " . $action);
        }
    } catch (Throwable $e) {
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
    }
}

// Redirecionar de volta para a página de usuários
header("Location: ../../view/painel/Admin/usuarios.php");
exit();
?>