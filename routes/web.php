<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

//Cargando Clases
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return '<h2>hola carlos con LARAVEL</h2>';
});


Route::get('/welcome', function () {
    return view('welcome');
});


Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = '<h2>texto de una ruta</h2>';
    $texto .= 'Nombre: ' . $nombre;

    return view('pruebas', array(
        'texto' => $texto
    ));
});


Route::get('/animales', 'PruebasController@index');
Route::get('/test-orm', 'PruebasController@testOrm');

//Rutas de API
//son rutas de pruebas
//Route::get('/usuario/pruebas', 'UserController@pruebas');
//Route::get('/categoria/pruebas', 'CategoryController@pruebas');
//Route::get('/entrada/pruebas', 'PostController@pruebas');
//rutas del controlador de USUARIO
Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');

//Rutas de controlador de categorias...
Route::resource('/api/category', 'CategoryController');
