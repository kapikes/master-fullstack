<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table= 'Categories';
    //Relacion de uno a muchos...
    public function post(){
        return $this->hasMany('App\Post');
    }
}
