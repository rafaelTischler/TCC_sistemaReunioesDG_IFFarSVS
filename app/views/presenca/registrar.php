<?php
require_once __DIR__ . '/../../../includes/header.php';

//variáveis de mensagem
$registro_status = $_SESSION['registro_status'] ?? 'erro';
$registro_mensagem = $_SESSION['registro_mensagem'] ?? '';

//limpa as mensagens
unset($_SESSION['registro_status']);
unset($_SESSION['registro_mensagem']);

//testa o tipo de alerta baseado no status
$tipo_alerta = 'info';
switch ($registro_status) {
    case 'registrado':
        $tipo_alerta = 'success';
        break;
    case 'ja_registrado':
        $tipo_alerta = 'warning';
        break;
    case 'erro':
        $tipo_alerta = 'danger';
        break;
}

if (empty($registro_mensagem)) {
    switch ($registro_status) {
        case 'registrado':
            $registro_mensagem = "Presença registrada com sucesso!";
            break;
        case 'ja_registrado':
            $registro_mensagem = "Sua presença já foi registrada para esta reunião.";
            break;
        case 'erro':
            $registro_mensagem = "Ocorreu um erro ao registrar sua presença. Por favor, tente novamente.";
            break;
        default:
            $registro_mensagem = "Status de registro desconhecido."; //se não tem mensagem específica usa mensagem padrão
            break;
    }
}
?>

<div class="container-fluid text-center mt-5">
    <h2>Confirmação de Presença</h2>

    <div class="alert alert-<?= $tipo_alerta ?>" role="alert">
        <h4 class="alert-heading">
            <?php
            if ($tipo_alerta === 'success') echo 'Sucesso!';
            else if ($tipo_alerta === 'warning') echo 'Aviso!';
            else if ($tipo_alerta === 'danger') echo 'Erro!';
            else echo 'Informação';
            ?>
        </h4>
        <p><?= htmlspecialchars($registro_mensagem); ?></p>

        <?php if ($tipo_alerta === 'success'): ?>
            <hr>
            <p class="mb-0">
                <i class="fas fa-check-circle"></i> Registrado em: <?= date('d/m/Y \à\s H:i:s'); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <a href="<?= BASE_URL ?>" class="btn btn-primary">
            <i class="fas fa-home"></i> Voltar ao Início
        </a>

        <?php if (isset($_GET['id']) && $registro_status !== 'registrado' && $registro_status !== 'ja_registrado'): ?>
            <a href="?id=<?= $_GET['id'] ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Tentar Novamente
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>