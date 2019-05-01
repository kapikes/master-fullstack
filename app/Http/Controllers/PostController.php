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

}
