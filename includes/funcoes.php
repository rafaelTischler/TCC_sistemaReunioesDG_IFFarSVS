<?php

require_once __DIR__ . '/../vendor/autoload.php';

function formatarData($data, $formato = 'd/m/Y H:i')
{
    $date = new DateTime($data);
    return $date->format($formato);
}

function getCaminhoLogo()
{
    $caminho = __DIR__ . '/../assets/images/logo-iffar-svs.jpg';
    if (file_exists($caminho)) {
        return $caminho;
    }
    return '';
}

function gerarDeclaracaoPDF($dados)
{
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font_size' => 12,
        'default_font' => 'times',
        'margin_left' => 30,
        'margin_right' => 30,
        'margin_top' => 70,
        'margin_bottom' => 30,
        'margin_header' => 5,
        'margin_footer' => 10
    ]);

    //cabeçalho HTML das declarações
    $cabecalhoHTML = '
<div style="text-align: center; margin-bottom: 10px; line-height: 1.2; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
        <img src="' . getCaminhoLogo() . '" style="width: 100px; height: auto; margin-right: 15px;" alt="Logo IFFar SVS">
        <div style="text-align: center;">
            <div style="font-size: 12px; font-weight: bold; margin: 2px 0;">MINISTÉRIO DA EDUCAÇÃO</div>
            <div style="font-size: 12px; font-weight: bold; margin: 2px 0;">SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA</div>
            <div style="font-size: 12px; font-weight: bold; margin: 2px 0;">INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA FARROUPILHA</div>
            <div style="font-size: 11px; font-weight: bold; margin: 2px 0; font-style: italic;">CAMPUS SÃO VICENTE DO SUL</div>
        </div>
    </div>
    <div style="font-size: 9px; margin: 1px 0;">Rua 20 de Setembro, nº 2616 - 97420-000 – São Vicente do Sul – RS</div>
    <div style="font-size: 9px; margin: 1px 0;">Campus São Vicente do Sul - Fone: (55) 3218-8500 - E-mail: gabinete.svs@iffarroupilha.edu.br</div>
</div>';

    //configura o cabeçalho para todas as páginas
    $mpdf->SetHTMLHeader($cabecalhoHTML);

    // HTML do conteúdo principal
    $html = '
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 12px; 
            line-height: 1.3;
            text-align: justify;
        }
        .titulo-principal {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0 20px 0;
            text-decoration: underline;
        }
        .texto-declaracao {
            text-align: justify;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .tabela-reunioes {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .tabela-reunioes th {
            border: 1px solid #000;
            padding: 8px;
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .tabela-reunioes td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .data-col {
            width: 80px;
            text-align: center;
            white-space: nowrap;
        }
        .assinatura {
            margin-top: 60px;
            text-align: center;
        }
        .data-local {
            margin-top: 30px;
            text-align: right;
        }
        .rodape {
            margin-top: 10px;
            font-size: 10px;
            text-align: center;
            line-height: 1.2;
        }
        .bold {
            font-weight: bold;
        }
    </style>

    <!-- Título Principal -->
    <div class="titulo-principal">DECLARAÇÃO</div>

    <!-- Texto da Declaração -->
    <div class="texto-declaracao">
        Pelo presente documento, para fins de progressão funcional, DECLARO que
        o servidor <span class="bold">' . $dados['servidor_nome'] . '</span>, ocupante do cargo de <span class="bold">' . $dados['servidor_cargo'] . '</span>,
        SIAPE nº <span class="bold">' . $dados['servidor_matricula'] . '</span>, participou das reuniões/atividades
        institucionais abaixo indicadas:
    </div>

    <!-- Tabela de Reuniões -->
    <table class="tabela-reunioes">
        <thead>
            <tr>
                <th style="width: 15%;">Data</th>
                <th style="width: 85%;">Atividade</th>
            </tr>
        </thead>
        <tbody>';

    //lista de reuniões
    if (empty($dados['reunioes']) || !is_array($dados['reunioes'])) {
        $html .= '
            <tr>
                <td colspan="2" style="text-align: center; font-style: italic;">Nenhuma reunião encontrada.</td>
            </tr>';
    } else {
        foreach ($dados['reunioes'] as $reuniao) {
            //formatar data
            $data = '';
            if (!empty($reuniao['data_reuniao'])) {
                try {
                    $data = (new DateTime($reuniao['data_reuniao']))->format('d/m/Y');
                } catch (Exception $e) {
                    $data = $reuniao['data_reuniao'];
                }
            }

            $titulo = $reuniao['titulo'] ?? '';
            $descricao = $reuniao['descricao'] ?? '';

            $html .= '
            <tr>
                <td class="data-col">' . $data . '</td>
                <td>
                    <div class="bold">' . $titulo . '</div>
                    <div>' . $descricao . '</div>
                </td>
            </tr>';
        }
    }

    $html .= '
        </tbody>
    </table>

    <!-- Data e Local -->
    <div class="data-local">
        São Vicente do Sul, RS, ' . dataPorExtenso() . '.
    </div>

    <!-- Assinatura -->
    <div class="assinatura">
        <div class="bold" style="margin-bottom: 10px;">Chefia de Gabinete</div>
        <div>Chefe de Gabinete</div>
    </div>

    <!-- Rodapé -->
    <div class="rodape">
        Portaria Eletrônica nº XXX/20XX
    </div>';

    //escreve o HTML no PDF
    $mpdf->WriteHTML($html);

    return $mpdf;
}

function dataPorExtenso()
{
    $meses = [
        1 => 'janeiro',
        2 => 'fevereiro',
        3 => 'março',
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro'
    ];

    $dia = date('d');
    $mes = $meses[(int)date('m')];
    $ano = date('Y');

    return $dia . ' de ' . $mes . ' de ' . $ano;
}

function downloadDeclaracaoPDF($dados)
{
    $mpdf = gerarDeclaracaoPDF($dados);

    //formata o nome do arquivo
    $nomeServidor = $dados['servidor_nome'];
    $dataInicio = $dados['data_inicio'];
    $dataFim = $dados['data_fim'];

    //remove barras das datas para usar no nome do arquivo
    $dataInicioFile = str_replace('/', '-', $dataInicio);
    $dataFimFile = str_replace('/', '-', $dataFim);

    //cria nome do arquivo
    $filename = $nomeServidor . ' - Declaração de Participação em Reuniões - ' . $dataInicioFile . '_' . $dataFimFile . '.pdf';
    $filename = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚâêîôÂÊÎÔãõÃÕçÇ\s\-_\.]/u', '', $filename);
    $filename = preg_replace('/\s+/', ' ', $filename);

    //força o download
    $mpdf->Output($filename, 'D');
    exit;
}

function gerarDeclaracaoHTML($dados)
{
    ob_start();
?>
    <div class="declaracao-container">
        <h2 class="declaracao-title">DECLARAÇÃO DE PARTICIPAÇÃO EM REUNIÕES</h2>
        <p>
            Pelo presente documento, para fins de progressão funcional, DECLARO que o servidor <strong><?= htmlspecialchars($dados['servidor_nome']) ?></strong>,
            matrícula SIAPE nº <strong><?= htmlspecialchars($dados['servidor_matricula']) ?></strong>,
            ocupante do cargo de <strong><?= htmlspecialchars($dados['servidor_cargo']) ?></strong>,
            participou das reuniões e atividade institucionais no período de
            <strong><?= htmlspecialchars($dados['data_inicio']) ?></strong> a
            <strong><?= htmlspecialchars($dados['data_fim']) ?></strong> abaixo indicadas:
        </p>

        <h4>Reuniões Participadas:</h4>

        <table class="declaracao-table" style="width:100%; border-collapse:collapse;">
            <tbody>
                <?php if (empty($dados['reunioes']) || !is_array($dados['reunioes'])): ?>
                    <tr>
                        <td colspan="2" style="padding:12px; text-align:center; color:#555;">Nenhuma reunião encontrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dados['reunioes'] as $reuniao): ?>
                        <?php
                        $data = '';
                        if (!empty($reuniao['data_reuniao'])) {
                            try {
                                $data = (new DateTime($reuniao['data_reuniao']))->format('d/m/Y');
                            } catch (Exception $e) {
                                $data = htmlspecialchars($reuniao['data_reuniao']);
                            }
                        }

                        $titulo = htmlspecialchars($reuniao['titulo'] ?? '');
                        $descricao = nl2br(htmlspecialchars($reuniao['descricao'] ?? ''));
                        ?>
                        <tr>
                            <td style="vertical-align:top; padding:10px 12px; width:140px; white-space:nowrap; font-weight:600;"><?= $data ?></td>
                            <td style="padding:10px 12px;">
                                <div style="font-weight:700; margin-bottom:6px;"><?= $titulo ?></div>
                                <div style="color:#555; font-size:0.95rem;"><?= $descricao ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <p class="declaracao-data">
            São Vicente do Sul, <?= date('d/m/Y') ?>
        </p>

        <div class="declaracao-assinatura">
            <p>___________________________________________</p>
            <p>Assinatura do Responsável</p>
        </div>
    </div>

    <!--botões para voltar e baixar PDF -->
    <div class="mt-4">
        <a href="<?= htmlspecialchars(BASE_URL) ?>" class="btn btn-secondary">Voltar</a>

        <!--formulário para download do PDF-->
        <form action="" method="POST" style="display:inline;">
            <input type="hidden" name="baixar_pdf" value="1">
            <button type="submit" class="btn btn-primary" style="margin-left:10px;">
                📄 Baixar Declaração em PDF
            </button>
        </form>
    </div>

<?php
    return ob_get_clean();
}

function buscarDeclaracoesServidor($usuario_id, $conn)
{
    $sql = "SELECT d.*, u.nome as emitido_por_nome 
            FROM declaracoes d 
            LEFT JOIN usuarios u ON d.emitido_por = u.id 
            WHERE d.usuario_id = ? 
            ORDER BY d.criado_em DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $declaracoes = [];
    while ($row = $result->fetch_assoc()) {
        $declaracoes[] = $row;
    }

    return $declaracoes;
}

function buscarDeclaracaoPorId($declaracao_id, $conn)
{
    $sql = "SELECT d.*, u.nome as servidor_nome, u.matricula_siape as servidor_matricula, 
                   u.cargo as servidor_cargo, u2.nome as emitido_por_nome
            FROM declaracoes d 
            INNER JOIN usuarios u ON d.usuario_id = u.id 
            LEFT JOIN usuarios u2 ON d.emitido_por = u2.id 
            WHERE d.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $declaracao_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

function downloadDeclaracaoPorId($declaracao_id, $conn)
{
    $declaracao = buscarDeclaracaoPorId($declaracao_id, $conn);

    if (!$declaracao) {
        return false;
    }

    //buscar reuniões do período da declaração
    $reunioes_sql = "SELECT r.titulo, r.descricao, r.data_reuniao 
                     FROM presencas p 
                     INNER JOIN reunioes r ON p.reuniao_id = r.id 
                     WHERE p.usuario_id = ? 
                     AND r.data_reuniao BETWEEN ? AND ? 
                     ORDER BY r.data_reuniao";

    $stmt_reunioes = $conn->prepare($reunioes_sql);
    $stmt_reunioes->bind_param("iss", $declaracao['usuario_id'], $declaracao['data_inicio'], $declaracao['data_fim']);
    $stmt_reunioes->execute();
    $reunioes_result = $stmt_reunioes->get_result();

    $reunioes = [];
    while ($row = $reunioes_result->fetch_assoc()) {
        $reunioes[] = $row;
    }

    //preparar dados usando a função
    $dados = [
        'servidor_nome' => $declaracao['servidor_nome'],
        'servidor_matricula' => $declaracao['servidor_matricula'],
        'servidor_cargo' => $declaracao['servidor_cargo'],
        'data_inicio' => formatarData($declaracao['data_inicio'], 'd/m/Y'),
        'data_fim' => formatarData($declaracao['data_fim'], 'd/m/Y'),
        'reunioes' => $reunioes
    ];
    downloadDeclaracaoPDF($dados);
    return true;
}

function visualizarDeclaracaoPorId($declaracao_id, $conn)
{
    $declaracao = buscarDeclaracaoPorId($declaracao_id, $conn);

    if (!$declaracao) {
        return false;
    }
    $reunioes_sql = "SELECT r.titulo, r.descricao, r.data_reuniao 
                     FROM presencas p 
                     INNER JOIN reunioes r ON p.reuniao_id = r.id 
                     WHERE p.usuario_id = ? 
                     AND r.data_reuniao BETWEEN ? AND ? 
                     ORDER BY r.data_reuniao";

    $stmt_reunioes = $conn->prepare($reunioes_sql);
    $stmt_reunioes->bind_param("iss", $declaracao['usuario_id'], $declaracao['data_inicio'], $declaracao['data_fim']);
    $stmt_reunioes->execute();
    $reunioes_result = $stmt_reunioes->get_result();

    $reunioes = [];
    while ($row = $reunioes_result->fetch_assoc()) {
        $reunioes[] = $row;
    }

    $dados = [
        'servidor_nome' => $declaracao['servidor_nome'],
        'servidor_matricula' => $declaracao['servidor_matricula'],
        'servidor_cargo' => $declaracao['servidor_cargo'],
        'data_inicio' => formatarData($declaracao['data_inicio'], 'd/m/Y'),
        'data_fim' => formatarData($declaracao['data_fim'], 'd/m/Y'),
        'reunioes' => $reunioes
    ];

    //gerar PDF e abrir no navegador
    $mpdf = gerarDeclaracaoPDF($dados);

    $nomeServidor = $dados['servidor_nome'];
    $dataInicio = $dados['data_inicio'];
    $dataFim = $dados['data_fim'];

    $filename = $nomeServidor . ' - Declaração - ' . str_replace('/', '-', $dataInicio) . '_' . str_replace('/', '-', $dataFim) . '.pdf';
    $filename = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚâêîôÂÊÎÔãõÃÕçÇ\s\-_\.]/u', '', $filename);

    $mpdf->Output($filename, 'I');
    return true;
}


function verificarAcessibilidade()
{
    $url = BASE_URL . 'check.php';
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200');
}

function getUsuarioNome()
{
    return $_SESSION['usuario_nome'] ?? 'Visitante';
}

function getUsuarioId()
{
    return $_SESSION['usuario_id'] ?? null;
}

function getUsuarioMatricula()
{
    return $_SESSION['usuario_matricula'] ?? null;
}

function isAdmin()
{
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'administrador';
}

function isGestor()
{
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'gestor';
}

function isServidor()
{
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'servidor';
}

function isAdminOrGestor()
{
    return isAdmin() || isGestor();
}

function getTipoUsuario()
{
    return $_SESSION['tipo_usuario'] ?? 'servidor';
}

function getTipoUsuarioLabel()
{
    $tipo = getTipoUsuario();
    $labels = [
        'administrador' => 'Administrador',
        'gestor' => 'Gestor',
        'servidor' => 'Servidor'
    ];
    return $labels[$tipo] ?? 'Servidor';
}

function url($path)
{
    return BASE_URL . ltrim($path, '/');
}
