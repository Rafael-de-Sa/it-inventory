<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginSubmit(LoginRequest $request)
    {
        $credentials = $request->validated();

        $usuario = Usuario::where('email', $credentials['email'])
            ->where('ativo', true)
            ->whereNull('apagado_em')
            ->first();
        if (!$usuario) {
            return back()->withInput()->with(['error' => 'Login Inválido']);
        }

        if (!password_verify($credentials['password'], $usuario->senha)) {
            return back()->withInput()->with(['error' => 'Login Inválido']);
        }

        $usuario->ultimo_login = now();
        $usuario->save();

        $request->session()->regenerate();
        Auth::login($usuario);

        return redirect()->intended(route('/'));
    }

    public function logout()
    {
        session()->flush();
        return redirect('login');
    }
}
