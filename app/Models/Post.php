<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    //Relacion Muchos a uno para Usuarios
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //Relacion Muchos a uno para Categorias
    public function category() {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
    use HasFactory;
}
