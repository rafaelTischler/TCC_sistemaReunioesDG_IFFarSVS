<?php
require_once __DIR__ . '/../../includes/config.php';

class Reuniao
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function criar($titulo, $descricao, $data_reuniao, $local_reuniao, $criado_por)
    {
        $stmt = $this->conn->prepare("INSERT INTO reunioes (titulo, descricao, data_reuniao, local_reuniao, criado_por) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $titulo, $descricao, $data_reuniao, $local_reuniao, $criado_por);
        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return false;
        }
    }

    public function atualizarQrcode($id, $qrcode_filename)
    {
        $update_stmt = $this->conn->prepare("UPDATE reunioes SET qrcode_file = ? WHERE id = ?");
        $update_stmt->bind_param("si", $qrcode_filename, $id);
        return $update_stmt->execute();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("
            SELECT r.*, u.nome as criador 
            FROM reunioes r
            JOIN usuarios u ON r.criado_por = u.id
            WHERE r.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function contarPresencas($reuniao_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM presencas WHERE reuniao_id = ?");
        $stmt->bind_param("i", $reuniao_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'];
    }

    public function atualizar($id, $titulo, $descricao, $data_reuniao, $local_reuniao)
    {
        $stmt = $this->conn->prepare("UPDATE reunioes SET titulo = ?, descricao = ?, data_reuniao = ?, local_reuniao = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $titulo, $descricao, $data_reuniao, $local_reuniao, $id);
        return $stmt->execute();
    }

    public function listarComCriador()
    {
        $query = "
            SELECT r.*, u.nome as criador 
            FROM reunioes r
            JOIN usuarios u ON r.criado_por = u.id
            ORDER BY r.data_reuniao DESC
        ";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM reunioes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscarPorQrcodeToken($token)
    {
        $stmt = $this->conn->prepare("SELECT id FROM reunioes WHERE qrcode_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function countAllReunioes()
    {
        $db = getDB();
        $result = $db->query("SELECT COUNT(*) FROM reunioes");
        return $result->fetch_row()[0];
    }

    public function contarTotal()
    {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM reunioes");
        return $result->fetch_assoc()['total'];
    }

    public function contarRealizadas()
    {
        $agora = date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM reunioes WHERE data_reuniao <= ?");
        $stmt->bind_param("s", $agora);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'];
    }

    public function getUltima()
    {
        $query = "
        SELECT r.*, u.nome as criador
        FROM reunioes r
        JOIN usuarios u ON r.criado_por = u.id
        ORDER BY r.data_reuniao DESC
        LIMIT 1
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function listarProximas($limite = 5)
    {
        $agora = date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("SELECT titulo, data_reuniao FROM reunioes WHERE data_reuniao > ? ORDER BY data_reuniao ASC LIMIT ?");
        $stmt->bind_param("si", $agora, $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function existeReuniaoComMesmoNome($titulo, $id_excluir = 0)
    {
        $stmt = $this->conn->prepare("SELECT id FROM reunioes WHERE titulo = ? AND id != ?");
        $stmt->bind_param("si", $titulo, $id_excluir);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function existeReuniaoMesmaDataLocal($data_reuniao, $local_reuniao, $id_excluir = 0)
    {
        $stmt = $this->conn->prepare("SELECT id FROM reunioes WHERE data_reuniao = ? AND local_reuniao = ? AND id != ?");
        $stmt->bind_param("ssi", $data_reuniao, $local_reuniao, $id_excluir);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}