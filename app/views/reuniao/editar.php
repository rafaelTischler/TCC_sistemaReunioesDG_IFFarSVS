<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2>Editar Reunião</h2>

<form method="POST" action="<?= BASE_URL ?>admin/reunioes/processar-edicao">
    <input type="hidden" name="id" value="<?= $reuniao['id'] ?>">
    <div class="form-group">
        <label>Título:</label>
        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($reuniao['titulo']) ?>" required>
    </div>

    <div class="form-group">
        <label>Descrição:</label>
        <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($reuniao['descricao']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Data e Hora:</label>
        <input type="datetime-local" name="data_reuniao" class="form-control"
            value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($reuniao['data_reuniao']))) ?>" required>
    </div>

    <div class="form-group">
        <label>Local:</label>
        <input type="text" name="local_reuniao" class="form-control" value="<?= htmlspecialchars($reuniao['local_reuniao']) ?>">
    </div>

    <button type="submit" class="btn">Atualizar</button>
    <a href="<?= BASE_URL ?>admin/reunioes/visualizar/<?= htmlspecialchars($reuniao['id']) ?>" class="btn">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
