<?php
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Estatísticas por Servidor</h2>

    <!--barra de pesquisa com autocompletar -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" class="form-inline" id="form-pesquisa">
                <div class="form-group d-flex" style="gap: 10px; width: 100%; align-items: center; position: relative;"">
                    <input
                        type=" text"
                    id="servidor_search"
                    name="search_display"
                    class="form-control"
                    placeholder="Digite nome ou SIAPE do servidor..."
                    autocomplete="off"
                    value="<?php echo isset($_GET['search_display']) ? htmlspecialchars($_GET['search_display']) : ''; ?>">
                    <input
                        type="hidden"
                        id="servidor_id"
                        name="search"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <div id="servidor_suggestions" class="suggestions" role="listbox" aria-label="Sugestões de servidores"></div>
                </div>
                <button class="btn btn-primary ml-2" type="submit">Pesquisar</button>
                <a href="<?= BASE_URL ?>" class="btn btn-secondary">Voltar ao Início</a>

            </form>
        </div>
    </div>

    <!--resultados da pesquisa-->
    <?php if ($resultados && mysqli_num_rows($resultados) > 0): ?>
        <div class="servidores-lista">
            <?php while ($row = mysqli_fetch_assoc($resultados)): ?>
                <div class="servidor-card">
                    <div class="servidor-header">
                        <div class="servidor-nome">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($row['nome']); ?>
                        </div>
                        <div class="servidor-percentual">
                            <span class="percentual-text">Taxa de participação:</span>
                            <span class="percentual-badge <?php echo $row['percentual_presenca'] >= 80 ? 'alta-frequencia' : ($row['percentual_presenca'] >= 60 ? 'media-frequencia' : 'baixa-frequencia'); ?>">
                                <?php echo $row['percentual_presenca']; ?>%
                            </span>
                        </div>
                    </div>
                    <div class="servidor-info">
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-id-card"></i> SIAPE:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['siape']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-briefcase"></i> Cargo:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['cargo']); ?></span>
                        </div>
                    </div>

                    <!--estatísticas-->
                    <div class="servidor-estatisticas">
                        <div class="estatistica-item">
                            <div class="estatistica-numero"><?php echo $row['total_reunioes']; ?></div>
                            <div class="estatistica-label">Total de Reuniões</div>
                        </div>
                        <div class="estatistica-item">
                            <div class="estatistica-numero estatistica-sucesso"><?php echo $row['presencas']; ?></div>
                            <div class="estatistica-label">Presenças</div>
                        </div>
                        <div class="estatistica-item">
                            <div class="estatistica-numero estatistica-alerta"><?php echo $row['faltas']; ?></div>
                            <div class="estatistica-label">Faltas</div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php elseif (isset($_GET['search'])): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Nenhum resultado encontrado para a pesquisa.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

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
    }

    .suggestions .item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .suggestions .item:hover,
    .suggestions .item.active {
        background-color: #f8f9fa;
    }

    .suggestions .item .titulo {
        font-weight: bold;
    }

    .suggestions .item .meta {
        font-size: 0.9em;
        color: #666;
    }

    .form-group {
        position: relative;
    }

    .servidores-lista {
        display: grid;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .servidor-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .servidor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .servidor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f8f9fa;
    }

    .servidor-nome {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .servidor-nome i {
        color: #3498db;
        font-size: 1.3rem;
    }

    .servidor-percentual {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .percentual-text {
        font-size: 0.95rem;
        color: #7f8c8d;
        font-weight: 500;
    }

    .percentual-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .alta-frequencia {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }

    .media-frequencia {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
    }

    .baixa-frequencia {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }

    .servidor-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-label {
        font-weight: 500;
        color: #5d6d7e;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-value {
        font-weight: 600;
        color: #2c3e50;
        text-align: right;
        font-size: 1.05rem;
    }

    .servidor-estatisticas {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        text-align: center;
    }

    .estatistica-item {
        padding: 1.5rem 1rem;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }

    .estatistica-item:hover {
        background: linear-gradient(135deg, #e9ecef, #dee2e6);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .estatistica-numero {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .estatistica-sucesso {
        color: #27ae60;
    }

    .estatistica-alerta {
        color: #e74c3c;
    }

    .estatistica-label {
        font-size: 0.95rem;
        color: #5d6d7e;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .servidor-card {
            padding: 1.5rem;
            margin: 0 0.5rem;
        }

        .servidor-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            text-align: left;
        }

        .servidor-percentual {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .servidor-nome {
            font-size: 1.3rem;
        }

        .servidor-info {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
            text-align: left;
        }

        .info-value {
            text-align: left;
        }

        .servidor-estatisticas {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .estatistica-item {
            padding: 1.25rem;
        }

        .estatistica-numero {
            font-size: 1.75rem;
        }
    }

    @media (min-width: 769px) and (max-width: 1200px) {
        .servidores-lista {
            grid-template-columns: 1fr;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .servidor-card {
            padding: 2rem;
        }
    }

    @media (min-width: 1201px) {
        .servidores-lista {
            grid-template-columns: 1fr;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .servidor-card {
            padding: 2.5rem;
        }

        .servidor-nome {
            font-size: 1.6rem;
        }

        .estatistica-numero {
            font-size: 2.25rem;
        }
    }

    .servidores-lista {
        margin-top: 2.5rem;
    }
</style>

<script src="<?= BASE_URL ?>js/autocompletar_servidores.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const servidores = <?= json_encode($servidores ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?> || [];

        inicializarAutocompleteServidores({
            servidores: servidores,
            inputId: 'servidor_search',
            hiddenId: 'servidor_id',
            suggestionsBoxId: 'servidor_suggestions',
            autoSubmit: true,
            onSelect: function(servidor) {
                document.getElementById('servidor_id').value = servidor.id;
                const displayText = `(${servidor.tipo}) ${servidor.nome}\t${servidor.matricula_siape}`;
                document.getElementById('servidor_search').value = displayText;
                document.getElementById('form-pesquisa').submit();
            }
        });
    });
</script>