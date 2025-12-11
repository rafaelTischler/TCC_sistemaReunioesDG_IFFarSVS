<?php

require_once __DIR__ . '/../models/Declaracao.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Presenca.php';
require_once __DIR__ . '/../../includes/funcoes.php';

class DeclaracaoController
{
    private $declaracaoModel;
    private $usuarioModel;
    private $presencaModel;

    //inicializa os models com a conexão do banco
    public function __construct($db)
    {
        $this->declaracaoModel = new Declaracao($db);
        $this->usuarioModel = new Usuario($db);
        $this->presencaModel = new Presenca($db);
    }

    public function mostrarFormularioEmissao()
    {
        $servidores = $this->usuarioModel->listarTodos(); //busca os servidores (SELECT)
        require_once __DIR__ . '/../views/declaracao/emitir.php';
    }

    public function processarEmissao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'declaracoes/emitir.php');
            return;
        }
        //pega os dados do formulário
        $usuario_id = $_POST['usuario_id'] ?? '';
        $data_inicio = $_POST['data_inicio'] ?? '';
        $data_fim = $_POST['data_fim'] ?? '';
        $servidor = $this->usuarioModel->buscarDetalhesPorId($usuario_id); //busca dados do servidor
        $reunioes = $this->presencaModel->buscarPresencasPorPeriodo($usuario_id, $data_inicio, $data_fim); //busca reuniões que o servidor participou no período indicado
        if ($servidor && count($reunioes) > 0) {
            $conteudo = "Declaração de participação em " . count($reunioes) . " reuniões"; //conteúdo que aparece nos logs
            $emitido_por = $_SESSION['usuario_id']; //id do usuário emissor
            $this->declaracaoModel->inserir($usuario_id, $emitido_por, $conteudo, $data_inicio, $data_fim); //registra a declaração no banco
            $dados = [ //prepara os dados pra gerar o PDF
                'servidor_nome' => $servidor['nome'],
                'servidor_matricula' => $servidor['matricula_siape'],
                'servidor_cargo' => $servidor['cargo'],
                'data_inicio' => formatarData($data_inicio, 'd/m/Y'),
                'data_fim' => formatarData($data_fim, 'd/m/Y'),
                'reunioes' => $reunioes
            ];
            downloadDeclaracaoPDF($dados); //gera e baixa o PDF (biblioteca mPDF)
            exit();
        } else {
            $_SESSION['erro_declaracao'] = "Nenhuma reunião encontrada no período selecionado ou usuário não encontrado.";
            header('Location: ' . BASE_URL . 'admin/declaracoes/emitir');
        }
    }
}
