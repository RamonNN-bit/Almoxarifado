<?php
// Garantir que a sessão esteja iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir o modelo de itens e a conexão com o banco de dados
require_once __DIR__ . '/../model/ItensModel.php';
require_once __DIR__ . '/../config/db.php';

// Verificar se a conexão com o banco de dados foi estabelecida
if (!isset($pdo) || !($pdo instanceof PDO)) {
    $_SESSION['erro'] = 'Erro de conexão com o banco de dados.';
    header('Location: ../view/painel/Admin/itens_cadastro.php');
    exit;
}

// Verificar se é uma requisição POST para incrementar quantidade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'incrementar') {
    $idItem = (int) ($_POST['id_item'] ?? 0);
    $qtdAdd = (int) ($_POST['quantidade'] ?? 0);
    
    if ($idItem > 0 && $qtdAdd > 0) {
        try {
            $itensModel = new Itens($pdo);
            if ($itensModel->incrementarQuantidade($idItem, $qtdAdd)) {
                $_SESSION['mensagem_sucesso'] = 'Quantidade adicionada com sucesso!';
            } else {
                // Registrar o erro em log para diagnóstico
                error_log("Falha ao incrementar quantidade do item ID: $idItem. Quantidade: $qtdAdd");
                $_SESSION['erro'] = 'Não foi possível adicionar a quantidade. Verifique os logs para mais detalhes.';
                
                // Verificar se o item existe
                $checkItem = $pdo->prepare("SELECT id FROM itens WHERE id = ?");
                $checkItem->execute([$idItem]);
                if ($checkItem->rowCount() === 0) {
                    error_log("Item ID $idItem não encontrado no banco de dados");
                    $_SESSION['erro'] = 'Item não encontrado no banco de dados.';
                }
            }
        } catch (\Exception $e) {
            // Registrar a exceção em log
            error_log("Exceção ao incrementar quantidade: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao processar a solicitação: ' . $e->getMessage();
        }
    } else {
        $_SESSION['erro'] = 'Selecione um item e informe uma quantidade válida.';
    }
    
    // Redirecionar de volta para a página de cadastro
    header('Location: ../view/painel/Admin/itens_cadastro.php');
    exit;
}

// Fim do arquivo
?>
