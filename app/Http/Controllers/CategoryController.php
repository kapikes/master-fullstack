<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller {

    //cargamos un constructor utilizamos lo protejemos por autentizacion menos en el index y show
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

//con este metodo sacamos TODAS LAS CATEGORIAS en nuesta bbdd
    public function index() {
        $categories = Category::all();
        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'categories' => $categories
        ]);
    }

//metodo para mostrar una categoria en concreto
    public function show($id) {
        $post=Post::find($id)->load('category');
        //saco de la bbdd el registro que necesito
        $category = Category::find($id);
        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'la categoria NO EXISTE'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if (!empty($params_array)) {


//Validar los datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);
            //Guardar la categoria
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'success',
                    'message' => 'No se ha guardado la categoria'
                ];
            } else {
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => $category
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        //Devolver los resultados

        return response()->json($data, $data['code']);
    }

//Metodo para MODIFICAR/ACTUALIZAR  TODOS los datos    
    public function update($id, Request $request) {
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if (!empty($params_array)) {


            //Validar los datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);
            //Quitar lo que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            //Actualizar el registro (datos)
            $category = Category::where('id', $id)->update($params_array);
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $params_array
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        //Devolver respuesta
        return response()->json($data, $data['code']);
    }

}
