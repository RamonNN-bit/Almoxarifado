<?php
require_once __DIR__ . '/../config/db.php';

class Solicitacoes {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Criar nova solicitação
    public function criarSolicitacao($id_item, $quantidade_solicitada, $id_usuario) {
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
            
            // Criar solicitação com status 'em espera'
            $sql_solicitacao = "INSERT INTO movimentacoes (id_item, tipo, status, quantidade, data, id_usuario) 
                               VALUES (:id_item, 'saida', 'em espera', :quantidade, CURDATE(), :id_usuario)";
            
            $stmt_solicitacao = $this->pdo->prepare($sql_solicitacao);
            $resultado = $stmt_solicitacao->execute([
                ':id_item' => $id_item,
                ':quantidade' => $quantidade_solicitada,
                ':id_usuario' => $id_usuario
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
                WHERE m.tipo = 'saida' AND m.id_usuario = :id_usuario AND m.status = 'em espera'
                ORDER BY m.data DESC, m.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aprovar solicitação (atualizar estoque e status)
    public function aprovarSolicitacao($id_solicitacao) {
        try {
            $this->pdo->beginTransaction();
            
            // Buscar dados da solicitação
            $sql_buscar = "SELECT id_item, quantidade, status FROM movimentacoes WHERE id = :id AND status = 'em espera'";
            $stmt_buscar = $this->pdo->prepare($sql_buscar);
            $stmt_buscar->execute([':id' => $id_solicitacao]);
            $solicitacao = $stmt_buscar->fetch(PDO::FETCH_ASSOC);
            
            if (!$solicitacao) {
                throw new Exception("Solicitação não encontrada ou já processada");
            }
            
            // Verificar estoque
            $sql_estoque = "SELECT quantidade FROM itens WHERE id = :id_item";
            $stmt_estoque = $this->pdo->prepare($sql_estoque);
            $stmt_estoque->execute([':id_item' => $solicitacao['id_item']]);
            $item = $stmt_estoque->fetch(PDO::FETCH_ASSOC);
            
            if ($item['quantidade'] < $solicitacao['quantidade']) {
                throw new Exception("Estoque insuficiente para aprovar a solicitação");
            }
            
            // Atualizar estoque
            $sql_estoque = "UPDATE itens SET quantidade = quantidade - :quantidade WHERE id = :id_item";
            $stmt_estoque = $this->pdo->prepare($sql_estoque);
            $stmt_estoque->execute([
                ':quantidade' => $solicitacao['quantidade'],
                ':id_item' => $solicitacao['id_item']
            ]);
            
            // Atualizar status para 'aprovado' (alinhado com a tabela)
            $sql_status = "UPDATE movimentacoes SET status = 'aprovado' WHERE id = :id";
            $stmt_status = $this->pdo->prepare($sql_status);
            $resultado = $stmt_status->execute([':id' => $id_solicitacao]);
            
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

    // Rejeitar solicitação (atualizar status)
    public function rejeitarSolicitacao($id_solicitacao) {
        try {
            $sql = "UPDATE movimentacoes SET status = 'recusado' WHERE id = :id AND status = 'em espera'";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([':id' => $id_solicitacao]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Solicitação não encontrada ou já processada");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Buscar estatísticas para o dashboard do usuário
    public function buscarEstatisticasUsuario($id_usuario) {
        try {
            // Solicitações pendentes (em espera)
            $sql_pendentes = "SELECT COUNT(*) as total FROM movimentacoes 
                             WHERE tipo = 'saida' AND id_usuario = :id_usuario AND status = 'em espera'";
            $stmt_pendentes = $this->pdo->prepare($sql_pendentes);
            $stmt_pendentes->execute([':id_usuario' => $id_usuario]);
            $pendentes = $stmt_pendentes->fetch(PDO::FETCH_ASSOC)['total'];

            // Solicitações aprovadas
            $sql_aprovadas = "SELECT COUNT(*) as total FROM movimentacoes 
                             WHERE tipo = 'saida' AND id_usuario = :id_usuario AND status = 'aprovado'";
            $stmt_aprovadas = $this->pdo->prepare($sql_aprovadas);
            $stmt_aprovadas->execute([':id_usuario' => $id_usuario]);
            $aprovadas = $stmt_aprovadas->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de itens solicitados pelo usuário
            $sql_itens_solicitados = "SELECT SUM(quantidade) as total FROM movimentacoes 
                                     WHERE tipo = 'saida' AND id_usuario = :id_usuario";
            $stmt_itens_solicitados = $this->pdo->prepare($sql_itens_solicitados);
            $stmt_itens_solicitados->execute([':id_usuario' => $id_usuario]);
            $itens_solicitados = $stmt_itens_solicitados->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Total de itens disponíveis no estoque
            $sql_itens_disponiveis = "SELECT SUM(quantidade) as total FROM itens";
            $stmt_itens_disponiveis = $this->pdo->query($sql_itens_disponiveis);
            $itens_disponiveis = $stmt_itens_disponiveis->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            return [
                'solicitacoes_pendentes' => (int)$pendentes,
                'solicitacoes_aprovadas' => (int)$aprovadas,
                'itens_solicitados' => (int)$itens_solicitados,
                'itens_disponiveis' => (int)$itens_disponiveis
            ];

        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Buscar solicitações recentes do usuário para o dashboard
    public function buscarSolicitacoesRecentesUsuario($id_usuario, $limite = 5) {
        try {
            $sql = "SELECT m.*, i.nome as item_nome, i.unidade 
                    FROM movimentacoes m 
                    INNER JOIN itens i ON m.id_item = i.id 
                    WHERE m.tipo = 'saida' AND m.id_usuario = :id_usuario
                    ORDER BY m.data DESC, m.id DESC
                    LIMIT :limite";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
?>