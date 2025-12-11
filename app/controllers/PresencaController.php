<?php

require_once __DIR__ . '/../models/Presenca.php';
require_once __DIR__ . '/../models/Reuniao.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../../includes/funcoes.php';

class PresencaController
{
    private $presencaModel;
    private $reuniaoModel;
    private $usuarioModel;
    private $logModel;

    public function __construct($db)
    {
        $this->presencaModel = new Presenca($db);
        $this->reuniaoModel = new Reuniao($db);
        $this->usuarioModel = new Usuario($db);
        $this->logModel = new Log($db);
    }

    public function registrarPresenca()
    {
        if (!isset($_GET['id'])) { //verifica se o id da reunião não foi passado
            $_SESSION['registro_status'] = 'erro';
            $_SESSION['registro_mensagem'] = "Reunião não especificada.";
        } else {
            $reuniao_id = intval($_GET['id']); //converte pra int pra garantir

            //busca informações da reunião
            $reuniao = $this->reuniaoModel->buscarPorId($reuniao_id);

            if (!$reuniao) { //verifica se a reunião existe
                $_SESSION['registro_status'] = 'erro';
                $_SESSION['registro_mensagem'] = "Reunião não encontrada.";
            } elseif (empty($reuniao['data_reuniao'])) { //verifica se a data/hora da reunião está definida
                $_SESSION['registro_status'] = 'erro';
                $_SESSION['registro_mensagem'] = "A reunião não possui data/hora definida.";
            } else {

                $data_hora_reuniao = strtotime($reuniao['data_reuniao']); //calcula se ainda está no prazo de registro
                $data_hora_atual = time(); //pega a hora atual
                $prazo_maximo = $data_hora_reuniao + 14400; //define o prazo máximo de 4 horas (14400 segundos) após a reunião

                if ($data_hora_atual < $data_hora_reuniao) { //verifica se a reunião ainda não começou
                    $_SESSION['registro_status'] = 'erro';
                    $_SESSION['registro_mensagem'] = "A reunião ainda não começou. Registro disponível a partir de " . date('d/m/Y H:i', $data_hora_reuniao);
                } elseif ($data_hora_atual > $prazo_maximo) { //verifica se passou do prazo
                    $_SESSION['registro_status'] = 'erro';
                    $_SESSION['registro_mensagem'] = "Prazo para registro expirado. O registro só era permitido até " . date('d/m/Y H:i', $prazo_maximo);
                } elseif (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] <= 0) { //testa se o usuário está logado
                    $_SESSION['reuniao_id_para_registro'] = $reuniao_id; //se não estiver logado, guarda o id da reunião pra registrar após o login
                    redirect('login');
                    return;
                } elseif ($this->presencaModel->presencaJaRegistrada($_SESSION['usuario_id'], $reuniao_id)) { //verifica se a presença já existe
                    $_SESSION['registro_status'] = 'ja_registrado';
                    $_SESSION['registro_mensagem'] = "Você já registrou presença nesta reunião.";
                } elseif ($this->presencaModel->registrar($_SESSION['usuario_id'], $reuniao_id)) { //registra a presença
                    $_SESSION['registro_status'] = 'registrado';
                    $_SESSION['registro_mensagem'] = "Presença registrada com sucesso na reunião!";
                    $this->logModel->registrar($_SESSION['usuario_id'], 'presença', 'Reunião: ' . $reuniao_id);
                } else {
                    $_SESSION['registro_status'] = 'erro';
                    $_SESSION['registro_mensagem'] = "Erro ao registrar presença. Tente novamente.";
                }
            }
        }
        require_once __DIR__ . '/../views/presenca/registrar.php'; //tela de confirmação de presença 
    }

    public function listarPresencas()
    {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) {
            $_SESSION['erro'] = "Você precisa estar logado para ver suas presenças.";
            redirect('login');
            return;
        }
        $presencas = $this->presencaModel->listarPresencasDoUsuario($usuario_id); //busca todas as presenças do usuário
        require_once __DIR__ . '/../views/presenca/listar.php';
    }

    public function mostrarEstatisticas() //mostra as estatísticas de presença dos Servidores apenas pra Administradores e Gestores
    {
        if (!Auth::isAdminOrGestor()) {
            header("Location: " . BASE_URL);
            exit;
        }

        $servidores = $this->usuarioModel->listarTodos(); //carrega os usuários pro autocompletar
        $resultados = null;
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            $resultados = $this->presencaModel->obterEstatisticasPorServidor($search); //busca as estatísticas
        }

        require_once dirname(__DIR__) . '/views/presenca/estatisticas_servidor.php'; //passa os dados do Servidor pra tela de estatísticas 
    }
}
