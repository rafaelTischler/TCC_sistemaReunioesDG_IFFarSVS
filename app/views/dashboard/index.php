<?php
require_once __DIR__ . '/../../../includes/header.php';


//inicialização dos models
$reuniaoModel = new Reuniao(getDB());
$presencaModel = new Presenca(getDB());
$usuario_id = getUsuarioId();

//dados básicos (disponíveis para todos os usuários)
$total_reunioes_realizadas = Reuniao::countAllReunioes();
$presencas_servidor = Presenca::countByUsuario(getDB(), $usuario_id);
$ausencias_servidor = $total_reunioes_realizadas - $presencas_servidor;
$taxa_participacao = $total_reunioes_realizadas > 0 ?
    round(($presencas_servidor / $total_reunioes_realizadas) * 100, 1) : 0;
$ultima_presenca = Presenca::getUltimaPresencaUsuario(getDB(), $usuario_id);
$proximas_reunioes = $reuniaoModel->listarProximas();

//dados específicos pra Administradores e Gestores
if (Auth::isAdminOrGestor()) {
    $total_reunioes = Reuniao::countAllReunioes();
    $ultima_reuniao = $reuniaoModel->getUltima();
    $presencas_ultima_reuniao = $ultima_reuniao ?
        $reuniaoModel->contarPresencas($ultima_reuniao['id']) : 0;
    $presencas_por_mes = Presenca::getPresencasPorMes(getDB());
}

//dados exclusivos para Administradores
if (Auth::isAdmin()) {
    $total_usuarios = Usuario::countAllUsuarios();
}
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <?php if (Auth::isAdminOrGestor()): ?>
            <!--estatísticas gerais pra Administradores e Gestores-->
            <div class="dashboard-cards">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-content">
                            <div class="stat-title">Total de Reuniões</div>
                            <div class="stat-value"><?= $total_reunioes ?></div>
                        </div>
                    </div>
                </div>

                <?php if (Auth::isAdmin()): ?>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-title">Total de Usuários</div>
                                <div class="stat-value"><?= $total_usuarios ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-content">
                            <div class="stat-title">Presenças na Última Reunião</div>
                            <div class="stat-value"><?= $presencas_ultima_reuniao ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!--gráfico de evolução por mês pra Administrador e Gestor-->
            <div class="chart-container">
                <h2 class="chart-title">
                    <span class="chart-icon"></span>
                    Evolução de Presenças por Mês
                </h2>
                <div class="chart-wrapper">
                    <canvas id="presenceChart"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <!--estatísticas pessoais de todos os usuários-->
        <div class="dashboard-cards mt-3">
            <h2 class="chart-title" style="grid-column: 1 / -1;">
                <span class="chart-icon"></span>
                <?= Auth::isAdminOrGestor() ? 'Minhas Estatísticas Pessoais' : 'Minhas Estatísticas' ?>
            </h2>

            <div class="stat-card" onclick="window.location.href='<?= BASE_URL ?>servidor/presenca/listar'" style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Minhas Presenças</div>
                        <div class="stat-value"><?= $presencas_servidor ?></div>
                        <div class="stat-comparison">
                            <span>de <?= $total_reunioes_realizadas ?> reuniões</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card" onclick="scrollParaGrafico()" style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Taxa de Participação</div>
                        <div class="stat-value"><?= $taxa_participacao ?>%</div>
                        <div class="stat-comparison">
                            <?php if ($taxa_participacao >= 80): ?>
                                <span class="trend-up">↑ Excelente participação</span>
                            <?php elseif ($taxa_participacao >= 60): ?>
                                <span class="trend-neutral">→ Boa participação</span>
                            <?php else: ?>
                                <span class="trend-down">↓ Participação pode melhorar</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Última Presença</div>
                        <div class="stat-value">
                            <?= $ultima_presenca ?
                                date('d/m/Y', strtotime($ultima_presenca['data_presenca'])) :
                                'Nenhuma presença' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--gráficos pra todos usuários-->
        <div class="charts-grid mt-3">
            <div class="chart-container">
                <h2 class="chart-title">
                    <span class="chart-icon"></span>
                    Minha Participação
                </h2>
                <div class="chart-wrapper">
                    <canvas id="participationChart"></canvas>
                </div>
                <div class="participation-info">
                    <div class="participation-item">
                        <div class="color-indicator color-present"></div>
                        <span>Presenças: <?= $presencas_servidor ?> (<?= $taxa_participacao ?>%)</span>
                    </div>
                    <div class="participation-item">
                        <div class="color-indicator color-absent"></div>
                        <span>Ausências: <?= $ausencias_servidor ?> (<?= round(100 - $taxa_participacao, 1) ?>%)</span>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h2 class="chart-title">
                    <span class="chart-icon"></span>
                    Próximas Reuniões
                </h2>
                <div class="meetings-list">
                    <?php if (!empty($proximas_reunioes)): ?>
                        <?php foreach ($proximas_reunioes as $reuniao): ?>
                            <div class="meeting-item">
                                <div class="meeting-title"><?= htmlspecialchars($reuniao['titulo']) ?></div>
                                <div class="meeting-details">
                                    <?= date('d/m/Y', strtotime($reuniao['data_reuniao'])) ?>
                                    • <?= date('H:i', strtotime($reuniao['data_reuniao'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="meeting-item">
                            <div class="meeting-title">Nenhuma reunião agendada</div>
                            <div class="meeting-details">Aguarde novas reuniões</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- script pra geração dos gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script>
    //configurações básicas do chart.js
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e2e8f0';
    Chart.defaults.backgroundColor = '#3b82f6';

    function initializeCharts() {
        const isMobile = window.innerWidth < 768;
        const responsiveConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: '#e2e8f0'
                    },
                    ticks: {
                        font: {
                            size: isMobile ? 10 : 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: isMobile ? 10 : 12
                        }
                    }
                }
            }
        };

        //gráfico de participação para todos
        const participationData = {
            datasets: [{
                data: [<?= $presencas_servidor ?>, <?= $ausencias_servidor ?>],
                backgroundColor: ['#4a58edff', '#eeeeeeff'],
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        };

        new Chart(
            document.getElementById('participationChart'), {
                type: 'pie',
                data: participationData,
                options: {
                    ...responsiveConfig,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            }
        );

        <?php if (Auth::isAdminOrGestor()): ?>
            //gráfico de evolução de presenças por mês (apenas para Administradores e Gestores)
            const evolutionData = {
                labels: <?= json_encode(array_keys($presencas_por_mes)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($presencas_por_mes)) ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            };

            new Chart(
                document.getElementById('presenceChart'), {
                    type: 'line',
                    data: evolutionData,
                    options: responsiveConfig
                }
            );
        <?php endif; ?>
    }


    document.addEventListener('DOMContentLoaded', initializeCharts);
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(initializeCharts, 250);
    });

    function scrollParaGrafico() {
        const grafico = document.getElementById('participationChart');

        if (grafico) {
            // scroll até o gráfico
            const chartContainer = grafico.closest('.chart-container');

            if (chartContainer) {
                //suavidade do scroll
                chartContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });


                setTimeout(() => {
                    chartContainer.style.boxShadow = 'none';
                }, 1500);
            } else {
                //scroll direto para o canvas caso nao dê certo
                grafico.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>