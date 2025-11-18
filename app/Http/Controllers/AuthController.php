<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required']
            ],
            [
                'email.required' => 'O e-mail é obrigatório',
                'email.email' => 'O e-mail deve ser válido',
                'password.required' => 'A senha é obrigatória'
            ]
        );

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
