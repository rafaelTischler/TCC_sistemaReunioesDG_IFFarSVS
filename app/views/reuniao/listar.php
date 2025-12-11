<?php
require_once __DIR__ . '/../../../includes/header.php';
?>

<h2 class="page-title">Lista de Reuniões</h2>

<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="alert alert-success"><?= $_SESSION['sucesso'];
                                        unset($_SESSION['sucesso']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erro'];
                                    unset($_SESSION['erro']); ?></div>
<?php endif; ?>

<!--filtros de pesquisa-->
<div class="filtros-container">
    <form method="GET" class="filtros-form">
        <div class="filtro-group">
            <label for="titulo" class="filtro-label">Título</label>
            <input type="text" class="filtro-input" id="titulo" name="titulo"
                value="<?= isset($_GET['titulo']) ? htmlspecialchars($_GET['titulo']) : '' ?>"
                placeholder="Digite o título">
        </div>
        
        <div class="filtro-group">
            <label for="data" class="filtro-label">Data</label>
            <input type="date" class="filtro-input" id="data" name="data"
                value="<?= isset($_GET['data']) ? htmlspecialchars($_GET['data']) : '' ?>">
        </div>
        
        <div class="filtro-group">
            <label for="local" class="filtro-label">Local</label>
            <input type="text" class="filtro-input" id="local" name="local"
                value="<?= isset($_GET['local']) ? htmlspecialchars($_GET['local']) : '' ?>"
                placeholder="Digite o local">
        </div>
        
        <div class="filtro-botoes">
            <button type="submit" class="btn btn-primary btn-filtro">
                <i class="fas fa-search"></i> Pesquisar
            </button>
            <a href="<?= BASE_URL ?>/admin/reunioes/listar" class="btn btn-secondary btn-filtro">
                <i class="fas fa-sync"></i> Limpar
            </a>
        </div>
    </form>
</div>

<div class="acoes-container">
    <a href="<?= BASE_URL ?>/admin/reunioes/criar" class="btn-nova-reuniao">
        <i class="fas fa-plus"></i> Nova Reunião
    </a>
    <div class="total-reunioes">
        Total de reuniões encontradas: <?= count($reunioes) ?>
    </div>
</div>
<div class="total-reunioes">
    Clique no título ou na data para organizar a lista em ordem crescente ou decrescente

</div>



<div class="table-responsive tabela-reunioes">
    <table class="table table-hover table-striped">
        <thead class="table-light">
            <tr>
                <th>
                    <a href="?<?= http_build_query(array_merge($_GET, ['ordem' => 'titulo'])) ?>" class="text-decoration-none">
                        Título <?= isset($_GET['ordem']) && $_GET['ordem'] == 'titulo' ? '↓' : '' ?>
                    </a>
                </th>
                <th>
                    <a href="?<?= http_build_query(array_merge($_GET, ['ordem' => 'data'])) ?>" class="text-decoration-none">
                        Data <?= isset($_GET['ordem']) && $_GET['ordem'] == 'data' ? '↓' : '' ?>
                    </a>
                </th>
                <th>Local</th>
                <th>Criador</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reunioes)): ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhuma reunião encontrada</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reunioes as $reuniao): ?>
                    <tr>
                        <td><?= htmlspecialchars($reuniao['titulo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($reuniao['data_reuniao'])) ?></td>
                        <td><?= htmlspecialchars($reuniao['local_reuniao']) ?></td>
                        <td><?= htmlspecialchars($reuniao['criador']) ?></td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="<?= BASE_URL ?>admin/reunioes/visualizar/<?= $reuniao['id'] ?>"
                                    class="btn-info btn-acao" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>admin/reunioes/editar/<?= $reuniao['id'] ?>"
                                    class="btn-warning btn-acao" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>admin/reunioes/excluir/<?= $reuniao['id'] ?>"
                                    class="btn-danger btn-acao"
                                    onclick="return confirm('Tem certeza que deseja excluir esta reunião?');"
                                    title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="<?= BASE_URL ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Voltar ao Início
</a>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>