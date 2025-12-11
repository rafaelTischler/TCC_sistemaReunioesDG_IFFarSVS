<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../includes/funcoes.php';

class UsuarioController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Usuario($db);
    }

    public function mostrarFormularioCadastro()
    {
        //estrutura de cargos por categoria
        $cargos_por_categoria = [
            'Docente' => [
                'Professor'
            ],

            'Tae - Técnico-Administrativos Em Educação' => [
                'Administrador',
                'Assistente Em Administração',
                'Jornalista',
                'Médico',
                'Nutricionista',
                'Técnico Em Tecnologia Da Informação',
                'Técnico Em Secretariado',
                'Técnico Em Laboratório',
                'Técnico Em Contabilidade',
                'Técnico Em Agropecuária',
                'Servente De Obras',
                'Técnico Assuntos Educacionais',
                'Cozinheiro',
                'Engenheiro Agrônomo',
                'Zootecnista',
                'Assistente Social',
                'Arquivista',
                'Operador De Máquinas Agrícolas',
                'Auxiliar De Agropecuária',
                'Auxiliar Encanador',
                'Assistente De Alunos',
                'Bibliotecário',
                'Enfermeiro',
                'Pedagogo',
                'Odontólogo',
                'Almoxarife',
                'Contador',
                'Analista De Tecnologia Da Informação',
                'Técnico Em Alimentos E Laticínios',
                'Telefonista',
                'Auxiliar De Biblioteca',
                'Vigilante',
                'Técnico Em Arquivo'
            ],

            'Empregados Públicos' => [
                'Artífice De Manutenção',
                'Escriturário',
                'Caixa'
            ]
        ];

        $dados = [
            'cargos_por_categoria' => $cargos_por_categoria
        ];

        require_once __DIR__ . '/../views/usuario/cadastrar.php';
    }

    public function processarCadastro()
    {
        //obtenção e tratamento dos dados do formulário
        $matricula = trim($_POST['matricula'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $cargo = trim($_POST['cargo'] ?? '');
        $tipo_usuario = $_POST['tipo_usuario'] ?? 'servidor';

        //salva os dados do formulário em caso de erro
        $_SESSION['dados_form'] = [
            'matricula' => $matricula,
            'nome' => $nome,
            'email' => $email,
            'cargo' => $cargo,
            'tipo_usuario' => $tipo_usuario
        ];

        //validações
        if (empty($matricula)) {
            $_SESSION['erro'] = "Matrícula é obrigatória!";
        } elseif ($this->model->existePorMatricula($matricula)) {
            $_SESSION['erro'] = "Matrícula já cadastrada!";
        } elseif ($this->model->existePorEmail($email)) {
            $_SESSION['erro'] = "E-mail já cadastrado! Por favor, utilize outro ou faça login.";
        } else {
            if ($this->model->cadastrar($matricula, $nome, $email, $senha, $cargo, $tipo_usuario)) {
                $_SESSION['sucesso'] = "Usuário cadastrado com sucesso!";
                unset($_SESSION['dados_form']); // Limpar dados do formulário
                redirect('login');
                return;
            } else {
                $_SESSION['erro'] = "Erro ao cadastrar usuário.";
            }
        }
        redirect('admin/usuarios/cadastrar');
    }

    public function editar()
    {
        if (!isset($_GET['id'])) {
            $_SESSION['erro'] = "ID do usuário não informado.";
            redirect('admin/usuarios/listar');
            return;
        }
        $id = intval($_GET['id']);
        $usuario = $this->model->buscarPorId($id);
        if (!$usuario) {
            $_SESSION['erro'] = "Usuário não encontrado.";
            redirect('admin/usuarios/listar');
            return;
        }

        require_once __DIR__ . '/../views/usuario/editar.php';
    }

    public function processarEdicao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            $_SESSION['erro'] = "Requisição inválida.";
            redirect('admin/usuarios/listar');
            return;
        }
        $id = intval($_POST['id']);
        $matricula = trim($_POST['matricula'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $tipo_usuario = $_POST['tipo_usuario'] ?? 'servidor';
        $novaSenha = trim($_POST['senha'] ?? '');

        if ($this->model->atualizar($id, $matricula, $nome, $email, $cargo, $tipo_usuario, $novaSenha)) {
            $_SESSION['sucesso'] = "Usuário atualizado com sucesso.";
            redirect('admin/usuarios/listar');
        } else {
            $_SESSION['erro'] = "Erro ao atualizar usuário.";
            redirect('admin/usuarios/editar?id=' . $id);
        }
    }

    public function listar()
    {
        $usuarios = $this->model->listarTodos();
        require_once __DIR__ . '/../views/usuario/listar.php';
    }

    public function excluir()
    {
        if (!isset($_GET['id'])) {
            $_SESSION['erro'] = "ID do usuário não informado para exclusão.";
            redirect('admin/usuarios/listar.php');
            return;
        }
        $id = intval($_GET['id']);
        if ($id == $_SESSION['usuario_id']) {
            $_SESSION['erro'] = "Você não pode excluir sua própria conta.";
        } elseif ($this->model->excluir($id)) {
            $_SESSION['sucesso'] = "Usuário excluído com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir usuário.";
        }
        redirect('admin/usuarios/listar.php');
    }
}
