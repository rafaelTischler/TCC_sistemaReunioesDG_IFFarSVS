<?php
require_once __DIR__ . '/../../includes/config.php';

class Usuario
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function autenticar($matricula, $senha)
    {
        $stmt = $this->conn->prepare("SELECT id, nome, matricula_siape, senha, cargo, tipo_usuario FROM usuarios WHERE matricula_siape = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                unset($usuario['senha']);
                return $usuario;
            }
        }
        return null;
    }

    public function existePorMatricula($matricula)
    {
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE matricula_siape = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function existePorEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function cadastrar($matricula, $nome, $email, $senha, $cargo, $tipo_usuario)
    {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Use hash para senhas!
        $stmt = $this->conn->prepare("INSERT INTO usuarios (matricula_siape, nome, email, senha, cargo, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $matricula, $nome, $email, $senha_hash, $cargo, $tipo_usuario);
        return $stmt->execute();
    }

    public function buscarPorMatricula($matricula)
    {
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE matricula_siape = ? LIMIT 1");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

  public function atualizar($id, $matricula, $nome, $email, $cargo, $tipo_usuario, $novaSenha = null)
{
    if (!empty($novaSenha)) {
        $senha_hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE usuarios SET matricula_siape = ?, nome = ?, email = ?, senha = ?, cargo = ?, tipo_usuario = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $matricula, $nome, $email, $senha_hash, $cargo, $tipo_usuario, $id);
    } else {
        $stmt = $this->conn->prepare("UPDATE usuarios SET matricula_siape = ?, nome = ?, email = ?, cargo = ?, tipo_usuario = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $matricula, $nome, $email, $cargo, $tipo_usuario, $id);
    }
    return $stmt->execute();
}

    public function listarTodos()
    {
        $result = $this->conn->query("SELECT * FROM usuarios ORDER BY nome");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscarDetalhesPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT nome, matricula_siape, cargo FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function countAllUsuarios()
    {
        $db = getDB();
        $result = $db->query("SELECT COUNT(*) FROM usuarios");
        return $result->fetch_row()[0];
    }

    public function contarTotal()
    {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM usuarios");
        return $result->fetch_assoc()['total'];
    }
}
