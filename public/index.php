<?php

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../app/controllers/ReuniaoController.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../app/controllers/PresencaController.php';
require_once __DIR__ . '/../app/controllers/DeclaracaoController.php';
require_once __DIR__ . '/../app/controllers/LogController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Log.php';

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
$reuniao_controller = new ReuniaoController($conn);
$usuario_controller = new UsuarioController($conn);
$presenca_controller = new PresencaController($conn);
$declaracao_controller = new DeclaracaoController($conn);
$log_controller = new LogController($conn);
$dashboard_controller = new DashboardController();
$auth_controller = new AuthController($conn);
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];
$base_url_path = parse_url(BASE_URL, PHP_URL_PATH);
if (strpos($request_uri, $base_url_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_url_path));
}
$request_uri = trim($request_uri, '/');

switch ($request_uri) {
    case '':
    case 'login':
    case 'logout':
    case 'servidor/presenca/registrar':
    case 'estatisticas':
    case 'servidor/presenca/listar':
    case 'registrar_presenca':
    case 'qrcode':
    case 'scanner-qrcode':
    case 'logs':
    case 'admin/reunioes/listar':
    case 'admin/reunioes/criar':
    case 'admin/declaracoes/emitir':
    case 'admin/usuarios/cadastrar':
    case 'admin/usuarios/listar':
        if ($request_method == 'GET') {
            switch ($request_uri) {
                case '':
                    $dashboard_controller->index();
                    break;
                case 'login':
                    $auth_controller->mostrarFormularioLogin();
                    break;
                case 'logout':
                    $auth_controller->processarLogout();
                    break;
                case 'servidor/presenca/registrar':
                    if (isset($_GET['id'])) {
                        $presenca_controller->registrarPresenca();
                    } else {
                        $_SESSION['erro'] = "ID da reunião não especificado.";
                        redirect('registrar_presenca');
                    }
                    break;
                case 'estatisticas':
                    $presenca_controller->mostrarEstatisticas();
                    break;
                case 'servidor/presenca/listar':
                    $presenca_controller->listarPresencas();
                    break;
                case 'registrar_presenca':
                    $reuniao_controller->mostrarFormularioRegistroManual();
                    break;
                case 'qrcode':
                    $reuniao_controller->mostrarFormularioQrcode();
                    break;
                case 'scanner-qrcode':
                    $scanner_qrcode_controller->index();
                    break;
                case 'logs':
                    $log_controller->listar();
                    break;
                case 'admin/reunioes/listar':
                    $reuniao_controller->listar();
                    break;
                case 'admin/reunioes/criar':
                    $reuniao_controller->criar();
                    break;
                case 'admin/declaracoes/emitir':
                    $declaracao_controller->mostrarFormularioEmissao();
                    break;
                case 'admin/usuarios/cadastrar':
                    $usuario_controller->mostrarFormularioCadastro();
                    break;
                case 'admin/usuarios/listar':
                    $usuario_controller->listar();
                    break;
            }
        } else {
            header("HTTP/1.0 405 Method Not Allowed");
            echo "Método de requisição não permitido para esta URL.";
        }
        break;

    case 'processar-login':
    case 'redirecionar-cadastro':
    case 'processar-registro-manual':
    case 'processar-qrcode':
    case 'scanner-qrcode/processar':
    case 'processar-criacao':
    case 'admin/usuarios/processar-edicao':
    case 'processar-emissao':
    case 'processar-cadastro':
    case 'admin/reunioes/processar-edicao':
        if ($request_method == 'POST') {
            switch ($request_uri) {
                case 'processar-login':
                    $auth_controller->processarLogin();
                    break;
                case 'redirecionar-cadastro':
                    $auth_controller->processarRedirecionarCadastro();
                    break;
                case 'processar-registro-manual':
                    $reuniao_controller->processarRegistroManual();
                    break;
                case 'scanner-qrcode/processar':
                    $scanner_qrcode_controller->processarQRCode();
                    break;
                case 'processar-qrcode':
                    $reuniao_controller->processarQrcode();
                    break;
                case 'processar-criacao':
                    $reuniao_controller->processarCriacao();
                    break;
                case 'admin/reunioes/processar-edicao':
                    $reuniao_controller->processarEdicao();
                    break;
                case 'processar-emissao':
                    $declaracao_controller->processarEmissao();
                    break;
                case 'processar-cadastro':
                    $usuario_controller->processarCadastro();
                    break;
                case 'admin/usuarios/processar-edicao':
                    $usuario_controller->processarEdicao();
                    break;
            }
        } else {
            header("HTTP/1.0 405 Method Not Allowed");
            echo "Método de requisição não permitido para esta URL.";
        }
        break;

    default:
        if (strpos($request_uri, 'assets/qrcodes/') === 0 && $request_method == 'GET') {
            $file_path = __DIR__ . '/../' . $request_uri;
            if (file_exists($file_path)) {
                header('Content-Type: image/png');
                readfile($file_path);
                exit();
            }
        }
        if ($request_method == 'GET') {
            if (strpos($request_uri, 'servidor/presenca/registrar/') === 0) {
                $_GET['id'] = str_replace('servidor/presenca/registrar/', '', $request_uri);
                $presenca_controller->registrarPresenca();
            } elseif (strpos($request_uri, 'admin/reunioes/editar/') === 0) {
                $_GET['id'] = str_replace('admin/reunioes/editar/', '', $request_uri);
                $reuniao_controller->editar();
            } elseif (strpos($request_uri, 'admin/reunioes/excluir/') === 0) {
                $_GET['id'] = str_replace('admin/reunioes/excluir/', '', $request_uri);
                $reuniao_controller->excluir();
            } elseif (strpos($request_uri, 'admin/reunioes/visualizar/') === 0) {
                $_GET['id'] = str_replace('admin/reunioes/visualizar/', '', $request_uri);
                $reuniao_controller->visualizar();
            } elseif (strpos($request_uri, 'admin/usuarios/editar/') === 0) {
                $_GET['id'] = str_replace('admin/usuarios/editar/', '', $request_uri);
                $usuario_controller->editar();
            } elseif (strpos($request_uri, 'admin/usuarios/excluir/') === 0) {
                $_GET['id'] = str_replace('admin/usuarios/excluir/', '', $request_uri);
                $usuario_controller->excluir();
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Página não encontrada.";
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Página não encontrada.";
        }
        break;
}
$conn->close();
