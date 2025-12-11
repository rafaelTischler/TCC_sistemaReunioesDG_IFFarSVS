<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Minhas Presenças</h2>

    <?php if (empty($presencas)): ?>
        <div class="alert alert-info mt-3">Você ainda não registrou presença em nenhuma reunião.</div>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Título da Reunião</th>
                    <th>Data da Reunião</th>
                    <th>Local</th>
                    <th>Data da Presença</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presencas as $presenca): ?>
                    <tr>
                        <td><?= htmlspecialchars($presenca['titulo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($presenca['data_reuniao'])) ?></td>
                        <td><?= htmlspecialchars($presenca['local_reuniao']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($presenca['data_presenca'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<a href="<?= BASE_URL ?>" class="btn btn-secondary mt-3">Voltar ao Início</a>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>