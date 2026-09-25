/*
 * Tema claro/escuro. A escolha (claro, escuro ou sistema) fica no localStorage em "tema";
 * "sistema" (padrão) segue a preferência do sistema operacional e acompanha as mudanças dela.
 * A classe .dark é aplicada antes da pintura pelo script inline do <head> (layouts/main_layout);
 * aqui ficam o botão [data-tema-alternar] (com um [data-tema-icone] por opção) e a reação à troca de preferência do sistema.
 */
const CHAVE = 'tema';
const ORDEM = ['claro', 'escuro', 'sistema'];
const ROTULOS = {
    claro: 'Tema claro',
    escuro: 'Tema escuro',
    sistema: 'Tema do sistema',
};
const preferenciaEscura = window.matchMedia('(prefers-color-scheme: dark)');

function temaSalvo() {
    try {
        const tema = localStorage.getItem(CHAVE);
        return ORDEM.includes(tema) ? tema : 'sistema';
    } catch {
        return 'sistema';
    }
}

function salvar(tema) {
    try {
        localStorage.setItem(CHAVE, tema);
    } catch {
        // Sem armazenamento (navegação privada/bloqueada): vale só para esta página.
    }
}

function aplicar(tema) {
    const escuro = tema === 'escuro' || (tema === 'sistema' && preferenciaEscura.matches);
    document.documentElement.classList.toggle('dark', escuro);

    const proximo = ORDEM[(ORDEM.indexOf(tema) + 1) % ORDEM.length];

    document.querySelectorAll('[data-tema-alternar]').forEach((botao) => {
        const rotulo = `${ROTULOS[tema]}. Clique para mudar para: ${ROTULOS[proximo].toLowerCase()}.`;

        botao.dataset.tema = tema;
        botao.setAttribute('aria-label', rotulo);
        botao.setAttribute('title', rotulo);

        botao.querySelectorAll('[data-tema-icone]').forEach((icone) => {
            icone.classList.toggle('hidden', icone.dataset.temaIcone !== tema);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let temaAtual = temaSalvo();
    aplicar(temaAtual);

    document.querySelectorAll('[data-tema-alternar]').forEach((botao) => {
        botao.addEventListener('click', () => {
            temaAtual = ORDEM[(ORDEM.indexOf(temaAtual) + 1) % ORDEM.length];
            salvar(temaAtual);
            aplicar(temaAtual);
        });
    });

    preferenciaEscura.addEventListener('change', () => {
        if (temaAtual === 'sistema') aplicar('sistema');
    });
});
