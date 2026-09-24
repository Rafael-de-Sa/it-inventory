/**
 * Formulário de ocorrência: máscara do valor cobrado e sugestão do último usuário
 * (quem está com o equipamento escolhido, se o campo ainda estiver vazio).
 */
import { aplicarMascaras } from '../util/data-mascara';

document.addEventListener('DOMContentLoaded', () => {
    aplicarMascaras();

    const equipamento = document.getElementById('equipamento_id');
    const funcionario = document.getElementById('funcionario_id');
    if (!equipamento || !funcionario) return;

    const responsaveis = JSON.parse(equipamento.dataset.responsaveis || '{}');

    equipamento.addEventListener('change', () => {
        const responsavel = responsaveis[equipamento.value];
        if (responsavel && !funcionario.value) funcionario.value = String(responsavel);
    });
});
