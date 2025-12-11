<?php

require_once __DIR__ . '/../../../includes/header.php';
?>

<h2>Lista de Usuários</h2>
<a href="<?= BASE_URL ?>cadastrar_usuario.php" class="btn btn-primary mb-3">Novo Usuário</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Cargo</th>
            <th>Tipo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['matricula_siape']) ?></td>
                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['cargo']) ?></td>
                <td>
                    <?php
                    $tipos = [
                        'administrador' => 'Administrador',
                        'gestor' => 'Gestor',
                        'servidor' => 'Servidor'
                    ];
                    echo $tipos[$usuario['tipo_usuario']] ?? 'Servidor';
                    ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>admin/usuarios/editar/<?= htmlspecialchars($usuario['id']) ?>" class="btn-warning btn-acao"><i class="fas fa-edit"></i></a>
                    <?php if (isset($_SESSION['usuario_id']) && $usuario['id'] != $_SESSION['usuario_id']): ?>
                        <a href="<?= BASE_URL ?>admin/usuarios/excluir/<?= htmlspecialchars($usuario['id']) ?>" class="btn-warning btn-acao" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>