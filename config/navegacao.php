<?php

/*
|--------------------------------------------------------------------------
| Menu principal
|--------------------------------------------------------------------------
| Fonte única do menu (desktop e mobile) renderizado em layouts/top_bar.
| Cada item tem 'rota' + 'ativo' (padrão para request()->routeIs) ou 'itens' (submenu).
| 'can' (opcional) é a permissão exigida (Gates em AppServiceProvider); submenus sem itens visíveis somem.
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
            ['rotulo' => 'Empresas', 'icone' => 'fa-regular fa-building', 'rota' => 'empresas.index', 'ativo' => 'empresas.*', 'can' => 'gerenciar-cadastros'],
            ['rotulo' => 'Setores', 'icone' => 'fa-solid fa-diagram-project', 'rota' => 'setores.index', 'ativo' => 'setores.*', 'can' => 'gerenciar-cadastros'],
            ['rotulo' => 'Tipos de Equipamento', 'icone' => 'fa-solid fa-sitemap', 'rota' => 'tipo-equipamentos.index', 'ativo' => 'tipo-equipamentos.*', 'can' => 'gerenciar-cadastros'],
            ['rotulo' => 'Equipamentos', 'icone' => 'fa-solid fa-computer', 'rota' => 'equipamentos.index', 'ativo' => 'equipamentos.*', 'can' => 'gerenciar-cadastros'],
            ['rotulo' => 'Funcionários', 'icone' => 'fa-solid fa-user-tie', 'rota' => 'funcionarios.index', 'ativo' => 'funcionarios.*', 'can' => 'gerenciar-funcionarios'],
            ['rotulo' => 'Usuários', 'icone' => 'fa-solid fa-users-gear', 'rota' => 'usuarios.index', 'ativo' => 'usuarios.*', 'can' => 'gerenciar-cadastros'],
        ],
    ],
    [
        'rotulo' => 'Operações',
        'icone' => 'fa-solid fa-arrows-rotate',
        'itens' => [
            ['rotulo' => 'Movimentações', 'icone' => 'fa-solid fa-list', 'rota' => 'movimentacoes.index', 'ativo' => 'movimentacoes.index', 'can' => 'gerenciar-movimentacoes'],
            ['rotulo' => 'Termo de responsabilidade', 'icone' => 'fa-solid fa-file-signature', 'rota' => 'movimentacoes.create', 'ativo' => 'movimentacoes.create', 'can' => 'gerenciar-movimentacoes'],
            ['rotulo' => 'Termo de devolução', 'icone' => 'fa-solid fa-box-open', 'rota' => 'movimentacoes.devolucao.create', 'ativo' => 'movimentacoes.devolucao.*', 'can' => 'gerenciar-movimentacoes'],
        ],
    ],
];
