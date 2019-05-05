<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller {

    //cargamos un constructor utilizamos lo protejemos por autentizacion menos en el index y show
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show','getImage']]);
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
            $user = $this->getIdentity($request);

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
        if (!empty($params_array)) {


            //Validar los datos
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required'
            ]);
            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }
            //Eliminar lo que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

//Conseguir el usuario identificado
            $user = $this->getIdentity($request);

            //Buscar el registro a actualizar    
            $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();
            if (!empty($post) && is_object($post)) {

                //Actualizar el registro en concreto
                $post->update($params_array);
                //Devolver algo
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                );
            }
            /*
              $where= [
              'id' => $id,
              'user_id' => $user->sub
              ];
              $post = Post::updateOrCreate($where, $params_array);

             */
        }

        return response()->json($data, $data['code']);
    }

    //Eliminar un post utilizando un metodo
    public function destroy($id, Request $request) {
        //Conseguir usuario identificado
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        //Conseguir usuario identificado
        //$user= $this->getIdentity($request);
        //Conseguir el registro
        $post = Post::where('id', $id)
                ->where('user_id', $user->sub)
                ->first();

        //CREAMOS UN IF SI EXISTE EL POST O NO
        if (!empty($post)) {
            //Borrarlo
            $post->delete();
            //Devolver algo
            $data = [
                'code' => 200,
                'status' => 'succes',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'post' => 'El post NO EXISTE'
            ];
        }

        return response()->json($data, $data['code']);
    }

    private function getIdentity($request) {
        //Conseguir usuario identificado
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }
    //Metodo para subir una imagen
public function upload(Request $request){
    //Recoger la imagen de la peticion
    $image=$request->file('file0');
    //Validar la imagen
    $validate=\Validator::make($request->all(), [
        'file0' =>'required|image|mimes:jpg,jpeg,png,gif'
        ]);
    //Guardar la imagen
    if(!$image || $validate->fails()){
        $data=[
            'code' => 400,
            'status' =>'error',
            'message' =>'Error al subir la imagen'
        ];
    }else{
        $image_name=time().$image->getClientOriginalname();
        
        \Storage::disk('images')->put($image_name, \File::get($image));
        $data=[
            'code' => 200,
            'status' =>'success',
            'image' =>$image_name
        ];
    }
    //Devolver datos
    return response()->json($data, $data['code']);
}
//Metodo para conseguir una imagen
public function getImage($filename){
    //Comprobar si existe una imagen
    $isset=\Storage::disk('images')->exists($filename);
    if($isset){
    //Conseguir la imagen
        $file= \Storage::disk('images')->get($filename);
        //Devolver la imagen
        return new Response($file, 200);
    }else{
        $data=[
            'code' => 404,
            'status' =>'error',
            'message' =>'La imagen NO EXISTE'
        ];
    }
    return response()->json($data, $data['code']);
    
    
    
    
    //Mostrar error
}
}
