import { initCepAutofill } from './cep-autofill';
import { initMasks, normalize } from '../util/masks';

document.addEventListener('DOMContentLoaded', () => {
    initMasks();
    initCepAutofill();

    const form = document.getElementById('empresaForm')
        || document.getElementById('empresaEditForm');
    if (!form) return;

    form.addEventListener('submit', () => {
        const cep = document.getElementById('cep');
        const cnpj = document.getElementById('cnpj');
        const uf = document.getElementById('estado');

        const tel = document.getElementById('telefone') || document.getElementById('telefones');

        if (cep) cep.value = normalize.digits(cep.value, 8);
        if (cnpj) cnpj.value = normalize.digits(cnpj.value, 14);
        if (tel) tel.value = normalize.digits(tel.value, 11);
        if (uf) uf.value = normalize.upper2(uf.value);
    });
});
