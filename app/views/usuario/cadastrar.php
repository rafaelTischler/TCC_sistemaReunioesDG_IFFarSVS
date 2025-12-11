<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 8px 8px;
        }

        .suggestions .item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .suggestions .item:hover,
        .suggestions .item.active {
            background-color: #f8f9fa;
        }

        .suggestions .item .titulo {
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }

        .suggestions .item .meta {
            font-size: 0.85em;
            color: #666;
        }

        .form-group {
            position: relative;
        }

        .cargo-input-container {
            position: relative;
        }

        .cargo-input-container .form-input {
            cursor: pointer;
        }

        .dropdown-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
        }

        /* Estilo para tooltip de erro */
        .tooltip-error {
            position: absolute;
            top: -10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            white-space: nowrap;
        }

        .tooltip-error::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #dc3545 transparent transparent transparent;
        }

        .error-placeholder::placeholder {
            color: #dc3545;
            opacity: 1;
        }

        @keyframes pulseError {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        .pulse-error {
            animation: pulseError 1s;
            border-color: #dc3545 !important;
        }

        .floating-error {
            position: absolute;
            top: -8px;
            left: 15px;
            background: white;
            color: #dc3545;
            font-size: 0.75em;
            padding: 0 5px;
            z-index: 10;
            display: none;
        }

        .form-group.error .floating-error {
            display: block;
        }

        .form-group.error .form-input {
            border-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">

                        <a href="<?= BASE_URL ?>" class="sidebar-logo">
                            <span class="logo-icon"><i class="fas fa-chart-pie"></i></span>
                            <span><?= SISTEMA_NOME ?></span>
                        </a>
                    </div>
                    <div class="login-title">Cadastrar Usuário</div>
                    <p class="login-subtitle">Crie um novo usuário para acessar o sistema</p>
                </div>
                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['erro'];
                        unset($_SESSION['erro']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['sucesso'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['sucesso'];
                        unset($_SESSION['sucesso']); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="<?= BASE_URL ?>processar-cadastro" class="login-form" id="formCadastro">
                    <div class="form-group">
                        <input type="number" name="matricula" id="matricula" class="form-input" required placeholder="Matrícula SIAPE"
                            value="<?= isset($_SESSION['dados_form']['matricula']) ? htmlspecialchars($_SESSION['dados_form']['matricula']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" name="nome" id="nome" class="form-input" required placeholder="Nome completo"
                            value="<?= isset($_SESSION['dados_form']['nome']) ? htmlspecialchars($_SESSION['dados_form']['nome']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" id="email" class="form-input" required placeholder="E-mail"
                            value="<?= isset($_SESSION['dados_form']['email']) ? htmlspecialchars($_SESSION['dados_form']['email']) : '' ?>">
                    </div>

                    <!--campo de cargo com autocompletar -->
                    <div class="form-group">
                        <div class="cargo-input-container">
                            <input type="text"
                                name="cargo_display"
                                id="cargo_display"
                                class="form-input"
                                required
                                placeholder="Selecione ou digite o cargo"
                                autocomplete="off"
                                value="<?= isset($_SESSION['dados_form']['cargo']) ? htmlspecialchars($_SESSION['dados_form']['cargo']) : '' ?>">
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                            <input type="hidden" name="cargo" id="cargo" value="<?= isset($_SESSION['dados_form']['cargo']) ? htmlspecialchars($_SESSION['dados_form']['cargo']) : '' ?>">
                            <div id="cargo_suggestions" class="suggestions" role="listbox" aria-label="Sugestões de cargos"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="password" name="senha" id="senha" class="form-input" required placeholder="Senha">
                        <div class="floating-error" id="senhaError">As senhas não coincidem</div>
                    </div>

                    <!-- Campo Confirmar Senha -->
                    <div class="form-group">
                        <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-input" required placeholder="Confirmar senha">
                        <div class="floating-error" id="confirmarSenhaError">As senhas não coincidem</div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-login">Cadastrar</button>
                        <a href="<?= BASE_URL ?>" class="btn-voltar">Voltar</a>
                    </div>
                </form>

                <?php unset($_SESSION['dados_form']); ?>
            </div>
        </div>
    </div>

    <!--arquivo com a funcionalidade do autocompletar cargos-->
    <script src="<?= BASE_URL ?>js/autocompletar_cargos.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cargosPorCategoria = <?= json_encode($cargos_por_categoria ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

            inicializarAutocompleteCargos({
                cargosPorCategoria: cargosPorCategoria,
                inputId: 'cargo_display',
                hiddenId: 'cargo',
                suggestionsBoxId: 'cargo_suggestions',
                onSelect: function(cargo) {
                    console.log('Cargo selecionado:', cargo.nome);
                }
            });

            //validação de senha
            const senhaInput = document.getElementById('senha');
            const confirmarSenhaInput = document.getElementById('confirmar_senha');
            const form = document.getElementById('formCadastro');
            const senhaGroup = senhaInput.closest('.form-group');
            const confirmarGroup = confirmarSenhaInput.closest('.form-group');
            let errorTimeout;

            function mostrarErro() {
                senhaGroup.classList.add('error');
                confirmarGroup.classList.add('error');

                //efeito de pulso
                senhaInput.classList.add('pulse-error');
                confirmarSenhaInput.classList.add('pulse-error');

                //placeholder temporário
                confirmarSenhaInput.placeholder = "As senhas não coincidem";
                confirmarSenhaInput.classList.add('error-placeholder');
                if (errorTimeout) clearTimeout(errorTimeout);

                errorTimeout = setTimeout(() => {
                    senhaInput.classList.remove('pulse-error');
                    confirmarSenhaInput.classList.remove('pulse-error');
                }, 1000);
            }

            function limparErro() {
                senhaGroup.classList.remove('error');
                confirmarGroup.classList.remove('error');
                confirmarSenhaInput.placeholder = "Confirmar senha";
                confirmarSenhaInput.classList.remove('error-placeholder');
            }

            //função para validar senhas
            function validarSenhas() {
                const senha = senhaInput.value;
                const confirmarSenha = confirmarSenhaInput.value;

                //se ambos estiverem vazios, não mostra erro
                if (senha === '' && confirmarSenha === '') {
                    limparErro();
                    return false;
                }

                //se um estiver preenchido e o outro não
                if ((senha === '' && confirmarSenha !== '') || (senha !== '' && confirmarSenha === '')) {
                    limparErro();
                    return false;
                }

                //se ambos estiverem preenchidos e diferentes
                if (senha !== confirmarSenha) {
                    mostrarErro();
                    return false;
                } else {
                    limparErro();
                    return true;
                }
            }

            //validar quando o usuário digitar nos campos
            senhaInput.addEventListener('input', function() {
                if (confirmarSenhaInput.value !== '') {
                    validarSenhas();
                }
            });

            confirmarSenhaInput.addEventListener('input', function() {
                if (senhaInput.value !== '') {
                    validarSenhas();
                }
            });

            //validar antes de enviar o formulário
            form.addEventListener('submit', function(event) {
                if (!validarSenhas() && senhaInput.value !== '' && confirmarSenhaInput.value !== '') {
                    event.preventDefault(); //impede o envio do formulário

                    //foca no campo de confirmação com efeito
                    confirmarSenhaInput.focus();
                    confirmarSenhaInput.select();
                    //dica
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip-error';
                    tooltip.textContent = 'As senhas não coincidem';
                    tooltip.style.display = 'block';
                    confirmarGroup.appendChild(tooltip);
                    setTimeout(() => {
                        if (tooltip.parentNode) {
                            tooltip.parentNode.removeChild(tooltip);
                        }
                    }, 3000);
                }
            });

            senhaInput.addEventListener('focus', function() {
                limparErro();
            });

            confirmarSenhaInput.addEventListener('focus', function() {
                limparErro();
            });
        });
    </script>
</body>

</html>