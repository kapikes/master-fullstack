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
//recoger los datos del usuario por post lo haremos con un json
$json=$request->input('json', null);
$params=json_decode($json);
$params_array=json_decode($json,true);

var_dump($params_array);
die();
//validar los datos
//cifrar la contraseña
//comprobar que el usuario existe (duplicado)
//crear el usuario
 
        


//devolver los datos con un json
        $data=array(
            'status'=>'error',
            'code'=>'404',
            'message'=>'El usuario no se ha creado'
        );
        return response()->json($data, $data['code']);
    }
    public function login(Request $request){
        return "Accion de login de usuario";
    }
}
