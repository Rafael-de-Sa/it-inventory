<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
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
        dd('entrou no controller');

        $credentials = $request->validated();

        dd([
            'credenciais_recebidas' => $credentials,
            'auth_tentou' => Auth::attempt($credentials),
            'usuario' => Auth::user(),
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return route('/');
        }

        $email = $request->input('email');
        $password = $request->input('password');


        echo $email;
        echo '<br>';
        echo $password;
    }

    public function logout()
    {
        echo "Logout";
    }
}
