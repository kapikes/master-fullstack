<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de pruebas de user-Controller";
    }
    //creamos dos metodos register y login
    public function register(Request $request){
        $name=$request->input('name');
        $surname=$request->input('surname');
        return "Accion de registro de usuarios: $name $surname";
    }
    public function login(Request $request){
        return "Accion de login de usuario";
    }
}
