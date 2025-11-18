<?php

namespace Database\Seeders;

use App\Models\Funcionario;
use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcionarioUm = Funcionario::find(1);

        if (! $funcionarioUm) {
            $this->command?->warn('Funcionário com ID 1 não encontrado. Execute primeiro o FuncionarioSeeder.');
            return;
        }

        Usuario::create([
            'funcionario_id' => $funcionarioUm->id,
            'email'          => 'admin@gmail.com',
            'senha'          => Hash::make('123456')
        ]);
    }
}
