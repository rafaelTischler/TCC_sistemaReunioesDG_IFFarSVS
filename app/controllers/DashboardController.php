<?php

require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../models/Reuniao.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Presenca.php';

class DashboardController
{
    private $db;

    public function __construct()
    {
        global $conn; //conexão com o banco de dados
        $this->db = $conn;
    }

    //exibe o dashboard   
    public function index()
    {
        Auth::verificaLogin();

        $reuniaoModel = new Reuniao($this->db);
        $usuarioModel = new Usuario($this->db);
        $presencaModel = new Presenca($this->db);
        $usuario_id = $_SESSION['usuario_id'];

        //dados básicos para todos os usuários
        $presencas_servidor = $presencaModel->contarPorUsuario($usuario_id);
        $total_reunioes_realizadas = $reuniaoModel->contarRealizadas();
        $ausencias_servidor = $total_reunioes_realizadas - $presencas_servidor;
        $taxa_participacao = $total_reunioes_realizadas > 0
            ? round(($presencas_servidor / $total_reunioes_realizadas) * 100, 1)
            : 0;
        $ultima_presenca = $presencaModel->buscarUltimaPorUsuario($usuario_id);
        $proximas_reunioes = $reuniaoModel->listarProximas(5);

        //dados para usuários Gestores e Administradores
        if (Auth::isAdminOrGestor()) {
            $total_reunioes = $reuniaoModel->contarTotal();
            $presencas_ultima_reuniao = $presencaModel->contarUltimaReuniao();
            $presencas_por_mes = $presencaModel->contarPorMes();
        }

        //dados exclusivos para Administradores
        if (Auth::isAdmin()) {
            $total_usuarios = $usuarioModel->contarTotal();
        }

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
