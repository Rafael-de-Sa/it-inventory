const onlyDigits = (s) => (s || '').replace(/\D+/g, '');
const isEightDigits = (s) => /^\d{8}$/.test(s);

// cache no cliente p/ evitar requisições repetidas no mesmo CEP
const cache = new Map();

function setDisabled(inputs, disabled) {
    inputs.forEach((el) => el && (el.disabled = disabled));
}

function setValues({ rua, bairro, cidade, estado }, els) {
    if (els.rua) els.rua.value = rua || '';
    if (els.bairro) els.bairro.value = bairro || '';
    if (els.cidade) els.cidade.value = cidade || '';
    if (els.estado) els.estado.value = (estado || '').toUpperCase();
}

function setHelpMessage(el, text, isError = false) {
    if (!el) return;
    el.textContent = text || '';
    el.classList.toggle('text-red-300', isError);
    el.classList.toggle('text-green-200', !isError);
}

async function fetchEndereco(endpointTemplate, cep8) {
    // usa cache de aba
    if (cache.has(cep8)) return cache.get(cep8);

    const endpoint = endpointTemplate.replace(/(\d{8})$/, cep8);
    const resp = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });

    if (resp.ok) {
        const data = await resp.json();
        cache.set(cep8, data);
        return data;
    }

    // trata erros conhecidos
    if (resp.status === 404) {
        throw new Error('CEP não encontrado.');
    }
    if (resp.status === 422) {
        const data = await resp.json().catch(() => ({}));
        const msg = data?.message || 'CEP inválido.';
        throw new Error(msg);
    }

    // outros erros (timeout reverso / 5xx mapeado no controller como 502)
    throw new Error('Falha ao consultar serviço de CEP.');
}

export function initCepAutofill() {
    const form = document.getElementById('empresaForm');
    if (!form) return;

    const endpointTemplate = form.dataset.cepEndpoint || ''; // ex.: /empresas/cep/00000000

    const elCep = document.getElementById('cep');
    const elRua = document.getElementById('rua');
    const elBairro = document.getElementById('bairro');
    const elCidade = document.getElementById('cidade');
    const elEstado = document.getElementById('estado');
    const elHelp = document.getElementById('cep_help');

    if (!elCep || !endpointTemplate) return;

    // grupo de campos que serão bloqueados durante a busca
    const addressEls = [elRua, elBairro, elCidade, elEstado];

    // função principal de busca/preenchimento
    const run = async () => {
        const cep8 = onlyDigits(elCep.value).slice(0, 8);
        if (!isEightDigits(cep8)) {
            setHelpMessage(elHelp, 'Formato: 00000-000', false);
            return;
        }

        // estado de carregamento
        setHelpMessage(elHelp, 'Buscando endereço…', false);
        setDisabled(addressEls, true);

        try {
            const data = await fetchEndereco(endpointTemplate, cep8);
            setValues(data, { rua: elRua, bairro: elBairro, cidade: elCidade, estado: elEstado });
            setHelpMessage(elHelp, 'Endereço preenchido automaticamente. Confira os dados.', false);
        } catch (e) {
            setHelpMessage(elHelp, e.message || 'Não foi possível buscar o CEP.', true);
            // mantém campos habilitados para edição manual
        } finally {
            setDisabled(addressEls, false);
        }
    };

    // Dispara no blur (principal) e quando atingir 8 dígitos (mesmo sem máscara)
    elCep.addEventListener('blur', run);

    // Debounce simples para input
    let t = null;
    elCep.addEventListener('input', () => {
        clearTimeout(t);
        const digits = onlyDigits(elCep.value);
        if (digits.length === 8) {
            t = setTimeout(run, 200);
        } else {
            // feedback enquanto digita
            setHelpMessage(elHelp, 'Formato: 00000-000', false);
        }
    });
}