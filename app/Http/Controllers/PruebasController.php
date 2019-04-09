<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function index(){
        $titulo='Animales';
        $animales=['Perro','Gato','Tigre'];
        
        return view('pruebas.index',array(
            'titulo'=>$titulo,
            'animales'=>$animales,
        ));
    }
    //se puede hacer de una manera o de la otra...
    public function testOrm(){
//        //sacarme todos los datos
//        $posts= Post::all();
//        foreach($posts as $post){
//            echo "<h1>".$post->title."</h1>";
//            echo "<p>".$post->content."</p>";
//        }
        
        //sacarme todos los datos
        $categories= Category::all();
        foreach($categories as $category){
            echo "<h1>{$category->name}</h1>";
           foreach($category->post as $post){
            echo "<h1>".$post->title."</h1>";
            echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>"; 
            echo "<p>".$post->content."</p>";
            
            
        }
        echo '<hr>';
    }
    die();
    }
}
