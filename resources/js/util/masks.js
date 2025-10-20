
const onlyDigits = (s) => (s || '').replace(/\D+/g, '');

// CEP: 00000-000
const maskCEP = (v) => {
    v = onlyDigits(v).slice(0, 8);
    return v.replace(/^(\d{5})(\d)/, '$1-$2');
};

// CNPJ: 00.000.000/0000-00
const maskCNPJ = (v) => {
    v = onlyDigits(v).slice(0, 14);
    v = v.replace(/^(\d{2})(\d)/, '$1.$2');
    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    v = v.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3/$4');
    v = v.replace(/(\/\d{4})(\d)/, '$1-$2');
    return v;
};

const maskPhone = (v) => {
    let d = onlyDigits(v).slice(0, 11);
    if (d.length === 0) return '';

    if (d.length <= 10) {
        d = d.replace(/^(\d{1,2})/, '($1');
        d = d.replace(/^\((\d{2})(\d)/, '($1) $2');
        d = d.replace(/^(\(\d{2}\)\s\d{4})(\d)/, '$1-$2');
    } else {
        d = d.replace(/^(\d{1,2})/, '($1');
        d = d.replace(/^\((\d{2})(\d)/, '($1) $2');
        d = d.replace(/^(\(\d{2}\)\s\d{1}\d{4})(\d)/, '$1-$2');
    }
    return d;
};

// CPF: 000.000.000-00
const maskCPF = (v) => {
    let d = onlyDigits(v).slice(0, 11);
    d = d.replace(/^(\d{3})(\d)/, '$1.$2');
    d = d.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    d = d.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
    return d;
};

const bindMask = (el, masker) => {
    if (!el) return;
    el.addEventListener('input', () => {
        el.value = masker(el.value);
    });
};

export function initMasks() {
    const elCEP = document.getElementById('cep');
    const elCNPJ = document.getElementById('cnpj');
    const elCPF = document.getElementById('cpf');
    const elTEL = document.getElementById('telefone') || document.getElementById('telefones');

    bindMask(elCEP, maskCEP);
    bindMask(elCNPJ, maskCNPJ);
    bindMask(elCPF, maskCPF);
    bindMask(elTEL, maskPhone);
}

export function reapplyMasks(ids = []) {
    const get = (id) => document.getElementById(id);
    const registry = {
        cep: { el: get('cep'), fn: maskCEP },
        cnpj: { el: get('cnpj'), fn: maskCNPJ },
        cpf: { el: get('cpf'), fn: maskCPF },
        telefone: { el: get('telefone'), fn: maskPhone },
        telefones: { el: get('telefones'), fn: maskPhone },
    };

    const keys = ids.length ? ids : Object.keys(registry);
    keys.forEach((k) => {
        const item = registry[k];
        if (item?.el && typeof item.fn === 'function') {
            const val = (item.el.value || '').trim();
            if (val.length === 0) return;          // <-- não mascara vazio
            item.el.value = item.fn(val);
        }
    });
}

// Helpers para normalizar antes do submit:
export const normalize = {
    digits: (v, max = null) => {
        const d = (v || '').replace(/\D+/g, '');
        return max ? d.slice(0, max) : d;
    },
    upper2: (v) => (v || '').toUpperCase().slice(0, 2),
};
