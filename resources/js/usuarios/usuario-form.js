document.addEventListener('DOMContentLoaded', () => {
    const empresaSelect = document.getElementById('empresa_id');
    const setorSelect = document.getElementById('setor_id');
    const funcionarioSelect = document.getElementById('funcionario_id');

    // se a view ainda não tem setor, não faz nada
    if (!empresaSelect || !setorSelect || !funcionarioSelect) return;

    /**
     * Carrega setores da empresa selecionada
     */
    async function carregarSetores(empresaId, selectedSetorId = null) {
        setorSelect.innerHTML = '<option value="">Carregando setores...</option>';
        funcionarioSelect.innerHTML = '<option value="">Selecione um setor primeiro...</option>';

        try {
            const response = await fetch(`/empresas/${empresaId}/setores-ativos`);
            if (!response.ok) {
                throw new Error('Resposta não OK ao carregar setores');
            }

            const setores = await response.json();

            setorSelect.innerHTML = '<option value="">Selecione...</option>';

            setores.forEach((setor) => {
                const option = document.createElement('option');
                option.value = setor.id;
                option.textContent = setor.nome;
                if (selectedSetorId && Number(selectedSetorId) === Number(setor.id)) {
                    option.selected = true;
                }
                setorSelect.appendChild(option);
            });

            // se veio old de setor, já carregamos funcionários dele
            if (selectedSetorId) {
                carregarFuncionariosPorSetor(selectedSetorId, funcionarioSelect.dataset.old || null);
            }

        } catch (error) {
            console.error(error);
            setorSelect.innerHTML = '<option value="">Erro ao carregar setores</option>';
        }
    }

    /**
     * Carrega funcionários do setor selecionado
     */
    async function carregarFuncionariosPorSetor(setorId, selectedFuncionarioId = null) {
        funcionarioSelect.innerHTML = '<option value="">Carregando funcionários...</option>';

        try {
            const response = await fetch(`/setores/${setorId}/funcionarios-disponiveis`);
            if (!response.ok) {
                throw new Error('Resposta não OK ao carregar funcionários');
            }

            const funcionarios = await response.json();

            funcionarioSelect.innerHTML = '<option value="">Selecione...</option>';

            funcionarios.forEach((f) => {
                const option = document.createElement('option');
                option.value = f.id;
                option.textContent = f.rotulo;
                if (selectedFuncionarioId && Number(selectedFuncionarioId) === Number(f.id)) {
                    option.selected = true;
                }
                funcionarioSelect.appendChild(option);
            });

        } catch (error) {
            console.error(error);
            funcionarioSelect.innerHTML = '<option value="">Erro ao carregar funcionários</option>';
        }
    }

    // quando mudar a empresa → carregar setores
    empresaSelect.addEventListener('change', (e) => {
        const empresaId = e.target.value;
        if (empresaId) {
            carregarSetores(empresaId);
        } else {
            setorSelect.innerHTML = '<option value="">Selecione uma empresa primeiro...</option>';
            funcionarioSelect.innerHTML = '<option value="">Selecione um setor primeiro...</option>';
        }
    });

    // quando mudar o setor → carregar funcionários
    setorSelect.addEventListener('change', (e) => {
        const setorId = e.target.value;
        if (setorId) {
            carregarFuncionariosPorSetor(setorId);
        } else {
            funcionarioSelect.innerHTML = '<option value="">Selecione um setor primeiro...</option>';
        }
    });

    // reaplicar old() depois de erro de validação
    const oldEmpresa = empresaSelect.dataset.old;
    const oldSetor = setorSelect.dataset.old;
    const oldFuncionario = funcionarioSelect.dataset.old;

    if (oldEmpresa) {
        // carrega setores e, dentro dele, funcionários
        carregarSetores(oldEmpresa, oldSetor);
        // o funcionário é carregado dentro do carregamento de setores
    }
});
