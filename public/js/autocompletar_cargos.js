
function inicializarAutocompleteCargos(config) {
    const {
        cargosPorCategoria,
        inputId,
        hiddenId,
        suggestionsBoxId,
        onSelect = null
    } = config;

    const input = document.getElementById(inputId);
    const hiddenInput = document.getElementById(hiddenId);
    const suggestionsBox = document.getElementById(suggestionsBoxId);

    if (!input || !hiddenInput || !suggestionsBox) {
        console.error('Elementos não encontrados para autocomplete de cargos');
        return;
    }

    //prepara lista de cargos para busca
    const todosCargos = [];
    for (const [categoria, cargos] of Object.entries(cargosPorCategoria)) {
        cargos.forEach(cargo => {
            todosCargos.push({
                nome: cargo,
                categoria: categoria,
                id: cargo.toLowerCase().replace(/\s+/g, '_')
            });
        });
    }

    let currentFocus = -1;
    let isOpen = false;

    //abre lista ao clicar no campo
    input.addEventListener('click', function() {
        if (!isOpen) {
            mostrarTodosCargos();
        }
    });

    input.addEventListener('input', function(e) {
        const valor = this.value.trim();
        
        if (!valor) {
            hiddenInput.value = '';
            mostrarTodosCargos();
            return;
        }

        currentFocus = -1;
        
        // Filtrar cargos
        const resultados = todosCargos.filter(cargo =>
            cargo.nome.toLowerCase().includes(valor.toLowerCase()) ||
            cargo.categoria.toLowerCase().includes(valor.toLowerCase())
        );

        // Exibir sugestões
        if (resultados.length > 0) {
            exibirSugestoes(resultados);
        } else {
            suggestionsBox.style.display = 'none';
            isOpen = false;
        }
    });

    // Mostrar todos os cargos
    function mostrarTodosCargos() {
        exibirSugestoes(todosCargos);
    }

    function exibirSugestoes(resultados) {
        suggestionsBox.innerHTML = '';
        resultados.forEach((cargo, index) => {
            const item = document.createElement('div');
            item.className = 'item';
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', 'false');
            item.setAttribute('data-index', index);
            
            item.innerHTML = `
                <div class="titulo">${cargo.nome}</div>
                <div class="meta">${cargo.categoria}</div>
            `;
            
            item.addEventListener('click', function() {
                selecionarCargo(cargo);
            });
            
            suggestionsBox.appendChild(item);
        });
        
        suggestionsBox.style.display = 'block';
        isOpen = true;
        currentFocus = -1;
    }

    // Navegação com teclado
    input.addEventListener('keydown', function(e) {
        const items = suggestionsBox.getElementsByClassName('item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!isOpen) {
                mostrarTodosCargos();
            }
            currentFocus = Math.min(currentFocus + 1, items.length - 1);
            atualizarSelecao(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus = Math.max(currentFocus - 1, -1);
            atualizarSelecao(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click();
            }
        } else if (e.key === 'Escape') {
            closeAllLists();
        }
    });

    function atualizarSelecao(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('active');
            items[i].setAttribute('aria-selected', 'false');
        }
        
        if (currentFocus >= 0 && items[currentFocus]) {
            items[currentFocus].classList.add('active');
            items[currentFocus].setAttribute('aria-selected', 'true');
            items[currentFocus].scrollIntoView({ block: 'nearest' });
        }
    }

    function selecionarCargo(cargo) {
        input.value = cargo.nome;
        hiddenInput.value = cargo.nome;
        
        closeAllLists();
        
        if (onSelect) {
            onSelect(cargo);
        }
    }

    function closeAllLists() {
        suggestionsBox.style.display = 'none';
        isOpen = false;
        currentFocus = -1;
    }

    // Fechar sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        if (!suggestionsBox.contains(e.target) && e.target !== input) {
            closeAllLists();
        }
    });

    // Validar formulário
    const form = input.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!hiddenInput.value) {
                e.preventDefault();
                alert('Por favor, selecione um cargo válido.');
                input.focus();
            }
        });
    }
}