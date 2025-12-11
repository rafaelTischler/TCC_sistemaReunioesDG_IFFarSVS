<?php

$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= SISTEMA_NOME ?></title>
    <link rel="stylesheet" href="<?= rtrim(BASE_URL, '/') ?>/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <a href="<?= BASE_URL ?>" class="sidebar-logo">
                        <span class="logo-icon"><i class="fas fa-chart-pie"></i></span>
                        <span><?= SISTEMA_NOME ?></span>
                    </a>
                </div>
                <h1 class="login-title">Bem-vindo</h1>
                <p class="login-subtitle">Entre com suas credenciais para acessar o sistema</p>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert-login">
                    <h3>Erro de autenticação</h3>
                    <p><?= htmlspecialchars($erro) ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>processar-login" method="POST" class="login-form" id="loginForm">
                <div class="form-group-login">
                    <input
                        type="text"
                        class="form-input"
                        id="matricula"
                        name="matricula"
                        required
                        placeholder="Matrícula"
                        autocomplete="username">
                </div>

                <div class="form-group-login">
                    <input
                        type="password"
                        class="form-input"
                        id="senha"
                        name="senha"
                        required
                        placeholder="Senha"
                        autocomplete="current-password">
                </div>

                <p class="login-forgot-password"><a href="<?= BASE_URL ?>recuperar-senha" class="login-link">Esqueceu sua senha?</a></p>

                <button type="submit" class="btn-login" id="loginButton">
                    <span class="btn-text">Entrar no Sistema</span>
                    <div class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Entrando...
                    </div>
                </button>
            </form>

            <div class="login-footer">
                <p class="login-footer-text">
                    Não tem uma conta?
                    <a href="<?= BASE_URL ?>admin/usuarios/cadastrar" class="login-link">
                        Crie aqui
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const btnText = loginButton.querySelector('.btn-text');
            const btnLoading = loginButton.querySelector('.btn-loading');

            loginForm.addEventListener('submit', function(e) {
                //efeito de loading no botão
                btnText.style.display = 'none';
                btnLoading.style.display = 'block';
                loginButton.disabled = true;
            });

            //verifica o funcionamento do formulário
            if (<?= isset($erro) ? 'true' : 'false' ?>) {
                btnText.style.display = 'block';
                btnLoading.style.display = 'none';
                loginButton.disabled = false;
            }
        });
    </script>
</body>

</html>