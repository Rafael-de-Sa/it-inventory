/**
 * Combobox com busca sobre um <select data-combobox> (melhoria progressiva: sem JS, o select continua funcionando).
 *
 * O <select> permanece no formulário (é ele que é enviado e que os outros scripts leem) e fica oculto; por cima
 * dele entra um campo de texto que filtra as opções enquanto digita, ignorando acentos e a ordem das palavras.
 * Teclado: ↑/↓ percorrem, Enter escolhe, Esc fecha. Mudanças feitas no select por outros scripts devem disparar
 * o evento "change" para o campo acompanhar.
 */
const normalizar = (texto) => texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

let contador = 0;

export function criarCombobox(select) {
    if (select.dataset.comboboxPronto) return;
    select.dataset.comboboxPronto = '1';

    const id = select.id || `combobox_${++contador}`;
    const idLista = `${id}_opcoes`;
    const opcoes = [...select.options].filter((opcao) => opcao.value !== '');
    const placeholder = [...select.options].find((opcao) => opcao.value === '')?.textContent ?? 'Digite para buscar…';

    const involucro = document.createElement('div');
    involucro.className = 'relative';

    const campo = document.createElement('input');
    campo.type = 'text';
    campo.id = `${id}_busca`;
    campo.autocomplete = 'off';
    campo.placeholder = placeholder;
    campo.className = `${select.className} pr-9`;
    campo.setAttribute('role', 'combobox');
    campo.setAttribute('aria-autocomplete', 'list');
    campo.setAttribute('aria-expanded', 'false');
    campo.setAttribute('aria-controls', idLista);
    if (select.getAttribute('aria-describedby')) campo.setAttribute('aria-describedby', select.getAttribute('aria-describedby'));
    if (select.getAttribute('aria-invalid')) campo.setAttribute('aria-invalid', select.getAttribute('aria-invalid'));

    const seta = document.createElement('span');
    seta.className = 'pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-ink-subtle';
    seta.innerHTML = '<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>';

    const lista = document.createElement('ul');
    lista.id = idLista;
    lista.hidden = true;
    lista.setAttribute('role', 'listbox');
    lista.className = 'absolute z-20 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-line bg-surface p-1 text-sm shadow-lg';

    // O rótulo passa a apontar para o campo de busca; a validação "obrigatório" também.
    document.querySelector(`label[for="${id}"]`)?.setAttribute('for', campo.id);
    campo.required = select.required;
    select.required = false;
    select.hidden = true;
    select.tabIndex = -1;

    select.after(involucro);
    involucro.append(campo, seta, lista, select);

    let visiveis = [];
    let ativo = -1;

    const rotuloSelecionado = () => select.selectedOptions[0]?.value ? select.selectedOptions[0].textContent.trim() : '';

    const sincronizar = () => {
        campo.value = rotuloSelecionado();
        campo.disabled = select.disabled;
        campo.setCustomValidity('');
    };

    const fechar = () => {
        lista.hidden = true;
        campo.setAttribute('aria-expanded', 'false');
        campo.removeAttribute('aria-activedescendant');
        ativo = -1;
    };

    const marcarAtivo = (indice) => {
        ativo = indice;
        lista.querySelectorAll('[role="option"]').forEach((item, i) => {
            const marcado = i === indice;
            item.classList.toggle('bg-brand-50', marcado);
            item.classList.toggle('text-brand-800', marcado);
            if (marcado) {
                campo.setAttribute('aria-activedescendant', item.id);
                item.scrollIntoView({ block: 'nearest' });
            }
        });
    };

    const escolher = (opcao) => {
        select.value = opcao ? opcao.value : '';
        select.dispatchEvent(new Event('change', { bubbles: true }));
        sincronizar();
        fechar();
    };

    const abrir = (filtro = '') => {
        const termos = normalizar(filtro).split(/\s+/).filter(Boolean);
        visiveis = opcoes.filter((opcao) => {
            const texto = normalizar(opcao.textContent);
            return termos.every((termo) => texto.includes(termo));
        });

        lista.innerHTML = '';
        visiveis.forEach((opcao, i) => {
            const item = document.createElement('li');
            item.id = `${idLista}_${i}`;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(opcao.value === select.value));
            item.className = 'cursor-pointer rounded-md px-3 py-2 text-ink hover:bg-surface-muted';
            if (opcao.value === select.value) item.classList.add('font-medium');
            item.textContent = opcao.textContent.trim();
            // mousedown (e não click) para escolher antes de o campo perder o foco.
            item.addEventListener('mousedown', (evento) => {
                evento.preventDefault();
                escolher(opcao);
            });
            lista.append(item);
        });

        if (visiveis.length === 0) {
            const vazio = document.createElement('li');
            vazio.className = 'px-3 py-2 text-ink-muted';
            vazio.textContent = 'Nenhum resultado.';
            lista.append(vazio);
        }

        lista.hidden = false;
        campo.setAttribute('aria-expanded', 'true');
        marcarAtivo(visiveis.length ? 0 : -1);
    };

    campo.addEventListener('focus', () => {
        campo.select();
        abrir();
    });
    campo.addEventListener('click', () => { if (lista.hidden) abrir(); });
    campo.addEventListener('input', () => {
        abrir(campo.value);
        // Texto digitado sem escolher da lista não vale como seleção.
        campo.setCustomValidity(campo.value && campo.value !== rotuloSelecionado() ? 'Escolha uma opção da lista.' : '');
    });

    campo.addEventListener('keydown', (evento) => {
        if (evento.key === 'ArrowDown' || evento.key === 'ArrowUp') {
            evento.preventDefault();
            if (lista.hidden) return abrir(campo.value);
            const passo = evento.key === 'ArrowDown' ? 1 : -1;
            marcarAtivo(Math.max(0, Math.min(visiveis.length - 1, ativo + passo)));
        } else if (evento.key === 'Enter' && !lista.hidden) {
            evento.preventDefault();
            if (visiveis[ativo]) escolher(visiveis[ativo]);
        } else if (evento.key === 'Escape' && !lista.hidden) {
            evento.preventDefault();
            sincronizar();
            fechar();
        }
    });

    campo.addEventListener('blur', () => {
        // Campo apagado limpa a escolha; texto incompleto volta para a opção escolhida.
        if (campo.value.trim() === '' && select.value !== '') escolher(null);
        else if (campo.value !== rotuloSelecionado()) sincronizar();
        fechar();
    });

    select.addEventListener('change', sincronizar);
    sincronizar();
}

export function aplicarComboboxes(raiz = document) {
    raiz.querySelectorAll('select[data-combobox]').forEach(criarCombobox);
}
