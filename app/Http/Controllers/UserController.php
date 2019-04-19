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
$pwd=hash('sha256', $params->password);
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
        $jwtAuth=new \JwtAuth();
        
        //Recibir datos con POST
        $json=$request->input('json',null);
        $params=json_decode($json);
        $params_array=json_decode($json,true);
        //Validar esos datos
        
       $validate=\Validator::make($params_array,[
    
    'email'=>'required|email',
    'password'=>'required'
    
]);

if($validate->fails()){
    
       $signup=array(
            'status'=>'error',
            'code'=>'404',
            'message'=>'El usuario no se ha podido LOGRAR',
           'errors'=>$validate->errors()
        );
}else{  
        //Cifrar la password
        $pwd=hash('sha256', $params->password);
        //Devolver token o datos
        $signup= $jwtAuth->signup($params->email, $pwd);
        if(!empty($params->gettoken)){
            $signup= $jwtAuth->signup($params->email, $pwd,true);
        }
}

         return response()->json($signup,200);
    }
    
    
  //actualizamos los datos del usuario  
    public function update(Request $request){
        //Comprobar que el usuario este identificado
        $token=$request->header('Authorization');
        $jwtAuth=new \JwtAuth();
        $checkToken=$jwtAuth->checkToken($token);
        
        //REcoger los datos por post
            $json=$request->input('json', null);
            $params_array=json_decode($json, true);
        
        if($checkToken && !empty($params_array)){
            
            
            
            //Sacar usuario identificado
            $user=$jwtAuth->checkToken($token, true);
            
            //Validar los datos
            $validate= \Validator::make($params_array,[
               'name'=>'required|alpha',
               'surname'=>'required|alpha',
               'email'=>'required|email|unique:users,'.$user->sub
               
            ]);
            //Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            
            //Acutalizar el usuario
            $user_update=User::where('id', $user->sub)->update($params_array);
            //Devolver array con el resultado
           $data=array(
               'code'   =>200,
               'status' =>'success',
               'user'=>$user,
               'changes'=>$params_array
           );
        }else{
           $data=array(
               'code'   =>400,
               'status' =>'error',
               'message'=>'El usuario no esta identificado'
           );
        }
        return response()->json($data,$data['code']);
    }
    

//Subir una imagen o avatar de usuario...
    public function upload(Request $request){
        //Recoger datos de la peticion
        $image=$request->file('file0');
        
        //Guardar imagen 
        if($image){
            $image_name=time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            
            $data=array(
                'code'   =>200,
                'status' =>'success',
                'image'  =>$image_name
            );
        }else{
            $data=array(
               'code'   =>400,
               'status' =>'error',
               'message'=>'ERROR al subir la imagen'
           );
            
        }
        
        
        return response()->json($data, $data['code']);
    }
}
