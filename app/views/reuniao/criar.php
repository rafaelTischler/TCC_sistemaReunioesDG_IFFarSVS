<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2>Criar Nova Reunião</h2>

<form method="POST" action="<?= BASE_URL ?>processar-criacao">
    <div class="form-group">
        <label>Título:</label>
        <input type="text" name="titulo" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Descrição:</label>
        <textarea name="descricao" class="form-control" rows="3" required></textarea>
    </div>

    <div class="form-group">
        <label>Data e Hora:</label>
        <input type="datetime-local" name="data_reuniao" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Local:</label>
        <input type="text" name="local_reuniao" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Criar Reunião</button>
</form>

<a href="<?= BASE_URL ?>/admin/reunioes/listar" class="btn btn-secondary mb-3">Voltar</a>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>