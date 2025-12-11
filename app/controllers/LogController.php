<?php

require_once __DIR__ . '/../models/Log.php';

class LogController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Log($db);
    }

    public function listar()
    {
        //testa se o usuário logado não é Administrador nem Gestor
        if (!Auth::isAdmin()) {
            $_SESSION['erro'] = "Acesso não autorizado.";
            redirect('dashboard');
            exit;
        } else if (!Auth::isAdminOrGestor()) {
            redirect('index.php');
        }
        $logs = $this->model->listarTodos();
        require_once __DIR__ . '/../views/log/listar.php';
    }
}
