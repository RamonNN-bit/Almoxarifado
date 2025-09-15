<?php


require_once '../model/ItensModel.php';

class ItensController {                 
    private $itensModel;

    public function __construct(PDO $pdo) {
        $this->itensModel = new Itens($pdo);
    }

    public function listar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405); 
            die('Método HTTP não permitido');
        }

        try {
            $itens = $this->itensModel->buscarTodosItens();
        } catch (Exception $e) {
            error_log('Erro ao listar itens: ' . $e->getMessage());
            http_response_code(500);
        }
    }

    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
            $unidade = filter_input(INPUT_POST, 'unidade', FILTER_SANITIZE_STRING);
            $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
            $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);

            if (!$nome || !$quantidade || !$unidade || !$marca || !$modelo) {
                $erro = 'Todos os campos são obrigatórios e devem ser válidos.';
                return;
            }

            try {
                if ($this->itensModel->criar($nome, $quantidade, $unidade, $marca, $modelo)) {
                    header('Location: ../painel/Admin/itens_cadastro.php?sucesso=Item criado com sucesso');
                    exit;
                } else {
                    $erro = 'Falha ao criar o item. Tente novamente.';
                }
            } catch (Exception $e) {
                error_log('Erro ao criar item: ' . $e->getMessage());
                $erro = 'Erro interno ao criar o item. Contate o administrador.';
            }
        } else {
            
        }
    }
}
    
?>

