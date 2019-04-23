<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller {

//con este metodo sacamos TODAS LAS CATEGORIAS en nuesta bbdd
    public function index() {
        $categories = Category::all();
        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'categories' => $categories
        ]);
    }

    public function show($id) {
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

}
