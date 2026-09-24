<?php

namespace App\Providers;

use App\Enums\Perfil;
use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->definirPermissoes();
    }

    /**
     * Permissões por área. Usadas nas rotas (middleware "can:..."), no menu (config/navegacao.php)
     * e nas views (@can). Para dar uma área a outro perfil, basta incluí-lo aqui.
     */
    private function definirPermissoes(): void
    {
        $permissoes = [
            // Empresas, setores, tipos de equipamento, equipamentos e usuários
            'gerenciar-cadastros' => [Perfil::TIC],
            // Termos de responsabilidade e devolução
            'gerenciar-movimentacoes' => [Perfil::TIC],
            // Listar, cadastrar, editar, consultar pendências, relatório e desligamento
            'gerenciar-funcionarios' => [Perfil::TIC, Perfil::DP],
            'excluir-funcionarios' => [Perfil::TIC],
        ];

        foreach ($permissoes as $permissao => $perfis) {
            Gate::define($permissao, fn (Usuario $usuario) => $usuario->temPerfil(...$perfis));
        }
    }
}
