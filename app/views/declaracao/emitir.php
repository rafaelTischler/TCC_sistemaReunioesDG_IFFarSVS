<?php

require_once __DIR__ . '/../../../includes/header.php';


?>

<h2>Emitir Declaração de Participação</h2>

<?php if (isset($_SESSION['erro_declaracao'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro_declaracao'];
        unset($_SESSION['erro_declaracao']); ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>processar-emissao">
    <div class="form-group" style="position:relative;">
        <label for="usuario_search">Servidor:</label>
        <input
            type="text"
            id="usuario_search"
            name="usuario_search"
            class="form-input"
            placeholder="Digite nome ou matrícula"
            autocomplete="off"
            required>
        <input type="hidden" name="usuario_id" id="usuario_id" value="">
        <div id="usuario_suggestions" class="suggestions" role="listbox" aria-label="Sugestões de servidores"></div>
    </div>


    <div class="form-group">
        <label>Período - Data Início:</label>
        <input type="date" name="data_inicio" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Período - Data Fim:</label>
        <input type="date" name="data_fim" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Emitir Declaração</button>
</form>

<a href="<?= BASE_URL ?>" class="btn btn-secondary mt-3">Voltar ao Início</a>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

<script src="<?= BASE_URL ?>js/autocompletar_servidores.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const servidores = <?= json_encode(array_values($servidores), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?> || [];
    
    inicializarAutocompleteServidores({
        servidores: servidores,
        inputId: 'usuario_search',
        hiddenId: 'usuario_id',
        suggestionsBoxId: 'usuario_suggestions',
        autoSubmit: false
    });

    const form = document.getElementById('form-emissao');
    form.addEventListener('submit', function(e){
        const usuarioId = document.getElementById('usuario_id').value;
        if (!usuarioId) {
            e.preventDefault();
            alert('Por favor selecione um servidor válido na lista antes de enviar.');
            document.getElementById('usuario_search').focus();
        }
    });
});
</script>