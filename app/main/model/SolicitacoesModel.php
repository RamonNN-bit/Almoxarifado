<?php
require_once __DIR__ . '/../config/db.php';

class Solicitacoes {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Criar nova solicitação
    public function criarSolicitacao($id_item, $quantidade_solicitada, $id_usuario, $observacoes = '') {
        try {
            $this->pdo->beginTransaction();
            
            // Verificar se há estoque suficiente
            $sql_estoque = "SELECT quantidade FROM itens WHERE id = :id_item";
            $stmt_estoque = $this->pdo->prepare($sql_estoque);
            $stmt_estoque->execute([':id_item' => $id_item]);
            $item = $stmt_estoque->fetch(PDO::FETCH_ASSOC);
            
            if (!$item) {
                throw new Exception("Item não encontrado");
            }
            
            if ($item['quantidade'] < $quantidade_solicitada) {
                throw new Exception("Quantidade solicitada maior que o estoque disponível");
            }
            
            // Criar solicitação
            $sql_solicitacao = "INSERT INTO movimentacoes (id_item, tipo, quantidade, data, id_usuario, observacoes) 
                               VALUES (:id_item, 'saida', :quantidade, CURDATE(), :id_usuario, :observacoes)";
            
            $stmt_solicitacao = $this->pdo->prepare($sql_solicitacao);
            $resultado = $stmt_solicitacao->execute([
                ':id_item' => $id_item,
                ':quantidade' => $quantidade_solicitada,
                ':id_usuario' => $id_usuario,
                ':observacoes' => $observacoes
            ]);
            
            if ($resultado) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    // Buscar todas as solicitações com informações dos itens
    public function buscarTodasSolicitacoes() {
        $sql = "SELECT m.*, i.nome as item_nome, i.unidade, u.nome as usuario_nome 
                FROM movimentacoes m 
                INNER JOIN itens i ON m.id_item = i.id 
                INNER JOIN usuario u ON m.id_usuario = u.id 
                WHERE m.tipo = 'saida'
                ORDER BY m.data DESC, m.id DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar solicitações de um usuário específico
    public function buscarSolicitacoesUsuario($id_usuario) {
        $sql = "SELECT m.*, i.nome as item_nome, i.unidade 
                FROM movimentacoes m 
                INNER JOIN itens i ON m.id_item = i.id 
                WHERE m.tipo = 'saida' AND m.id_usuario = :id_usuario
                ORDER BY m.data DESC, m.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aprovar solicitação (atualizar estoque)
    public function aprovarSolicitacao($id_solicitacao) {
        try {
            $this->pdo->beginTransaction();
            
            // Buscar dados da solicitação
            $sql_buscar = "SELECT * FROM movimentacoes WHERE id = :id";
            $stmt_buscar = $this->pdo->prepare($sql_buscar);
            $stmt_buscar->execute([':id' => $id_solicitacao]);
            $solicitacao = $stmt_buscar->fetch(PDO::FETCH_ASSOC);
            
            if (!$solicitacao) {
                throw new Exception("Solicitação não encontrada");
            }
            
            // Atualizar estoque
            $sql_estoque = "UPDATE itens SET quantidade = quantidade - :quantidade WHERE id = :id_item";
            $stmt_estoque = $this->pdo->prepare($sql_estoque);
            $resultado_estoque = $stmt_estoque->execute([
                ':quantidade' => $solicitacao['quantidade'],
                ':id_item' => $solicitacao['id_item']
            ]);
            
            if ($resultado_estoque) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }

    // Rejeitar solicitação
    public function rejeitarSolicitacao($id_solicitacao) {
        $sql = "DELETE FROM movimentacoes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id_solicitacao]);
    }
}
?>

