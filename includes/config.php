<?php
date_default_timezone_set('America/Sao_Paulo');

function getLocalIP()
{
    $ip = gethostbyname(gethostname());
    return (filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1');
}

if (!defined('NETWORK_CONFIG')) {
    define('NETWORK_CONFIG', [
        'ip' => getLocalIP(),
        'port' => '80',
        'protocol' => 'http'
    ]);
}

define('BASE_URL', NETWORK_CONFIG['protocol'] . '://' . NETWORK_CONFIG['ip'] . ':' . NETWORK_CONFIG['port'] . '/sistema_presenca/public/');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'sistema_presenca');
define('SISTEMA_NOME', 'praesencia');
define('QRCODE_DIR', __DIR__ . '/../assets/qrcodes/');

if (!function_exists('redirect')) {
    function redirect($path = '')
    {
        $path = str_replace('.php', '', $path);

        if (strpos($path, BASE_URL) !== 0) {
            $path = BASE_URL . $path;
        }
        header("Location: " . $path);
        exit();
    }


    if (!function_exists('getDB')) {
        function getDB()
        {
            static $conn;
            if (!$conn) {
                $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
                if ($conn->connect_error) {
                    die("Erro de conexão: " . $conn->connect_error);
                }
            }
            return $conn;
        }
    }
}
