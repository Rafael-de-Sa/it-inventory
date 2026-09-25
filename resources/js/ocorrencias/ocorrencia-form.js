/**
 * Formulário de ocorrência: máscaras de valor e, ao escolher o equipamento, quem está com ele
 * (preenche o último usuário e mostra a opção de recolher o equipamento, que gera o termo de devolução).
 */
import { aplicarMascaras } from '../util/data-mascara';

document.addEventListener('DOMContentLoaded', () => {
    aplicarMascaras();

    const equipamento = document.getElementById('equipamento_id');
    const funcionario = document.getElementById('funcionario_id');
    const caixa = document.querySelector('[data-responsavel]');
    if (!equipamento || !funcionario) return;

    const responsaveis = JSON.parse(equipamento.dataset.responsaveis || '{}');
    const recolher = document.getElementById('recolher');
    let preenchidoAutomaticamente = false;
    let atualizandoFuncionario = false;

    const atualizar = () => {
        const responsavel = responsaveis[equipamento.value];

        // Troca o último usuário sugerido, sem sobrescrever uma escolha feita à mão.
        if (!funcionario.value || preenchidoAutomaticamente) {
            funcionario.value = responsavel ? String(responsavel.funcionario_id) : '';
            atualizandoFuncionario = true;
            funcionario.dispatchEvent(new Event('change', { bubbles: true })); // atualiza o combobox
            atualizandoFuncionario = false;
            preenchidoAutomaticamente = Boolean(responsavel);
        }

        if (!caixa) return;
        caixa.hidden = !responsavel;
        if (recolher) recolher.disabled = !responsavel;
        if (responsavel) {
            caixa.querySelector('[data-responsavel-nome]').textContent = responsavel.nome;
            caixa.querySelector('[data-responsavel-termo]').textContent = responsavel.termo;
        }
    };

    funcionario.addEventListener('change', () => {
        if (!atualizandoFuncionario) preenchidoAutomaticamente = false;
    });
    equipamento.addEventListener('change', atualizar);
    atualizar();
});
