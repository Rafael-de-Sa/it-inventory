<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    //Teste
    public function index()
    {
        return view('index');
    }

    public function NewEmployer()
    {
        return  view('employers.create');
    }
}
