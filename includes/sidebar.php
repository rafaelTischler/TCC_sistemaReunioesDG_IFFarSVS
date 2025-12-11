<?php
$isAdmin = class_exists('Auth') && Auth::isAdmin();
$isGestor = class_exists('Auth') && Auth::isGestor();
$isAdminOrGestor = $isAdmin || $isGestor;
$tipoUsuarioLabel = class_exists('Auth') ? Auth::getTipoUsuarioLabel() : 'Servidor';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SISTEMA_NOME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/estilo.css">


</head>

<body>
    <div class="mobile-header">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <a href="<?= BASE_URL ?>" class="mobile-logo">
            <?= SISTEMA_NOME ?>
        </a>
        <div></div>
    </div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= BASE_URL ?>" class="sidebar-logo">
                <span class="logo-icon"><i class="fas fa-chart-pie"></i></span>
                <span><?= SISTEMA_NOME ?></span>
            </a>
        </div>

        <div class="user-info">
            <div class="user-avatar">
                <?= isset($_SESSION['usuario_nome']) ? substr($_SESSION['usuario_nome'], 0, 1) : 'U' ?>
            </div>
            <div class="user-name">
                <?= isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário' ?>
            </div>
            <div class="user-role">
                <?= $tipoUsuarioLabel ?>
            </div>
        </div>


        <!--menu principal para todos-->
        <div class="menu-section">
            <div class="menu-title">Principal</div>
            <a href="<?= BASE_URL ?>" class="menu-item <?= basename($_SERVER['PHP_SELF']) == '' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-home"></i></span>
                <span class="menu-text">Dashboard</span>
            </a>
        </div>

        <!--menu servidor para todos-->
        <div class="menu-section">

            <div class="menu-section">
                <div class="menu-title">Servidor</div>
                <!--NÃO IMPLEMENTADO-->
                <!-- <a href="<?= BASE_URL ?>scanner-qrcode" class="menu-item" id="scannerQRCode">
                    <span class="menu-icon"><i class="fas fa-qrcode"></i></span>
                    <span class="menu-text">Escanear QR Code</span>
                </a> -->
                <a href="<?= BASE_URL ?>registrar_presenca" class="menu-item">
                    <span class="menu-icon"><i class="fas fa-calendar-check"></i></span>
                    <span class="menu-text">Registrar Presença</span>
                </a>
                <a href="<?= BASE_URL ?>servidor/presenca/listar" class="menu-item">
                    <span class="menu-icon"><i class="fas fa-list"></i></span>
                    <span class="menu-text">Minhas Presenças</span>
                </a>
            </div>

            <!--menu gestão (para Admin e Gestor) -->
            <?php if ($isAdminOrGestor): ?>
                <div class="menu-section">
                    <div class="menu-title">Gestão</div>
                    <a href="<?= BASE_URL ?>admin/reunioes/listar" class="menu-item">
                        <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="menu-text">Gerenciar Reuniões</span>
                    </a>
                    <a href="<?= BASE_URL ?>estatisticas" class="menu-item">
                        <span class="menu-icon"><i class="fas fa-chart-bar"></i></span>
                        <span class="menu-text">Consultar estatísticas</span>
                    </a>
                    <a href="<?= BASE_URL ?>admin/declaracoes/emitir" class="menu-item">
                        <span class="menu-icon"><i class="fas fa-file-alt"></i></span>
                        <span class="menu-text">Emitir Declarações</span>
                    </a>
                </div>
            <?php endif; ?>

            <!--menu administração (apenas Admin) -->
            <?php if ($isAdmin): ?>
                <div class="menu-section">
                    <div class="menu-title">Administração</div>
                    <a href="<?= BASE_URL ?>admin/usuarios/listar" class="menu-item">
                        <span class="menu-icon"><i class="fas fa-users"></i></span>
                        <span class="menu-text">Gerenciar Usuários</span>
                    </a>
                    <a href="<?= BASE_URL ?>logs" class="menu-item">
                        <span class="menu-icon"><i class="fas fa-clipboard-list"></i></span>
                        <span class="menu-text">Consultar Logs</span>
                    </a>
                </div>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>logout" class="logout-btn" id="logoutBtn">
                <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="menu-text">Sair</span>
            </a>
        </div>
    </div>
    <div class="main-content">
        <div class="container">