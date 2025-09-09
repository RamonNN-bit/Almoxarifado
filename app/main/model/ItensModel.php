<?php
require_once '..includes/db.php';
class Itens {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Buscar todos os itens no banco de dados
    public function buscarTodosItens() {
        $sql = "SELECT * FROM itens ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
}
?>
