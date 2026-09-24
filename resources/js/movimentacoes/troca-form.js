/**
 * Termo de troca: a condição do equipamento substituído só vale na troca interna.
 * Na troca pelo fornecedor ele recebe baixa, então o campo fica oculto e desabilitado (não é enviado).
 */
document.addEventListener('DOMContentLoaded', () => {
    const tipoTroca = document.getElementById('tipo_troca');
    const motivo = document.querySelector('[data-motivo-troca]');
    if (!tipoTroca || !motivo) return;

    const campoMotivo = motivo.closest('div');

    const atualizar = () => {
        const interna = tipoTroca.value !== 'fornecedor';
        campoMotivo.hidden = !interna;
        motivo.disabled = !interna;
        motivo.required = tipoTroca.value === 'interna';
    };

    tipoTroca.addEventListener('change', atualizar);
    atualizar();
});
