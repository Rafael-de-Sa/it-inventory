document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.querySelector('form[data-endpoints]');
    if (!formulario) return;

    const campoEmpresa = document.getElementById('empresa_id');
    const campoSetor = document.getElementById('setor_id');
    const campoFuncionario = document.getElementById('funcionario_id');

    const endpointSetoresTemplate = formulario.dataset.carregarSetoresEndpoint;
    const endpointFuncionariosTemplate = formulario.dataset.carregarFuncionariosEndpoint;

    const oldEmpresaId = formulario.dataset.oldEmpresaId || '';
    const oldSetorId = formulario.dataset.oldSetorId || '';
    const oldFuncionarioId = formulario.dataset.oldFuncionarioId || '';

    function resetarSelect(selectElement, textoPlaceholder) {
        selectElement.innerHTML = '';
        const opcao = document.createElement('option');
        opcao.value = '';
        opcao.textContent = textoPlaceholder;
        selectElement.appendChild(opcao);
    }

    async function carregarSetores(empresaId, setorSelecionadoId = null) {
        resetarSelect(campoSetor, 'Selecione…');
        campoSetor.disabled = true;
        resetarSelect(campoFuncionario, 'Selecione um setor…');
        campoFuncionario.disabled = true;

        if (!empresaId) return;

        const url = endpointSetoresTemplate.replace('EMPRESA_ID', empresaId);

        try {
            const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!resposta.ok) return;

            const setores = await resposta.json();
            resetarSelect(campoSetor, 'Todos');

            setores.forEach((setor) => {
                const opcao = document.createElement('option');
                opcao.value = setor.id;
                opcao.textContent = setor.nome;
                if (String(setor.id) === String(setorSelecionadoId)) {
                    opcao.selected = true;
                }
                campoSetor.appendChild(opcao);
            });

            campoSetor.disabled = false;

            if (setorSelecionadoId) {
                await carregarFuncionarios(setorSelecionadoId, oldFuncionarioId);
            }
        } catch (erro) {
            console.error('Erro ao carregar setores:', erro);
        }
    }

    async function carregarFuncionarios(setorId, funcionarioSelecionadoId = null) {
        resetarSelect(campoFuncionario, 'Selecione…');
        campoFuncionario.disabled = true;

        if (!setorId) return;

        const url = endpointFuncionariosTemplate.replace('SETOR_ID', setorId);

        try {
            const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!resposta.ok) return;

            const funcionarios = await resposta.json();
            resetarSelect(campoFuncionario, 'Todos');

            funcionarios.forEach((funcionario) => {
                const opcao = document.createElement('option');
                opcao.value = funcionario.id;
                opcao.textContent = funcionario.rotulo;
                if (String(funcionario.id) === String(funcionarioSelecionadoId)) {
                    opcao.selected = true;
                }
                campoFuncionario.appendChild(opcao);
            });

            campoFuncionario.disabled = false;
        } catch (erro) {
            console.error('Erro ao carregar funcionários:', erro);
        }
    }

    // eventos
    if (campoEmpresa) {
        campoEmpresa.addEventListener('change', (e) => {
            carregarSetores(e.target.value);
        });
    }

    if (campoSetor) {
        campoSetor.addEventListener('change', (e) => {
            carregarFuncionarios(e.target.value);
        });
    }

    // inicialização ao carregar
    if (oldEmpresaId) {
        carregarSetores(oldEmpresaId, oldSetorId || null);
    }
});
