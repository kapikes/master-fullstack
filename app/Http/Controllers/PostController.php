<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller {

    //cargamos un constructor utilizamos lo protejemos por autentizacion menos en el index y show
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

//Metodo para listar todos los post que tenemos
    //Metodo en el que nos saca un listado de TODAS las entradas que necesitamos....
    public function index() {
        $post = Post::all()->load('category');
        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                        ], 200);
    }

    //Metodo para sacarnos el registro que nosotros queramos DETALLE
    public function show($id) {
        $post = Post::find($id);
        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada NO EXISTE'
            ];
        }


        return response()->json($data, $data['code']);
    }

//metodo para guardar un post
    public function store(Request $request) {
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Conseguir usuario identificado
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            //Validar los datos
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required',
                        'image' => 'required'
            ]);
            //Guardar la categoria
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'success',
                    'message' => 'No se ha guardado el post... FALTAN DATOS'
                ];
            } else {
                //Guardar el articulo
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();


                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente'
            ];
        }
        //Devolver los resultados

        return response()->json($data, $data['code']);
    }

    //Metodo para actualizar una entrada o post
    public function update($id, Request $request) {
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        //Datos para devolver
        $data = array(
            'code' => 400,
            'status' => 'error',
            'post' => 'Datos enviados incorrectos'
        );
        if(!empty($params_array)){
            
        
        //Validar los datos
        $validate = \Validator::make($params_array, [
                    'title' => 'required',
                    'content' => 'required',
                    'category_id' => 'required'
        ]);
        if($validate->fails()){
            $data['errors']=$validate->errors();
            return response()->json($data, $data['code']);
        }
        //Eliminar lo que no queremos actualizar
        unset($params_array['id']);
        unset($params_array['user_id']);
        unset($params_array['created_at']);
        unset($params_array['user']);

        //Actualizar el registro en concreto
        $post = Post::Where('id', $id)->update($params_array);
        //Devolver algo
        $data = array(
            'code' => 200,
            'status' => 'success',
            'post' => $params_array
        );
        }

        return response()->json($data, $data['code']);
    }

}
