<?php

class Declaracao
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //insere uma declaração no banco de dados
    public function inserir($usuario_id, $emitido_por_id, $conteudo, $data_inicio, $data_fim)
    {
        $stmt = $this->conn->prepare("INSERT INTO declaracoes (usuario_id, emitido_por, conteudo, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $usuario_id, $emitido_por_id, $conteudo, $data_inicio, $data_fim);
        return $stmt->execute();
    }

    //buscar declarações por servidor via id
    public function buscarPorServidor($usuario_id)
    {
        $stmt = $this->conn->prepare("
            SELECT d.*, u1.nome as nome_servidor, u2.nome as nome_emissor 
            FROM declaracoes d 
            LEFT JOIN usuarios u1 ON d.usuario_id = u1.id 
            LEFT JOIN usuarios u2 ON d.emitido_por = u2.id 
            WHERE d.usuario_id = ? 
            ORDER BY d.created_at DESC
        ");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Buscar declaração específica por ID
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("
            SELECT d.*, u1.nome as nome_servidor, u2.nome as nome_emissor 
            FROM declaracoes d 
            LEFT JOIN usuarios u1 ON d.usuario_id = u1.id 
            LEFT JOIN usuarios u2 ON d.emitido_por = u2.id 
            WHERE d.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    //busca as estatísticas de declarações por servidor
    public function estatisticasPorServidor($usuario_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_declaracoes,
                MIN(created_at) as primeira_emissao,
                MAX(created_at) as ultima_emissao
            FROM declaracoes 
            WHERE usuario_id = ?
        ");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    //busca todas as declarações com paginação (para Administradores)
    public function buscarTodas($pagina = 1, $limite = 10)
    {
        $offset = ($pagina - 1) * $limite;
        
        $stmt = $this->conn->prepare("
            SELECT d.*, u1.nome as nome_servidor, u2.nome as nome_emissor 
            FROM declaracoes d 
            LEFT JOIN usuarios u1 ON d.usuario_id = u1.id 
            LEFT JOIN usuarios u2 ON d.emitido_por = u2.id 
            ORDER BY d.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limite, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //contar total de declarações para paginação
    public function contarTotal()
    {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM declaracoes");
        return $result->fetch_assoc()['total'];
    }
}