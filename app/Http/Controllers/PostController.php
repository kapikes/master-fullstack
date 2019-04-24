<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;

class PostController extends Controller
{
     //cargamos un constructor utilizamos lo protejemos por autentizacion menos en el index y show
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }
   //Metodo en el que nos saca un listado de TODAS las entradas que necesitamos....
    public function index(){
        $post= Post::all()->load('category');
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'post'=>$post
        ],200);
    }
    //Metodo para sacarnos el registro que nosotros queramos DETALLE
    public function show($id){
        $post=Post::find($id);
        if(is_object($post)){
            $data=[
            'code'=>200,
            'status'=>'success',
            'post'=>$post
        ];
            }else{
               $data=[
            'code'=>404,
            'status'=>'error',
            'message'=>'La entrada NO EXISTE'
        ]; 
            }
            
            
           return response()->json($data, $data['code']); 
        
    }
}
