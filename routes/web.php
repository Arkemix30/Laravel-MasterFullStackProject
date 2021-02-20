<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\ApiAuthMiddleware;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/animales', [PruebaController::class, 'index']);
Route::get('/testORM', [PruebaController::class, 'testORM']);

//Rutas API
Route::get('/api/users/pruebas', [UserController::class, 'pruebas']);
Route::get('/api/posts/pruebas', [PostController::class, 'pruebas']);
Route::get('/api/categories/pruebas', [CategoryController::class, 'pruebas']);  

Route::post('/api/user/register', [UserController::class, 'register']);
Route::post('/api/user/login', [UserController::class, 'login']);
Route::put('/api/user/update', [UserController::class, 'update']);

Route::post('/api/user/upload',[UserController::class, 'upload'])->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}',[UserController::class, 'getImage']);
Route::get('/api/user/detail/{id}',[UserController::class, 'userdetail']);