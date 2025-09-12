<?php
require_once '../../../config/db.php';

class UsuariosModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Buscar todos os usuários
    public function buscarTodosUsuarios() {
        try {
            $sql = "SELECT id, nome, email, TIPO, 
                           CASE 
                               WHEN TIPO = 'admin' THEN 'Administrador'
                               WHEN TIPO = 'usuario' THEN 'Usuário'
                               ELSE 'Indefinido'
                           END as tipo_descricao
                    FROM usuario 
                    ORDER BY nome ASC";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar usuários: " . $e->getMessage());
        }
    }

    // Buscar usuário por ID
    public function buscarUsuarioPorId($id) {
        try {
            $sql = "SELECT id, nome, email, TIPO FROM usuario WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar usuário: " . $e->getMessage());
        }
    }

    // Atualizar tipo de usuário
    public function atualizarTipoUsuario($id, $novo_tipo) {
        try {
            // Validar se o tipo é válido
            if (!in_array($novo_tipo, ['admin', 'usuario'])) {
                throw new Exception("Tipo de usuário inválido");
            }

            // Verificar se o usuário existe
            $usuario = $this->buscarUsuarioPorId($id);
            if (!$usuario) {
                throw new Exception("Usuário não encontrado");
            }

            // Atualizar o tipo
            $sql = "UPDATE usuario SET TIPO = :tipo WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':tipo' => $novo_tipo,
                ':id' => $id
            ]);

            if ($resultado && $stmt->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Erro ao atualizar tipo do usuário");
            }
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }

    // Buscar estatísticas de usuários
    public function buscarEstatisticasUsuarios() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_usuarios,
                        SUM(CASE WHEN TIPO = 'admin' THEN 1 ELSE 0 END) as total_admins,
                        SUM(CASE WHEN TIPO = 'usuario' THEN 1 ELSE 0 END) as total_usuarios_normais
                    FROM usuario";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar estatísticas: " . $e->getMessage());
        }
    }

    // Verificar se usuário pode ser alterado (não pode alterar a si mesmo)
    public function podeAlterarUsuario($id_usuario_logado, $id_usuario_alterar) {
        return $id_usuario_logado != $id_usuario_alterar;
    }
}
?>
