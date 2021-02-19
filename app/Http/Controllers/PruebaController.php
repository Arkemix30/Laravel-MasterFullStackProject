<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

class PruebaController extends Controller
{
    public function index(){
        $titulo = 'Animales';
        $animales = ['Perro', 'Gato', 'Tigre'];

        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }

    public function testORM(){
        $posts = Post::all();
        $categories = Category::all();
        foreach($posts as $post){
            echo "<h1>".$post->title."</h1>";
            echo "<h2>{$post->user->name}</h2>";
            echo "<p>".$post->content."</p>";
            echo "<hr>";
        }
        
        die();
    }
}
