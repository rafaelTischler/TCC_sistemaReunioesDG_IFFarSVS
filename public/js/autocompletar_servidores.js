
function inicializarAutocompleteServidores(config) {
    const {
        servidores,
        inputId,
        hiddenId,
        suggestionsBoxId,
        autoSubmit = false
    } = config;

    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const box = document.getElementById(suggestionsBoxId);
    let items = [];
    let focusedIndex = -1;

    console.log('Autocomplete inicializado com', servidores.length, 'servidores');

    function render(list) {
        box.innerHTML = '';
        if (!list.length) {
            box.style.display = 'none';
            items = [];
            focusedIndex = -1;
            return;
        }
        const fragment = document.createDocumentFragment();
        list.forEach((s, idx) => {
            const el = document.createElement('div');
            el.className = 'item';
            el.setAttribute('role', 'option');
            el.setAttribute('data-id', s.id);
            el.setAttribute('data-name', s.nome);
            el.setAttribute('data-matricula', s.matricula_siape || s.siape || '');
            el.innerHTML = '<div class="titulo"><strong>' + escapeHtml(s.nome) + '</strong></div>' +
                         ((s.matricula_siape || s.siape) ? '<div class="meta">SIAPE: ' + escapeHtml(s.matricula_siape || s.siape) + '</div>' : '');
            el.addEventListener('mousedown', function (e) {
                e.preventDefault();
                selectItem(s);
            });
            fragment.appendChild(el);
        });
        box.appendChild(fragment);
        box.style.display = 'block';
        items = Array.from(box.querySelectorAll('.item'));
        focusedIndex = -1;
    }

    function filter(term) {
        term = (term || '').trim().toLowerCase();
        if (term.length === 0) {
            // quando vazio, não mostrar nenhum servidor
            render([]);
            return;
        }
        const filtered = servidores.filter(s => {
            return (s.nome && s.nome.toLowerCase().includes(term)) ||
                 ((s.matricula_siape || s.siape) && String(s.matricula_siape || s.siape).includes(term));
        }).slice(0, 20);
        render(filtered);
    }

    function selectItem(s) {
        input.value = s.nome + ((s.matricula_siape || s.siape) ? ' (' + (s.matricula_siape || s.siape) + ')' : '');
        if (hidden) {
            hidden.value = s.id;
        }
        closeBox();
        
        // Submeter o formulário automaticamente após selecionar
        if (autoSubmit && input.form) {
            setTimeout(() => {
                input.form.submit();
            }, 100);
        }
    }

    function closeBox() {
        box.style.display = 'none';
        items = [];
        focusedIndex = -1;
        Array.from(box.querySelectorAll('.item')).forEach(i => i.setAttribute('aria-selected', 'false'));
    }

    function escapeHtml(str){
        return String(str || '').replace(/[&<>"']/g, function(m){ 
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; 
        });
    }

    input.addEventListener('input', function(){
        if (hidden) hidden.value = '';
        filter(this.value);
    });

    input.addEventListener('focus', function(){
        // só abre se tiver texto
        if (this.value.trim().length) filter(this.value);
    });

    input.addEventListener('blur', function(){
        setTimeout(closeBox, 150);
    });

    input.addEventListener('keydown', function(e){
        if (!items.length && e.key !== 'Escape') return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusedIndex = Math.min(focusedIndex + 1, items.length - 1);
            updateFocus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusedIndex = Math.max(focusedIndex - 1, 0);
            updateFocus();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (focusedIndex >= 0 && items[focusedIndex]) {
                const el = items[focusedIndex];
                const id = el.getAttribute('data-id');
                const nome = el.getAttribute('data-name');
                const mat = el.getAttribute('data-matricula');
                selectItem({ id: id, nome: nome, matricula_siape: mat });
            } else if (autoSubmit && input.form) {
                // Se pressionar Enter sem selecionar, submeter normalmente
                input.form.submit();
            }
        } else if (e.key === 'Escape') {
            closeBox();
        }
    });

    function updateFocus(){
        items.forEach(i => {
            i.classList.remove('active');
            i.setAttribute('aria-selected', 'false');
        });
        if (focusedIndex >= 0 && items[focusedIndex]) {
            const el = items[focusedIndex];
            el.classList.add('active');
            el.setAttribute('aria-selected', 'true');
            el.scrollIntoView({block:'nearest'});
        }
    }

    // inicializar começar vazio com nenhuma sugestão
    render([]);
}