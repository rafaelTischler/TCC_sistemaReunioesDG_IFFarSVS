<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2><?= htmlspecialchars($reuniao['titulo']) ?></h2>

<div class="reuniao-visualizar">
    <div class="reuniao-info">
        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($reuniao['data_reuniao'])) ?></p>
        <p><strong>Local:</strong> <?= htmlspecialchars($reuniao['local_reuniao']) ?></p>
        <p><strong>Criador:</strong> <?= htmlspecialchars($reuniao['criador']) ?></p>
        <p><strong>Total de Presenças:</strong> <?= htmlspecialchars($total_presencas) ?></p>
        <h3>Descrição:</h3>
        <p><?= nl2br(htmlspecialchars($reuniao['descricao'])) ?></p>
        <a href="<?= BASE_URL ?>admin/reunioes/editar/<?= htmlspecialchars($reuniao['id']) ?>" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/admin/reunioes/listar" class="btn btn-secondary mb-3">Voltar</a>

        <p>Link direto:
            <small>
                <span class="breakable-link">
                    <a href="<?= BASE_URL . 'servidor/presenca/registrar/' . htmlspecialchars($reuniao['id']) ?>" target="_blank">
                        <?= BASE_URL . 'servidor/presenca/registrar/' . htmlspecialchars($reuniao['id']) ?>
                    </a>
                </span>
            </small>
        </p>
        <p class="qrcode-hint">
            Acesse este link de outro dispositivo na mesma rede
        </p>

    </div>

    <div class="reuniao-qrcode text-center">
        <h3>QR Code para Presença</h3>
        <?php
        $qrcode_file = __DIR__ . '/../../../assets/qrcodes/qrcode_' . htmlspecialchars($reuniao['id']) . '.png';
        if (file_exists($qrcode_file)):
        ?>
            <img src="<?= BASE_URL ?>assets/qrcodes/qrcode_<?= htmlspecialchars($reuniao['id']) ?>.png" alt="QR Code" class="qrcode-img">
        <?php else: ?>
            <div class="qrcode-placeholder">
                <p>QR Code não gerado</p>
            </div>
        <?php endif; ?>

        <p>ID da Reunião: <strong><?= htmlspecialchars($reuniao['id']) ?></strong></p>


    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>