<?php

namespace Database\Seeders;

use App\Enums\Perfil;
use App\Models\Funcionario;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuários de acesso: admin@gmail.com (TIC) e dp@gmail.com (Departamento Pessoal), ambos com a senha 123456.
 * Pode ser executado de novo sem duplicar registros.
 */
class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rafael de Sá (FuncionarioSeeder)
        $funcionarioUm = Funcionario::where('cpf', '38421835009')->first() ?? Funcionario::find(1);

        if (! $funcionarioUm) {
            $this->command?->warn('Funcionário do admin não encontrado. Execute primeiro o FuncionarioSeeder.');
            return;
        }

        Usuario::firstOrCreate(['email' => 'admin@gmail.com'], [
            'funcionario_id' => $funcionarioUm->id,
            'senha'          => Hash::make('123456'),
            'perfil'         => Perfil::TIC,
        ]);

        $setorDp = Setor::where('nome', 'Departamento Pessoal')->first() ?? Setor::first();

        $funcionariaDp = Funcionario::withTrashed()->firstOrCreate(['cpf' => '52998224725'], [
            'setor_id'     => $setorDp->id,
            'nome'         => 'Ana',
            'sobrenome'    => 'Pereira',
            'matricula'    => '0002',
            'admitido_em'  => '2024-02-01',
            'telefone'     => '44977776666',
            'terceirizado' => false,
        ]);

        Usuario::firstOrCreate(['email' => 'dp@gmail.com'], [
            'funcionario_id' => $funcionariaDp->id,
            'senha'          => Hash::make('123456'),
            'perfil'         => Perfil::DP,
        ]);
    }
}
