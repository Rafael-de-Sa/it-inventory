// resources/js/funcionarios/funcionario-form.js
import { initMasks, normalize, reapplyMasks } from '../util/masks';

document.addEventListener('DOMContentLoaded', () => {
    initMasks();
    // Aplica a máscara nos valores que já chegaram preenchidos (old())
    reapplyMasks(['cpf', 'telefone']);

    const form = document.getElementById('funcionarioForm');
    const empresaSelect = document.getElementById('empresa_id');
    const setorSelect = document.getElementById('setor_id');
    const cpfInput = document.getElementById('cpf');
    const telInput = document.getElementById('telefone');
    const matriculaInput = document.getElementById('matricula');
    const terceirizadoCheck = document.querySelector('input[name="terceirizado"]');

    // Normalização final antes de enviar
    form?.addEventListener('submit', () => {
        if (cpfInput) cpfInput.value = normalize.digits(cpfInput.value, 11);
        if (telInput) telInput.value = normalize.digits(telInput.value, 11);
        if (matriculaInput && !matriculaInput.disabled) {
            matriculaInput.value = normalize.digits(matriculaInput.value, 30);
        }
    });

    // Carrega setores por empresa (e repopula em caso de old())
    async function carregarSetores(empresaId, setorSelecionado = null) {
        setorSelect.innerHTML = '<option value="">Carregando setores...</option>';
        if (!empresaId) {
            setorSelect.innerHTML = '<option value="">Selecione uma empresa primeiro...</option>';
            return;
        }
        try {
            const resp = await fetch(`/empresas/${empresaId}/setores`);
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const setores = await resp.json();

            if (!Array.isArray(setores) || setores.length === 0) {
                setorSelect.innerHTML = '<option value="">Nenhum setor encontrado</option>';
                return;
            }

            setorSelect.innerHTML = '<option value="">Selecione...</option>';
            setores.forEach((s) => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.nome;
                if (setorSelecionado && String(setorSelecionado) === String(s.id)) opt.selected = true;
                setorSelect.appendChild(opt);
            });
        } catch (e) {
            console.error('Erro ao carregar setores:', e);
            setorSelect.innerHTML = '<option value="">Erro ao carregar setores</option>';
        }
    }

    empresaSelect?.addEventListener('change', () => carregarSetores(empresaSelect.value, null));

    const empresaOld = empresaSelect?.value || empresaSelect?.getAttribute('data-old');
    const setorOld = setorSelect?.getAttribute('data-old');
    if (empresaOld) carregarSetores(empresaOld, setorOld || null);

    // Matrícula apenas números (e respeita a validação do back)
    matriculaInput?.addEventListener('input', () => {
        matriculaInput.value = normalize.digits(matriculaInput.value, 30);
    });

    // Terceirizado: limpa e bloqueia matrícula
    const toggleMatricula = () => {
        if (!matriculaInput) return;
        if (terceirizadoCheck?.checked) {
            matriculaInput.value = '';
            matriculaInput.disabled = true; // usa classes disabled: do Tailwind
        } else {
            matriculaInput.disabled = false;
        }
    };
    terceirizadoCheck?.addEventListener('change', toggleMatricula);
    toggleMatricula(); // estado inicial (cobre old('terceirizado'))
});
