<?php
namespace App\Model;

use PDO;
use PDOException;

class Movimentacao {
    private $pdo;

    public function __construct() {
        try {

            $this->pdo = new PDO('mysql:host=localhost;almoxarifado', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public function registrar($id_item, $tipo, $quantidade, $data, $id_usuario) {
        try {
            if (empty($id_item) || empty($tipo) || empty($quantidade) || empty($data) || empty($id_usuario)) {
                return false;
            }

            
            $tipo = mb_strtolower($tipo);
            $tipo = str_replace('á', 'a', $tipo);

            if (!in_array($tipo, ['entrada', 'saida'])) {
                return false;
            }

            if ($quantidade <= 0) {
                return false;
            }

            $sql = "INSERT INTO movimentacoes (id_item, tipo, quantidade, id_usuario, created_at) 
                    VALUES (:id_item, :tipo, :quantidade, :id_usuario, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_item', $id_item, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao registrar movimentação: " . $e->getMessage());
            return false;
        }
    }
}
?>
