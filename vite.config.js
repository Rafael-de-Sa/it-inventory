import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // Scripts carregados por página via @vite(...) também precisam estar aqui,
            // senão não entram no manifest do build e a página quebra em produção.
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/empresas/empresa-form.js',
                'resources/js/equipamentos/equipamento-form.js',
                'resources/js/funcionarios/funcionario-form.js',
                'resources/js/movimentacoes/movimentacao-devolucao-form.js',
                'resources/js/movimentacoes/movimentacao-filtros.js',
                'resources/js/movimentacoes/movimentacao-form.js',
                'resources/js/usuarios/usuario-form.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
