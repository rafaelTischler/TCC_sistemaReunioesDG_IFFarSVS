<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2>Registrar Presença Manualmente</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro'];
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form method="POST" action="processar-registro-manual">
    <div>
        <label for="id" class="form-label">Código da Reunião (ID):</label>
        <input type="number" class="form-control" id="id" name="id" required>
    </div>
    <button type="submit" class="btn btn-primary">Registrar Presença</button>
    <a href="<?= BASE_URL ?>" class="btn btn-secondary">Voltar ao Início</a>
</form>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>