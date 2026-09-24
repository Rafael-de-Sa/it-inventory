/**
 * Cadastro/edição de equipamento: mostra a ficha técnica da categoria do tipo escolhido.
 * As fichas das outras categorias ficam ocultas e desabilitadas (não são enviadas nem validadas pelo navegador).
 */
import { aplicarMascaras } from '../util/data-mascara';

document.addEventListener('DOMContentLoaded', () => {
    aplicarMascaras();

    const selectTipo = document.getElementById('tipo_equipamento_id');
    if (!selectTipo) return;

    const categorias = JSON.parse(selectTipo.dataset.categorias || '{}');
    const fichas = document.querySelectorAll('[data-ficha]');

    const atualizarFicha = () => {
        const categoria = categorias[selectTipo.value] || null;

        fichas.forEach((ficha) => {
            const ativa = ficha.dataset.ficha === categoria;
            ficha.hidden = !ativa;
            ficha.disabled = !ativa;
        });

        atualizarWifi();
    };

    // MAC do Wi-Fi só faz sentido quando o computador possui Wi-Fi.
    const checkWifi = document.querySelector('[data-possui-wifi]');
    const macWifi = document.querySelector('[data-mac-wifi]');

    const atualizarWifi = () => {
        if (!checkWifi || !macWifi) return;
        const fichaDesabilitada = checkWifi.closest('fieldset')?.disabled;
        macWifi.disabled = fichaDesabilitada || !checkWifi.checked;
        macWifi.required = !macWifi.disabled;
    };

    selectTipo.addEventListener('change', atualizarFicha);
    checkWifi?.addEventListener('change', atualizarWifi);

    // Formata MAC ao sair do campo: "aabbccddeeff" → "AA:BB:CC:DD:EE:FF".
    document.querySelectorAll('input[name$="[mac]"], input[name$="[mac_ethernet]"], input[name$="[mac_wifi]"]').forEach((campo) => {
        campo.addEventListener('blur', () => {
            const hex = campo.value.replace(/[^0-9a-f]/gi, '');
            if (hex.length === 12) {
                campo.value = hex.toUpperCase().match(/.{2}/g).join(':');
            }
        });
    });

    atualizarFicha();
});
