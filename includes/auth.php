<?php

class Auth
{
    public static function verificaLogin()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            redirect('login.php');
        }
    }

    public static function isAdmin()
    {
        return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'administrador';
    }

    public static function isGestor()
    {
        return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'gestor';
    }

    public static function isServidor()
    {
        return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'servidor';
    }

    public static function isAdminOrGestor()
    {
        return self::isAdmin() || self::isGestor();
    }

    public static function getTipoUsuario()
    {
        return $_SESSION['tipo_usuario'] ?? 'servidor';
    }

    public static function getTipoUsuarioLabel()
    {
        $tipo = self::getTipoUsuario();
        $labels = [
            'administrador' => 'Administrador',
            'gestor' => 'Gestor',
            'servidor' => 'Servidor'
        ];
        return $labels[$tipo] ?? 'Servidor';
    }
}
