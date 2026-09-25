/**
 * Campos com data-mascara="moeda|digitos|chave-nfe": aceitam só números e formatam enquanto digita.
 */
import { maskChaveNfe, maskDigitos, maskMoeda } from './masks';

const MASCARAS = { moeda: maskMoeda, digitos: maskDigitos, 'chave-nfe': maskChaveNfe };

export function aplicarMascaras(raiz = document) {
    raiz.querySelectorAll('[data-mascara]').forEach((campo) => {
        const mascara = MASCARAS[campo.dataset.mascara];
        if (!mascara) return;
        campo.addEventListener('input', () => { campo.value = mascara(campo.value); });
        if (campo.value) campo.value = mascara(campo.value);
    });
}
