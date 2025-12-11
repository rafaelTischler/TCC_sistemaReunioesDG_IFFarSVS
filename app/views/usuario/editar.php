<?php require_once __DIR__ . '/../../../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Editar Usuário</h2>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['erro'];
                                        unset($_SESSION['erro']); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>admin/usuarios/processar-edicao" class="border p-4 rounded bg-light">
        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
        <div class="mb-3">
            <label class="form-label">Matrícula SIAPE</label>
            <input type="text" name="matricula" class="form-control" value="<?= htmlspecialchars($usuario['matricula_siape']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars($usuario['cargo']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nova Senha <small class="text-muted">(deixe em branco para manter a atual)</small></label>
            <input type="password" name="senha" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de Usuário</label>
            <select name="tipo_usuario" class="form-control" required>
                <option value="servidor" <?= $usuario['tipo_usuario'] === 'servidor' ? 'selected' : '' ?>>Servidor</option>
                <option value="gestor" <?= $usuario['tipo_usuario'] === 'gestor' ? 'selected' : '' ?>>Gestor</option>
                <option value="administrador" <?= $usuario['tipo_usuario'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="<?= BASE_URL ?>admin/usuarios/listar" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>