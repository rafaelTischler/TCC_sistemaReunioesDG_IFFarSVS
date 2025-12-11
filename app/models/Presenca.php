<?php
require_once __DIR__ . '/../../includes/config.php';

class Presenca
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function presencaJaRegistrada($usuario_id, $reuniao_id) //verifica se o usuário já registrou presença na reunião
    {
        $stmt = $this->conn->prepare("SELECT id FROM presencas WHERE usuario_id = ? AND reuniao_id = ?");
        $stmt->bind_param("ii", $usuario_id, $reuniao_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function registrar($usuario_id, $reuniao_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO presencas (usuario_id, reuniao_id, data_presenca) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $usuario_id, $reuniao_id);
        return $stmt->execute();
    }

    public function listarPresencasDoUsuario($usuario_id)
    {
        $stmt = $this->conn->prepare("
            SELECT p.data_presenca, r.titulo, r.data_reuniao, r.local_reuniao
            FROM presencas p
            JOIN reunioes r ON p.reuniao_id = r.id
            WHERE p.usuario_id = ?
            ORDER BY r.data_reuniao DESC
        ");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPresencasPorPeriodo($usuario_id, $data_inicio, $data_fim) //busca presenças dentro de um período específico (usado nas declarações)
    {
        $stmt = $this->conn->prepare("
            SELECT r.titulo, r.data_reuniao 
            FROM reunioes r
            JOIN presencas p ON r.id = p.reuniao_id
            WHERE p.usuario_id = ? 
            AND r.data_reuniao BETWEEN ? AND ? 
            ORDER BY r.data_reuniao
        ");
        $stmt->bind_param("iss", $usuario_id, $data_inicio, $data_fim);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function contarTotalPorReuniao($reuniao_id) //conta quantas presenças já foram registradas em uma reunião
    {
        $query = "SELECT COUNT(*) as total FROM presencas WHERE reuniao_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $reuniao_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public static function getPresencasPorMes($conn) //estatisticas de presença por mês para o gráfico
    {
        $anoAtual = date('Y');

        $stmt = $conn->prepare("
        SELECT MONTH(data_presenca) AS mes, COUNT(*) AS total
        FROM presencas
        WHERE YEAR(data_presenca) = ?
        GROUP BY mes
        ORDER BY mes
    ");
        $stmt->bind_param("i", $anoAtual);
        $stmt->execute();
        $result = $stmt->get_result();

        $meses = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $presencasPorMes = array_fill_keys(array_values($meses), 0);

        while ($row = $result->fetch_assoc()) {
            $mesNome = $meses[(int)$row['mes']];
            $presencasPorMes[$mesNome] = (int)$row['total'];
        }

        return $presencasPorMes;
    }

    public static function countByUsuario($conn, $usuario_id)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM presencas WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'];
    }

    public static function getUltimaPresencaUsuario($conn, $usuario_id)
    {
        $stmt = $conn->prepare("SELECT data_presenca FROM presencas WHERE usuario_id = ? ORDER BY data_presenca DESC LIMIT 1");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function contarPorUsuario($usuario_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM presencas WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'];
    }

    public function contarUltimaReuniao()
    {
        $query = "SELECT id FROM reunioes ORDER BY data_reuniao DESC LIMIT 1";
        $result = $this->conn->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return $this->contarTotalPorReuniao($row['id']);
        }
        return 0;
    }

    public function contarPorMes()
    {
        $anoAtual = date('Y');
        $stmt = $this->conn->prepare("
            SELECT MONTH(data_presenca) AS mes, COUNT(*) AS total
            FROM presencas
            WHERE YEAR(data_presenca) = ?
            GROUP BY mes
            ORDER BY mes
        ");
        $stmt->bind_param("i", $anoAtual);
        $stmt->execute();
        $result = $stmt->get_result();

        $meses = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $presencasPorMes = array_fill_keys(array_values($meses), 0);

        while ($row = $result->fetch_assoc()) {
            $mesNome = $meses[(int)$row['mes']];
            $presencasPorMes[$mesNome] = (int)$row['total'];
        }

        return $presencasPorMes;
    }

    public function buscarUltimaPorUsuario($usuario_id)
    {
        $stmt = $this->conn->prepare("SELECT data_presenca FROM presencas WHERE usuario_id = ? ORDER BY data_presenca DESC LIMIT 1");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function obterEstatisticasPorServidor($search)
    {
        //verifica se é uma busca por ID específico
        $isIdSearch = is_numeric($search) && strlen($search) < 5;

        $sql = "SELECT 
            u.nome, 
            u.matricula_siape as siape, 
            u.cargo,
            (SELECT COUNT(*) FROM reunioes) as total_reunioes,
            COUNT(p.id) as presencas,
            ((SELECT COUNT(*) FROM reunioes) - COUNT(p.id)) as faltas,
            CASE 
                WHEN (SELECT COUNT(*) FROM reunioes) > 0 
                THEN ROUND((COUNT(p.id) / (SELECT COUNT(*) FROM reunioes)) * 100, 2)
                ELSE 0 
            END as percentual_presenca
        FROM usuarios u
        LEFT JOIN presencas p ON u.id = p.usuario_id
        WHERE " . ($isIdSearch ? "u.id = ?" : "u.nome LIKE ? OR u.matricula_siape LIKE ?") . "
        GROUP BY u.id, u.nome, u.matricula_siape, u.cargo";

        $stmt = $this->conn->prepare($sql);

        if ($isIdSearch) {
            $stmt->bind_param("i", $search);
        } else {
            $searchParam = "%$search%";
            $stmt->bind_param("ss", $searchParam, $searchParam);
        }

        $stmt->execute();
        return $stmt->get_result();
    }
}
