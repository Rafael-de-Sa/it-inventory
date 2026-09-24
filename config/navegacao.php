<?php

/*
|--------------------------------------------------------------------------
| Menu principal
|--------------------------------------------------------------------------
| Fonte única do menu (desktop e mobile) renderizado em layouts/top_bar.
| Cada item tem 'rota' + 'ativo' (padrão para request()->routeIs) ou 'itens' (submenu).
*/

return [
    [
        'rotulo' => 'Dashboard',
        'icone' => 'fa-solid fa-gauge',
        'rota' => '/',
        'ativo' => '/',
    ],
    [
        'rotulo' => 'Cadastros',
        'icone' => 'fa-solid fa-folder-tree',
        'itens' => [
            ['rotulo' => 'Empresas', 'icone' => 'fa-regular fa-building', 'rota' => 'empresas.index', 'ativo' => 'empresas.*'],
            ['rotulo' => 'Setores', 'icone' => 'fa-solid fa-diagram-project', 'rota' => 'setores.index', 'ativo' => 'setores.*'],
            ['rotulo' => 'Tipos de Equipamento', 'icone' => 'fa-solid fa-sitemap', 'rota' => 'tipo-equipamentos.index', 'ativo' => 'tipo-equipamentos.*'],
            ['rotulo' => 'Equipamentos', 'icone' => 'fa-solid fa-computer', 'rota' => 'equipamentos.index', 'ativo' => 'equipamentos.*'],
            ['rotulo' => 'Funcionários', 'icone' => 'fa-solid fa-user-tie', 'rota' => 'funcionarios.index', 'ativo' => 'funcionarios.*'],
            ['rotulo' => 'Usuários', 'icone' => 'fa-solid fa-users-gear', 'rota' => 'usuarios.index', 'ativo' => 'usuarios.*'],
        ],
    ],
    [
        'rotulo' => 'Operações',
        'icone' => 'fa-solid fa-arrows-rotate',
        'itens' => [
            ['rotulo' => 'Movimentações', 'icone' => 'fa-solid fa-list', 'rota' => 'movimentacoes.index', 'ativo' => 'movimentacoes.index'],
            ['rotulo' => 'Termo de responsabilidade', 'icone' => 'fa-solid fa-file-signature', 'rota' => 'movimentacoes.create', 'ativo' => 'movimentacoes.create'],
            ['rotulo' => 'Termo de devolução', 'icone' => 'fa-solid fa-box-open', 'rota' => 'movimentacoes.devolucao.create', 'ativo' => 'movimentacoes.devolucao.*'],
        ],
    ],
];
