<?php

class Log
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listarTodos()
    {
        $stmt = $this->conn->prepare("SELECT id, usuario_id, acao, modulo, data_log FROM logs ORDER BY data_log DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function registrar($usuario_id, $acao, $modulo, $dados = null)
    {
        $dados = $dados ? json_encode($dados) : null;
        $stmt = $this->conn->prepare("INSERT INTO logs (usuario_id, acao, modulo, dados) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $acao, $modulo, $dados);
        $stmt->execute();
        $stmt->close();
    }
}
