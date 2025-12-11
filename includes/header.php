<?php
defined('SISTEMA_NOME');
defined('BASE_URL');

//verifica se o usuário está logado antes de mostrar a sidebar
if (isset($_SESSION['usuario_id'])) {
    //inclui a barra lateral apenas se o usuário estiver logado
    include __DIR__ . '/sidebar.php';
}
