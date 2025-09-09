<?php
namespace App\Main\Control;

use App\Model\Movimentacao;

class MovimentacaoController {

    // Exibe a view de registro da movimentação
    public function index() {
        // Certifique-se que o caminho da view está correto
        $viewPath = __DIR__ . '../view/registrarmovimentacao.php'; 
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "Erro: View não encontrada!";
        }
    }

    // Método para registrar movimentação, recebendo parâmetros
    public function registrar($id_item, $tipo, $quantidade, $data, $id_usuario) {
        // Validação simples
        if (empty($id_item) || empty($tipo) || empty($quantidade) || empty($data) || empty($id_usuario)) {
            echo "Erro: Todos os campos são obrigatórios!";
            return;
        }

        // Normaliza e limpa o tipo para evitar problemas com acentos
        $tipo = mb_strtolower($tipo);
        $tipo = str_replace('á', 'a', $tipo);

        if (!in_array($tipo, ['entrada', 'saida'])) {
            echo "Erro: Tipo deve ser 'entrada' ou 'saida'!";
            return;
        }

        if (!is_numeric($quantidade) || $quantidade <= 0) {
            echo "Erro: Quantidade deve ser maior que zero!";
            return;
        }

        // Evita erros passando apenas dados válidos
        $id_item = (int) $id_item;
        $quantidade = (int) $quantidade;
        $id_usuario = (int) $id_usuario;

        try {
            $movimentacao = new Movimentacao();
            $resultado = $movimentacao->registrar($id_item, $tipo, $quantidade, $data, $id_usuario);

            if ($resultado) {
                echo "Movimentação de " . $tipo . " registrada com sucesso!";
            } else {
                echo "Erro ao registrar a movimentação.";
            }
        } catch (\Exception $e) {
            // Exibe o erro para debugging (apenas em desenvolvimento)
            echo "Erro inesperado: " . $e->getMessage();
        }
    }
}
?>
