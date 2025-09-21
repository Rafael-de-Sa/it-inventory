
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

// Telefone: (AA) 9XXXX-XXXX (11) ou (AA) XXXX-XXXX (10)
const maskPhone = (v) => {
    let d = onlyDigits(v).slice(0, 11);
    if (d.length <= 10) {
        d = d.replace(/^(\d{0,2})/, '($1');
        d = d.replace(/^\((\d{2})(\d)/, '($1) $2');
        d = d.replace(/^(\(\d{2}\)\s\d{4})(\d)/, '$1-$2');
    } else {
        d = d.replace(/^(\d{0,2})/, '($1');
        d = d.replace(/^\((\d{2})(\d)/, '($1) $2');
        d = d.replace(/^(\(\d{2}\)\s\d{1}\d{4})(\d)/, '$1-$2');
    }
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

    // Telefone: tente 'telefone' (novo) e, se não achar, 'telefones' (legado)
    const elTEL = document.getElementById('telefone') || document.getElementById('telefones');

    bindMask(elCEP, maskCEP);
    bindMask(elCNPJ, maskCNPJ);
    bindMask(elTEL, maskPhone);
}

// Helpers para normalizar antes do submit:
export const normalize = {
    digits: (v, max = null) => {
        const d = (v || '').replace(/\D+/g, '');
        return max ? d.slice(0, max) : d;
    },
    upper2: (v) => (v || '').toUpperCase().slice(0, 2),
};
