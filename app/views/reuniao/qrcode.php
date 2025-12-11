<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2>Registrar Presença com Código</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro'];
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>processar-qrcode">
    <div class="form-group">
        <label>Digite o código da reunião:</label>
        <input type="text" name="codigo" class="form-control" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Presença</button>
</form>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>