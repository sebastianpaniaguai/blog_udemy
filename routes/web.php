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

use App\Http\Middleware\ApiAuthMiddleware;

//Rutas de prueba
Route::get('/', function () {
    return view('welcome');
});

Route::get('/pruebas/{nombre?}', function ($nombre = null){
  $texto = '<h2>Texto desde una ruta</h2>';
  $texto .= 'Nombre: '.$nombre;
  return view('pruebas', array(
    'texto'=>$texto
  ));
});


Route::get('/test-orm', 'PruebasController@testOrm');

// Rutas del API

  /*********************
    Métodos Http
      * GET:    Conseguir datos o recursos.
      * POST:   Guardar datos o recursos o hacer logica o recibir lógica desde un formulario.
      * PUT:    Actualizar datos o recursos.
      * DELETE: Eliminar datos o recursos.
  **********************/

  //Rutas de prueba
  Route::get('/usuario/pruebas', 'UserController@pruebas');
  Route::get('/categoria/pruebas', 'CategoryController@pruebas');
  Route::get('/post/pruebas', 'PostController@pruebas');

  //Rutas del controlador de usuarios
  Route::post('/api/register', 'UserController@register');
  Route::post('/api/login', 'UserController@login');
  Route::put('/api/user/update', 'UserController@update');
  Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
  Route::post('/api/user/upload','UserController@upload')->middleware(ApiAuthMiddleware::class);
