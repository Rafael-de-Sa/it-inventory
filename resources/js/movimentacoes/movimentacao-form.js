document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('movimentacaoForm');
    if (!formulario) return;

    const campoEmpresa = document.getElementById('empresa_id');
    const campoSetor = document.getElementById('setor_id');
    const campoFuncionario = document.getElementById('funcionario_id');

    const endpointSetoresTemplate = formulario.dataset.carregarSetoresEndpoint;
    const endpointFuncionariosTemplate = formulario.dataset.carregarFuncionariosEndpoint;

    const oldEmpresaId = formulario.dataset.oldEmpresaId || '';
    const oldSetorId = formulario.dataset.oldSetorId || '';
    const oldFuncionarioId = formulario.dataset.oldFuncionarioId || '';

    const tabelaEquipamentosDisponiveis = document.querySelector('#tabela_equipamentos_disponiveis tbody');
    const tabelaEquipamentosSelecionados = document.querySelector('#tabela_equipamentos_selecionados tbody');
    const containerInputsEquipamentos = document.getElementById('container_inputs_equipamentos');

    const botaoAdicionarEquipamentos = document.getElementById('botaoAdicionarEquipamentos');
    const botaoRemoverEquipamentos = document.getElementById('botaoRemoverEquipamentos');

    const campoBuscaEquipamento = document.getElementById('busca_equipamento');
    const campoFiltroTipo = document.getElementById('filtro_tipo');
    const botaoPesquisarEquipamentos = document.getElementById('botaoPesquisarEquipamentos');

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

        resetarSelect(campoFuncionario, 'Selecione um setor primeiro…');
        campoFuncionario.disabled = true;

        if (!empresaId) return;

        const url = endpointSetoresTemplate.replace('EMPRESA_ID', empresaId);

        try {
            const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!resposta.ok) {
                console.error('Erro ao carregar setores:', resposta.status);
                return;
            }

            const setores = await resposta.json();

            resetarSelect(campoSetor, 'Selecione…');

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
            console.error('Erro inesperado ao carregar setores:', erro);
        }
    }

    async function carregarFuncionarios(setorId, funcionarioSelecionadoId = null) {
        resetarSelect(campoFuncionario, 'Selecione…');
        campoFuncionario.disabled = true;

        if (!setorId) return;

        const url = endpointFuncionariosTemplate.replace('SETOR_ID', setorId);

        try {
            const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!resposta.ok) {
                console.error('Erro ao carregar funcionários:', resposta.status);
                return;
            }

            const funcionarios = await resposta.json();

            resetarSelect(campoFuncionario, 'Selecione…');

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
            console.error('Erro inesperado ao carregar funcionários:', erro);
        }
    }

    function atualizarInputsEquipamentos() {
        containerInputsEquipamentos.innerHTML = '';

        const linhasSelecionadas = tabelaEquipamentosSelecionados.querySelectorAll('tr[data-equipamento-id]');

        linhasSelecionadas.forEach((linha) => {
            const idEquipamento = linha.getAttribute('data-equipamento-id');
            if (!idEquipamento) return;

            const inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = 'equipamentos[]';
            inputHidden.value = idEquipamento;

            containerInputsEquipamentos.appendChild(inputHidden);
        });
    }

    function moverLinhas(origemTbody, destinoTbody) {
        const checkboxesMarcados = origemTbody.querySelectorAll('input[type="checkbox"]:checked');

        checkboxesMarcados.forEach((checkbox) => {
            const linha = checkbox.closest('tr');
            if (!linha) return;

            checkbox.checked = false;
            destinoTbody.appendChild(linha);
        });

        atualizarInputsEquipamentos();
    }

    function preencherFiltroTipo() {
        const tipos = new Set();

        tabelaEquipamentosDisponiveis.querySelectorAll('tr[data-equipamento-tipo]').forEach((linha) => {
            const tipo = (linha.getAttribute('data-equipamento-tipo') || '').trim();
            if (tipo) tipos.add(tipo);
        });

        tipos.forEach((tipo) => {
            const opcao = document.createElement('option');
            opcao.value = tipo;
            opcao.textContent = tipo;
            campoFiltroTipo.appendChild(opcao);
        });
    }

    function aplicarFiltroEquipamentos() {
        const termoBusca = (campoBuscaEquipamento.value || '').trim().toLowerCase();
        const tipoFiltrado = (campoFiltroTipo.value || '').trim().toLowerCase();

        tabelaEquipamentosDisponiveis.querySelectorAll('tr[data-equipamento-id]').forEach((linha) => {
            const patrimonio = (linha.getAttribute('data-equipamento-patrimonio') || '').toLowerCase();
            const descricao = (linha.getAttribute('data-equipamento-descricao') || '').toLowerCase();
            const serie = (linha.getAttribute('data-equipamento-serie') || '').toLowerCase();
            const tipo = (linha.getAttribute('data-equipamento-tipo') || '').toLowerCase();

            let deveExibir = true;

            if (termoBusca) {
                const correspondeAoTermo =
                    patrimonio.includes(termoBusca) || descricao.includes(termoBusca) || serie.includes(termoBusca);

                if (!correspondeAoTermo) deveExibir = false;
            }

            if (deveExibir && tipoFiltrado) {
                if (tipo !== tipoFiltrado) deveExibir = false;
            }

            linha.style.display = deveExibir ? '' : 'none';
        });
    }

    if (campoEmpresa) {
        campoEmpresa.addEventListener('change', (evento) => {
            const empresaIdSelecionada = evento.target.value || '';
            carregarSetores(empresaIdSelecionada);
        });
    }

    if (campoSetor) {
        campoSetor.addEventListener('change', (evento) => {
            const setorIdSelecionado = evento.target.value || '';
            carregarFuncionarios(setorIdSelecionado);
        });
    }

    if (botaoAdicionarEquipamentos) {
        botaoAdicionarEquipamentos.addEventListener('click', () => {
            moverLinhas(tabelaEquipamentosDisponiveis, tabelaEquipamentosSelecionados);
        });
    }

    if (botaoRemoverEquipamentos) {
        botaoRemoverEquipamentos.addEventListener('click', () => {
            moverLinhas(tabelaEquipamentosSelecionados, tabelaEquipamentosDisponiveis);
        });
    }

    if (botaoPesquisarEquipamentos) {
        botaoPesquisarEquipamentos.addEventListener('click', () => {
            aplicarFiltroEquipamentos();
        });
    }

    if (campoBuscaEquipamento) {
        campoBuscaEquipamento.addEventListener('keyup', () => {
            aplicarFiltroEquipamentos();
        });
    }

    if (campoFiltroTipo) {
        campoFiltroTipo.addEventListener('change', () => {
            aplicarFiltroEquipamentos();
        });
    }

    preencherFiltroTipo();

    if (oldEmpresaId) {
        carregarSetores(oldEmpresaId, oldSetorId || null);
    }

    const oldEquipamentos = JSON.parse(formulario.dataset.oldEquipamentos || '[]');

    if (Array.isArray(oldEquipamentos) && oldEquipamentos.length > 0) {
        oldEquipamentos.forEach((idEquipamento) => {
            const linha = tabelaEquipamentosDisponiveis.querySelector(`tr[data-equipamento-id="${idEquipamento}"]`);
            if (linha) {
                tabelaEquipamentosSelecionados.appendChild(linha);
            }
        });

        atualizarInputsEquipamentos();
    }
});
