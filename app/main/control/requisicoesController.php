<?php
namespace App\Main\Control;

class Usuario {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    }
}
class Itens {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM itens")->fetchAll(PDO::FETCH_ASSOC);
    }
}

class movimentacoes {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM movimentacoes")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
