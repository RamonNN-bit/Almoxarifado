<?php
require_once __DIR__ . '/../config/db.php';
// Garantir que a sessão esteja iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
class Itens {
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    // Buscar todos os itens no banco de dados
    public function buscarTodosItens() {
        $sql = "SELECT * FROM itens ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar item por ID
    public function buscarItemPorId($id_item) {
        $sql = "SELECT * FROM itens WHERE id = :id_item";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_item' => $id_item]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Criar novo item no banco de dados
    public function criar($nome, $quantidade, $unidade, $marca, $modelo) {
        // Converter unidade para string se necessário, já que na tabela está definido como INT
        $unidade_valor = is_numeric($unidade) ? (int)$unidade : $unidade;
        
        $sql = "INSERT INTO itens (nome, quantidade, unidade, marca, modelo) 
                VALUES (:nome, :quantidade, :unidade, :marca, :modelo)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nome' => $nome,
            ':quantidade' => $quantidade,
            ':unidade' => $unidade_valor,
            ':marca' => $marca,
            ':modelo' => $modelo
        ]);
    }

    // Incrementar quantidade existente de um item
    public function incrementarQuantidade(int $id_item, int $quantidadeAdicionar): bool {
        if ($id_item <= 0 || $quantidadeAdicionar <= 0) {
            return false;
        }
    
        if (!isset($_SESSION['id'])) {
            return false; // Usuário não está logado
        }
    
        $this->pdo->beginTransaction();
        try {
            // Verificar se o item existe
            $checkSql = "SELECT id FROM itens WHERE id = :id";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([':id' => $id_item]);
            
            if ($checkStmt->rowCount() === 0) {
                // Item não existe
                $this->pdo->rollBack();
                error_log("Erro ao incrementar quantidade: Item não existe");
                return false;
            }
            
            // Atualiza a quantidade na tabela itens
            $sql = "UPDATE itens SET quantidade = quantidade + :qtd WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':qtd' => $quantidadeAdicionar, ':id' => $id_item]);
            
            if ($stmt->rowCount() === 0) {
                // Nenhuma linha foi atualizada
                $this->pdo->rollBack();
                error_log("Erro ao incrementar quantidade: Nenhuma linha foi atualizada");
                return false;
            }
    
            // Insere a movimentação na tabela movimentacoes
            // Verificando a estrutura da tabela movimentacoes no banco.sql
            // Campos: id, id_item, tipo, quantidade, data, timestamp, status, id_usuario
            $sqlInsert = "INSERT INTO movimentacoes (id_item, tipo, quantidade, id_usuario, status, data) 
                          VALUES (:id_item, 'entrada', :quantidade, :id_usuario, 'em espera', CURDATE())";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            
            try {
                // Log para diagnóstico antes da execução
                error_log("Tentando inserir movimentação: ID Item=$id_item, Quantidade=$quantidadeAdicionar, ID Usuário={$_SESSION['id']}");
                
                $result = $stmtInsert->execute([
                    ':id_item' => $id_item,
                    ':quantidade' => $quantidadeAdicionar,
                    ':id_usuario' => $_SESSION['id']
                ]);
                
                // Registrar o erro específico se houver
                if (!$result) {
                    $errorInfo = $stmtInsert->errorInfo();
                    error_log("Erro SQL ao inserir movimentação: " . json_encode($errorInfo));
                } else {
                    error_log("Movimentação inserida com sucesso. ID da movimentação: " . $this->pdo->lastInsertId());
                }
            } catch (PDOException $ex) {
                error_log("Exceção ao inserir movimentação: " . $ex->getMessage());
                $result = false;
            }
            
            if (!$result) {
                // Erro ao inserir na tabela de movimentações
                $this->pdo->rollBack();
                error_log("Erro ao incrementar quantidade: Falha ao inserir movimentação");
                return false;
            }
    
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao incrementar quantidade: " . $e->getMessage());
            return false;
        }
    }
}
?>
