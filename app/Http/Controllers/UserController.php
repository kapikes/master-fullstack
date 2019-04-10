<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

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

if(!empty($params)&& !empty($params_array)){
    

//limpiar los datos
$params_array=array_map('trim',$params_array);
//validar los datos
$validate=\Validator::make($params_array,[
    'name'=>'required|alpha',
    'surname'=>'required|alpha',
    'email'=>'required|email|unique:users',
    'password'=>'required'
    
]);

if($validate->fails()){
    
       $data=array(
            'status'=>'error',
            'code'=>'404',
            'message'=>'El usuario no se ha creado',
           'errors'=>$validate->errors()
        );
    
}else{
     $data=array(
            'status'=>'succes',
            'code'=>'200',
            'message'=>'El usuario se ha creado CORRECTAMENTE'
           
        );
}
}else{
    $data=array(
            'status'=>'error',
            'code'=>'404',
            'message'=>'Los datos enviados NO SON CORRECTOS'
           
        );
}
//cifrar la contraseña
$pwd=password_hash($params->password, PASSWORD_BCRYPT, ['cost'=>4]);
//comprobar que el usuario existe (duplicado)

//crear el usuario
$user=new User();
$user->name=$params_array['name'];
$user->surname=$params_array['surname'];
$user->email=$params_array['email'];
$user->password=$pwd;
$user->role='ROLE_USER';


 //Guardar el usuario
$user->save();
        


//devolver los datos con un json
     
        return response()->json($data, $data['code']);
    }
    public function login(Request $request){
        return "Accion de login de usuario";
    }
}
