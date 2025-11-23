document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('movimentacaoDevolucaoForm');
    if (!formulario) return;

    const campoEmpresa = document.getElementById('empresa_id');
    const campoSetor = document.getElementById('setor_id');
    const campoFuncionario = document.getElementById('funcionario_id');

    const endpointSetoresTemplate = formulario.dataset.carregarSetoresEndpoint;
    const endpointFuncionariosTemplate = formulario.dataset.carregarFuncionariosEndpoint;
    const endpointEquipamentosEmUsoTemplate = formulario.dataset.carregarEquipamentosEmUsoEndpoint;

    const oldEmpresaId = formulario.dataset.oldEmpresaId || '';
    const oldSetorId = formulario.dataset.oldSetorId || '';
    const oldFuncionarioId = formulario.dataset.oldFuncionarioId || '';
    const oldEquipamentos = JSON.parse(formulario.dataset.oldEquipamentos || '[]');

    const tabelaEquipamentosEmUso = document.querySelector('#tabela_equipamentos_em_uso tbody');
    const containerInputsEquipamentos = document.getElementById('container_inputs_equipamentos');
    const templateLinhaEquipamento = document.getElementById('linha_equipamento_template');

    if (!tabelaEquipamentosEmUso || !containerInputsEquipamentos || !templateLinhaEquipamento) {
        console.error('Elementos necessários para o formulário de devolução não foram encontrados.');
        return;
    }

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

            if (funcionarioSelecionadoId) {
                await carregarEquipamentosEmUso(funcionarioSelecionadoId);
            }
        } catch (erro) {
            console.error('Erro inesperado ao carregar funcionários:', erro);
        }
    }

    async function carregarEquipamentosEmUso(funcionarioId) {
        tabelaEquipamentosEmUso.innerHTML = '';
        containerInputsEquipamentos.innerHTML = '';

        if (!funcionarioId) return;

        const url = endpointEquipamentosEmUsoTemplate.replace('FUNCIONARIO_ID', funcionarioId);

        try {
            const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!resposta.ok) {
                console.error('Erro ao carregar equipamentos em uso:', resposta.status);
                return;
            }

            const equipamentos = await resposta.json();

            if (!Array.isArray(equipamentos) || equipamentos.length === 0) {
                const linhaVazia = document.createElement('tr');
                linhaVazia.innerHTML = `
                    <td colspan="7" class="px-4 py-3 text-center text-sm text-green-100/80">
                        Nenhum equipamento em uso para este funcionário.
                    </td>
                `;
                tabelaEquipamentosEmUso.appendChild(linhaVazia);
                return;
            }

            equipamentos.forEach((equipamento) => {
                const fragmento = templateLinhaEquipamento.content.cloneNode(true);
                const linha = fragmento.querySelector('tr');

                linha.setAttribute('data-equipamento-id', equipamento.id);

                const estaSelecionado = oldEquipamentos.includes(equipamento.id);

                const colunaId = linha.querySelector('.coluna-id');
                const colunaPatrimonio = linha.querySelector('.coluna-patrimonio');
                const colunaNumeroSerie = linha.querySelector('.coluna-numero-serie');
                const colunaDescricao = linha.querySelector('.coluna-descricao');
                const colunaTipo = linha.querySelector('.coluna-tipo');
                const campoObservacao = linha.querySelector('.campo-observacao');
                const campoMotivoDevolucao = linha.querySelector('.campo-motivo-devolucao');
                const checkbox = linha.querySelector('.checkbox-equipamento');

                if (colunaId) colunaId.textContent = equipamento.id;
                if (colunaPatrimonio) colunaPatrimonio.textContent = equipamento.patrimonio ?? '';
                if (colunaNumeroSerie) colunaNumeroSerie.textContent = equipamento.numero_serie ?? '';
                if (colunaDescricao) colunaDescricao.textContent = equipamento.descricao ?? '';
                if (colunaTipo) colunaTipo.textContent = equipamento.tipo ?? '-';

                if (campoObservacao) {
                    campoObservacao.name = `observacoes_equipamentos[${equipamento.id}]`;
                }

                if (campoMotivoDevolucao) {
                    campoMotivoDevolucao.name = `motivos_devolucao_equipamentos[${equipamento.id}]`;
                }

                if (checkbox) {
                    checkbox.checked = estaSelecionado;

                    if (estaSelecionado) {
                        criarInputHiddenEquipamento(equipamento.id);
                    }

                    checkbox.addEventListener('change', (evento) => {
                        const marcado = evento.target.checked;
                        if (marcado) {
                            criarInputHiddenEquipamento(equipamento.id);
                        } else {
                            removerInputHiddenEquipamento(equipamento.id);
                        }
                    });
                }

                tabelaEquipamentosEmUso.appendChild(linha);
            });
        } catch (erro) {
            console.error('Erro inesperado ao carregar equipamentos em uso:', erro);
        }
    }

    function criarInputHiddenEquipamento(idEquipamento) {
        const seletor = `input[type="hidden"][name="equipamentos[]"][value="${idEquipamento}"]`;
        const jaExiste = containerInputsEquipamentos.querySelector(seletor);
        if (jaExiste) return;

        const inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'equipamentos[]';
        inputHidden.value = idEquipamento;

        containerInputsEquipamentos.appendChild(inputHidden);
    }

    function removerInputHiddenEquipamento(idEquipamento) {
        const seletor = `input[type="hidden"][name="equipamentos[]"][value="${idEquipamento}"]`;
        const input = containerInputsEquipamentos.querySelector(seletor);
        if (input) {
            containerInputsEquipamentos.removeChild(input);
        }
    }

    // Eventos principais
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

    if (campoFuncionario) {
        campoFuncionario.addEventListener('change', (evento) => {
            const funcionarioIdSelecionado = evento.target.value || '';
            carregarEquipamentosEmUso(funcionarioIdSelecionado);
        });
    }

    if (oldEmpresaId) {
        carregarSetores(oldEmpresaId, oldSetorId || null);
    }
});
