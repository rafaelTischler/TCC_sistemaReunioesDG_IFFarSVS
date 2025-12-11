<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2>Logs de Ações</h2>

<?php if (count($logs) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário ID</th>
                <th>Ação</th>
                <th>Módulo</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['id']) ?></td>
                    <td><?= htmlspecialchars($log['usuario_id']) ?></td>
                    <td><?= htmlspecialchars($log['acao']) ?></td>
                    <td><?= htmlspecialchars($log['modulo']) ?></td>
                    <td><?= formatarData($log['data_log'], 'd/m/Y H:i') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  <a href="<?= BASE_URL ?>" class="btn btn-secondary mt-3">Voltar ao Início</a>
<?php else: ?>
    <p>Não há logs registrados.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>