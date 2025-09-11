<?php
require_once __DIR__ . '/../config/db.php';
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
        $sql = "INSERT INTO itens (nome, quantidade, unidade, marca, modelo) 
                VALUES (:nome, :quantidade, :unidade, :marca, :modelo)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nome' => $nome,
            ':quantidade' => $quantidade,
            ':unidade' => $unidade,
            ':marca' => $marca,
            ':modelo' => $modelo
        ]);
    }

    // Incrementar quantidade existente de um item
    public function incrementarQuantidade(int $id_item, int $quantidadeAdicionar): bool {
        if ($id_item <= 0 || $quantidadeAdicionar <= 0) {
            return false;
        }
        $sql = "UPDATE itens SET quantidade = quantidade + :qtd WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':qtd' => $quantidadeAdicionar, ':id' => $id_item]);
    }
}
?>
