<?php

require_once __DIR__ . '/../models/Reuniao.php';
require_once __DIR__ . '/../models/Presenca.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../../lib/phpqrcode/qrlib.php';
require_once __DIR__ . '/../../includes/funcoes.php';

class ReuniaoController
{
    private $model;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->model = new Reuniao($db);
    }

    public function criar()
    {
        //mensagens de erro específicas
        $erro_criacao = $_SESSION['erro_criacao'] ?? '';
        $dados_form = $_SESSION['dados_form_criacao'] ?? [];

        //limpa as mensagens
        unset($_SESSION['erro_criacao']);
        unset($_SESSION['dados_form_criacao']);

        //passa pra view
        $erro = $erro_criacao;

        require_once __DIR__ . '/../views/reuniao/criar.php';
    }

    public function processarCriacao()
    {
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $data_reuniao = $_POST['data_reuniao'] ?? '';
        $local_reuniao = trim($_POST['local_reuniao'] ?? '');

        //data não pode ser anterior à atual
        $timestamp_reuniao = strtotime($data_reuniao);
        $timestamp_atual = time();

        if ($timestamp_reuniao < $timestamp_atual) {
            $_SESSION['erro_criacao'] = "Não é possível criar uma reunião com data/hora anterior à atual.";
            $_SESSION['dados_form_criacao'] = [
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data_reuniao' => $data_reuniao,
                'local_reuniao' => $local_reuniao
            ];
            redirect('admin/reunioes/criar');
            return;
        }

        //nome da reunião não pode repetir
        if ($this->model->existeReuniaoComMesmoNome($titulo, 0)) {
            $_SESSION['erro_criacao'] = "Já existe uma reunião com este nome. Por favor, escolha outro nome.";
            $_SESSION['dados_form_criacao'] = [
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data_reuniao' => $data_reuniao,
                'local_reuniao' => $local_reuniao
            ];
            redirect('admin/reunioes/criar');
            return;
        }

        //mesma data e hora só se local da reunião for diferente
        if ($this->model->existeReuniaoMesmaDataLocal($data_reuniao, $local_reuniao, 0)) {
            $_SESSION['erro_criacao'] = "Já existe uma reunião agendada para esta data e hora no mesmo local. Por favor, escolha outro horário ou local.";
            $_SESSION['dados_form_criacao'] = [
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data_reuniao' => $data_reuniao,
                'local_reuniao' => $local_reuniao
            ];
            redirect('admin/reunioes/criar');
            return;
        }

        $reuniao_id = $this->model->criar($titulo, $descricao, $data_reuniao, $local_reuniao, $_SESSION['usuario_id']); //se passou em todas as verificações cria a reunião

        if ($reuniao_id) {
            $qrcode_filename = 'qrcode_' . $reuniao_id . '.png'; //nome do QR Code
            $qrcode_file_path = QRCODE_DIR . $qrcode_filename; //caminho do QR Code
            $url_presenca = BASE_URL . 'servidor/presenca/registrar?id=' . $reuniao_id; //link da presença

            //cria a pasta caso nao exista
            if (!file_exists(QRCODE_DIR)) {
                mkdir(QRCODE_DIR, 0777, true);
            }
            //gera a imagem em PNG do QR Code
            try {
                QRcode::png($url_presenca, $qrcode_file_path, QR_ECLEVEL_L, 10, 2);
                $this->model->atualizarQrcode($reuniao_id, $qrcode_filename); //atualiza o banco de dados com o nome do arquivo do QR Code
                $_SESSION['sucesso'] = "Reunião criada com sucesso!";
                redirect('admin/reunioes/visualizar/' . $reuniao_id);
                return;
            } catch (Exception $e) {
                $_SESSION['erro'] = "Reunião criada, mas houve um problema ao gerar o QR Code.";
                redirect('admin/reunioes/visualizar/' . $reuniao_id);
                return;
            }
        } else {
            $_SESSION['erro_criacao'] = "Erro ao criar reunião.";
            $_SESSION['dados_form_criacao'] = [
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data_reuniao' => $data_reuniao,
                'local_reuniao' => $local_reuniao
            ];
            redirect('admin/reunioes/criar');
            return;
        }
    }

    public function editar()
    {
        if (!isset($_GET['id'])) { //verifica se o id da reunião foi informado
            $_SESSION['erro'] = "Reunião não especificada.";
            redirect('admin/reunioes/listar');
            return;
        }
        $reuniao_id = $_GET['id'];
        $reuniao = $this->model->buscarPorId($reuniao_id); //busca os dados da reunião
        if (!$reuniao) {
            $_SESSION['erro'] = "Reunião não encontrada.";
            redirect('admin/reunioes/listar');
            return;
        }
        require_once __DIR__ . '/../views/reuniao/editar.php';
    }

    public function processarEdicao()
    {
        if (!isset($_POST['id'])) { //verifica se o id foi informado
            $_SESSION['erro'] = "Reunião não especificada.";
            redirect('admin/reunioes/listar');
            return;
        }
        $reuniao_id = $_POST['id'];
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $data_reuniao = $_POST['data_reuniao'] ?? '';
        $local_reuniao = $_POST['local_reuniao'] ?? '';
        if ($this->model->atualizar($reuniao_id, $titulo, $descricao, $data_reuniao, $local_reuniao)) { //realiza a atualização dos dados da reunião
            $_SESSION['sucesso'] = "Reunião atualizada com sucesso!";
            redirect('admin/reunioes/visualizar/' . $reuniao_id);
            return;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar reunião.";
            redirect('admin/reunioes/editar/' . $reuniao_id);
            return;
        }
    }

    public function listar()
    {
        $filtros = [];
        $params = [];
        $types = '';

        //base da consulta (JOIN para incluir o nome do criador)
        $sql = "SELECT r.*, u.nome as criador 
            FROM reunioes r 
            LEFT JOIN usuarios u ON r.criado_por = u.id 
            WHERE 1=1";

        //filtros de consulta
        if (!empty($_GET['titulo'])) {
            $sql .= " AND r.titulo LIKE ?";
            $params[] = "%" . $_GET['titulo'] . "%";
            $types .= 's';
        }
        if (!empty($_GET['data'])) {
            $sql .= " AND DATE(r.data_reuniao) = ?";
            $params[] = $_GET['data'];
            $types .= 's';
        }
        if (!empty($_GET['local'])) {
            $sql .= " AND r.local_reuniao LIKE ?";
            $params[] = "%" . $_GET['local'] . "%";
            $types .= 's';
        }

        //ordenação
        $ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'data_reuniao';
        switch ($ordem) {
            case 'titulo':
                $sql .= " ORDER BY r.titulo ASC";
                break;
            case 'data':
                $sql .= " ORDER BY r.data_reuniao DESC";
                break;
            default:
                $sql .= " ORDER BY r.data_reuniao DESC";
        }

        $stmt = $this->db->prepare($sql); //prepara a consulta

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute(); //executa
        $result = $stmt->get_result();
        $reunioes = [];

        while ($row = $result->fetch_assoc()) { //converte o resultado da consulta em um array
            $reunioes[] = $row;
        }

        $stmt->close();

        require_once __DIR__ . '/../views/reuniao/listar.php';
    }

    public function excluir()
    {
        if (!isset($_GET['id'])) {
            $_SESSION['erro'] = "Reunião não especificada para exclusão.";
            redirect('admin/reunioes/listar');
            return;
        }
        $id = intval($_GET['id']);
        if ($this->model->excluir($id)) {
            $_SESSION['sucesso'] = "Reunião excluída com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir reunião.";
        }
        redirect('admin/reunioes/listar');
    }

    public function visualizar()
    {
        if (!isset($_GET['id'])) {
            redirect('admin/reunioes/listar');
            return;
        }
        $reuniao_id = $_GET['id'];
        $reuniao_model = new Reuniao($this->db);
        $reuniao = $reuniao_model->buscarPorId($reuniao_id);

        if (!$reuniao) {
            $_SESSION['erro'] = "Reunião não encontrada.";
            redirect('admin/reunioes/listar');
            return;
        }
        $qr_url = BASE_URL . 'servidor/presenca/registrar?id=' . $reuniao['id'];
        $qrcode_file = QRCODE_DIR . 'qrcode_' . $reuniao['id'] . '.png';
        QRcode::png($qr_url, $qrcode_file, QR_ECLEVEL_H, 10, 2);
        $presenca_model = new Presenca($this->db);
        $total_presencas = $presenca_model->contarTotalPorReuniao($reuniao_id);
        require_once __DIR__ . '/../views/reuniao/visualizar.php';
    }

    public function mostrarFormularioRegistroManual()
    {
        require_once __DIR__ . '/../views/reuniao/registrar_presenca.php';
    }

    public function processarRegistroManual()
    {
        $reuniao_id = intval($_POST['id'] ?? 0);
        $reuniao = $this->model->buscarPorId($reuniao_id);
        if ($reuniao) {
            redirect("servidor/presenca/registrar?id=" . $reuniao_id);
            return;
        } else {
            $_SESSION['erro'] = "Reunião com o código informado não foi encontrada.";
            redirect("registrar_presenca");
        }
    }

    public function mostrarFormularioQrcode()
    {
        require_once __DIR__ . '/../views/reuniao/qrcode.php';
    }

    public function processarQrcode()
    {
        if (!isset($_POST['codigo'])) {
            redirect('qrcode');
            return;
        }

        $codigo = trim($_POST['codigo']);
        $reuniao = $this->model->buscarPorQrcodeToken($codigo);

        if ($reuniao) {
            redirect("servidor/presenca/registrar?id=" . $reuniao['id']);
            return;
        } else {
            $_SESSION['erro'] = "Código inválido ou expirado.";
            redirect("qrcode");
        }
    }
}
