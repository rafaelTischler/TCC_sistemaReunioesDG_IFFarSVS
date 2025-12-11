<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../controllers/PresencaController.php';

class AuthController
{
    private $usuarioModel;
    private $logModel;
    private $presencaController;

    //inicializa os models com a conexão do banco
    public function __construct($db)
    {
        $this->usuarioModel = new Usuario($db);
        $this->logModel = new Log($db);
        $this->presencaController = new PresencaController($db);
    }

    public function mostrarFormularioLogin()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function processarLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricula = $_POST['matricula'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $usuario = $this->usuarioModel->autenticar($matricula, $senha); //autentica o usuário com a matrícula SIAPE e a senha

            if ($usuario) {
                session_regenerate_id(true); //se logou, regenera a sessão do usuário
                //armazena os dados na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_matricula'] = $usuario['matricula_siape'];
                session_write_close();
                $this->logModel->registrar($usuario['id'], 'login', 'sistema'); //log login
                //verifica redirecionamento pendente para registro de presença (ao acessar o sistema pelo QR Code)
                if (isset($_SESSION['reuniao_id_para_registro'])) {
                    $reuniao_id = $_SESSION['reuniao_id_para_registro'];
                    unset($_SESSION['reuniao_id_para_registro']);
                    redirect('servidor/presenca/registrar?id=' . $reuniao_id);
                } else {
                    redirect(''); //página inicial
                }
            } else {
                $_SESSION['erro'] = "Matrícula ou senha incorretos.";
                redirect('login');
            }
        }
    }

    public function processarRedirecionarCadastro()
    {
        redirect('cadastrar');
    }

    public function processarLogout()
    {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if ($usuario_id) {
            $this->logModel->registrar($usuario_id, 'logout', 'sistema'); //log logout
        }
        session_destroy(); //destroi a sessão
        redirect('login'); //redireciona pro login
    }
}
