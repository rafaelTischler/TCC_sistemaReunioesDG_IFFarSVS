<!-- NÃO IMPLEMENTADO POR PROBLEMAS COM PROTOCOLO (HTTP's') -->


<!-- <!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escanear QR Code - <?= SISTEMA_NOME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
    <style>
        .scanner-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .scanner-title {
            margin-bottom: 30px;
            color: #333;
        }

        .scanner-wrapper {
            position: relative;
            width: 100%;
            max-height: 250px;
            max-width: 250px;
            margin: 0 auto 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
        }

        #reader {
            width: 100%;
            height: 400px;
            position: relative;
        }

        /* Quadrado central de foco */
        .scan-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 3px solid #a8a8a8ff;
            border-radius: 15px;
            background: transparent;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.4);
            z-index: 10;
            pointer-events: none;
            animation: pulse 2s infinite;
        }

        /* Cantos decorativos do quadrado */
        .scan-frame::before,
        .scan-frame::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid #939393ff;
        }

        .scan-frame::before {
            top: -3px;
            left: -3px;
            border-right: none;
            border-bottom: none;
            border-top-left-radius: 10px;
        }

        .scan-frame::after {
            bottom: -3px;
            right: -3px;
            border-left: none;
            border-top: none;
            border-bottom-right-radius: 10px;
        }

        .scan-frame .corner-top-right {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 20px;
            height: 20px;
            border: 3px solid #868686ff;
            border-left: none;
            border-bottom: none;
            border-top-right-radius: 10px;
        }

        .scan-frame .corner-bottom-left {
            position: absolute;
            bottom: -3px;
            left: -3px;
            width: 20px;
            height: 20px;
            border: 3px solid #919191ff;
            border-right: none;
            border-top: none;
            border-bottom-left-radius: 10px;
        }

        /* Animação de pulsação */
        @keyframes pulse {
            0% {
                border-color: #c4c4c4ff;
                box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.4);
            }

            50% {
                border-color: #b3b3b3ff;
                box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.3);
            }

            100% {
                border-color: #909090ff;
                box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.4);
            }
        }

        /* Instruções */
        .scan-instructions {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            border-radius: 5px;
            margin: 0 20px;
            font-size: 14px;
            z-index: 11;
        }

        .scanner-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }

        .result-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .result-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .scanner-actions {
            margin-top: 20px;
        }

        .btn-scan-again {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: none;
            margin: 5px;
        }

        .camera-permission {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
        }

        .loading-scanner {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 400px;
            color: white;
            font-size: 16px;
            background: #1a1a1a;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <?php
    require_once __DIR__ . '/../../../includes/header.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="scanner-container">
                <h1 class="scanner-title">
                    <i class="fas fa-qrcode"></i> Escanear QR Code
                </h1>

                <div class="camera-permission">
                    <i class="fas fa-info-circle"></i>
                    Permita o acesso à câmera para escanear o QR Code da reunião
                </div>

                <div class="scanner-wrapper">
                    <div id="reader">
                        
                       
                    </div>
                </div>

                <div id="scannerResult" class="scanner-result"></div>

                <div class="scanner-actions">
                    <button id="btnScanAgain" class="btn-scan-again" onclick="initScanner()">
                        <i class="fas fa-redo"></i> Escanear Novamente
                    </button>
                    <a href="<?= BASE_URL ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner;
        let isScanning = false;

        function initScanner() {
            // Limpar resultado anterior
            document.getElementById('scannerResult').style.display = 'none';
            document.getElementById('btnScanAgain').style.display = 'none';

            // Se já existe um scanner, parar primeiro
            if (html5QrcodeScanner && isScanning) {
                html5QrcodeScanner.clear();
            }



            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0,
                    showTorchButtonIfSupported: true
                },
                false
            );

            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            isScanning = true;
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Parar o scanner temporariamente
            html5QrcodeScanner.clear();
            isScanning = false;

            // Mostrar loading
            document.getElementById('scannerResult').innerHTML =
                '<p><i class="fas fa-spinner fa-spin"></i> Processando QR Code...</p>';
            document.getElementById('scannerResult').className = 'scanner-result';
            document.getElementById('scannerResult').style.display = 'block';

            // Enviar dados para o servidor
            fetch('<?= BASE_URL ?>scanner-qrcode/processar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        qrcode_data: decodedText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('scannerResult');

                    if (data.success) {
                        resultDiv.className = 'scanner-result result-success';
                        resultDiv.innerHTML = `
                        <h4><i class="fas fa-check-circle"></i> Sucesso!</h4>
                        <p><strong>${data.message}</strong></p>
                        ${data.reuniao ? `<p><strong>Reunião:</strong> ${data.reuniao}</p>` : ''}
                        ${data.data ? `<p><strong>Data:</strong> ${data.data}</p>` : ''}
                    `;
                    } else {
                        resultDiv.className = 'scanner-result result-error';
                        resultDiv.innerHTML = `
                        <h4><i class="fas fa-exclamation-triangle"></i> Erro</h4>
                        <p>${data.message}</p>
                        ${data.reuniao ? `<p><strong>Reunião:</strong> ${data.reuniao}</p>` : ''}
                    `;
                    }

                    document.getElementById('btnScanAgain').style.display = 'inline-block';
                })
                .catch(error => {
                    console.error('Erro:', error);
                    const resultDiv = document.getElementById('scannerResult');
                    resultDiv.className = 'scanner-result result-error';
                    resultDiv.innerHTML = `
                    <h4><i class="fas fa-exclamation-triangle"></i> Erro</h4>
                    <p>Erro ao processar QR Code. Tente novamente.</p>
                `;
                    document.getElementById('btnScanAgain').style.display = 'inline-block';
                });
        }

        function onScanFailure(error) {
            // Erros são esperados quando não há QR Code na câmera
        }

        // Inicializar scanner quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            initScanner();
        });

        // Adicionar efeito de foco quando o mouse passa sobre o scanner
        document.addEventListener('DOMContentLoaded', function() {
            const scannerWrapper = document.querySelector('.scanner-wrapper');
            const scanFrame = document.querySelector('.scan-frame');

            scannerWrapper.addEventListener('mouseenter', function() {
                scanFrame.style.borderColor = '#b4b4b4ff';
                scanFrame.style.animationDuration = '1s';
            });

            scannerWrapper.addEventListener('mouseleave', function() {
                scanFrame.style.borderColor = '#c5c5c5ff';
                scanFrame.style.animationDuration = '2s';
            });
        });
    </script>
</body>

</html>


<?php require_once __DIR__ . '/../../../includes/footer.php'; ?> -->