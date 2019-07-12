<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function pruebas(Request $request){
      return "Acción de pruebas de UserController";
    }

    public function register(Request $request){

      //Recoger los datos del usuario por POST
      $json = $request->input('json', null);
      $params= json_decode($json); //Decodificar el json
      $params_array= json_decode($json, true);

      //Limpiar datos
      $params_array = array_map('trim', $params_array);

      //Validar datos
      $validate = \Validator::make($params_array, [
        'name'    => 'required|alpha',
        'surname' => 'required|alpha',
        'email'   => 'required|email|unique:users',//Comprobar si usuario existe (duplicado)
        'password'=> 'required'
      ]);
      if(!empty($params) && !empty($params_array)){
        if($validate->fails()){
          //Validación ha fallado
          $data = array(
            'status'=>'error',
            'code'=> 404,
            'message' => 'El usuario no se ha creado correctamente',
            'errors' => $validate->errors()
          );
        }else{
          //Validación correcta
            //Cifrar la contraseña
          // $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost'=>4]);
          $pwd = hash('sha256', $params->password);
            //Crear el usuario
          $user = New User();
          $user->name = $params_array['name'];
          $user->surname = $params_array['surname'];
          $user->email = $params_array['email'];
          $user->role = $params_array['role'];
          $user->password = $pwd;
            //Guardar el usuario
          $user->save();
          $data = array(
            'status'=>'success',
            'code'=> 200,
            'message' => 'El usuario se ha creado correctamente'
          );
        }
      } else {
        $data = array(
          'status'=>'error',
          'code'=> 400,
          'message' => 'Los datos enviados no son correctos'
        );
      }

      return response()->json($data, $data['code']);
    }
    public function login(Request $request){
      $jwtAuth = new \JwtAuth();

      //Recibir datos por POST
      $json = $request->input('json', null);
      $params = json_decode($json);
      $params_array = json_decode($json, true);


      //Validar esos datos
      $validate = \Validator::make($params_array, [
        'email'   => 'required|email',//Comprobar si usuario existe (duplicado)
        'password'=> 'required'
      ]);
      if($validate->fails()){
        //Validación ha fallado
        $signup = array(
          'status'=>'error',
          'code'=> 404,
          'message' => 'El usuario no se ha identificado correctamente',
          'errors' => $validate->errors()
        );
      }else{
        //Validación correcta
        //Cifrar la contraseña
        $pwd = hash('sha256', $params->password);
        //Devolver datos
        $signup=$jwtAuth->signup($params->email, $pwd);
        if(!empty($params->gettoken)){
          $signup=$jwtAuth->signup($params->email, $pwd, true);
        }
        $data = array(
          'status'=>'success',
          'code'=> 200,
          'message' => 'El usuario se ha creado correctamente'
        );
      }
      //Cifrar la contraseña
      return response()->json($signup, 200);
    }
    public function update(Request $request){

      //Comprobar si el usuario está identificado
      $token = $request->header('Authorization');
      $jwtAuth = new \JwtAuth();
      $checkToken = $jwtAuth->checkToken($token);
      //Recoger los datos por POST
      $json=$request->input("json", null);
      $params_array = json_decode($json, true);
      if ($checkToken && !empty($params_array)) {
      //Actualizar el usuario
        //Conseguir usuario identificado
        $user = $jwtAuth->checkToken($token, true);
        //Validar los datos
        $validate = \Validator::make($params_array, [
          'name'    => 'required|alpha',
          'surname' => 'required|alpha',
          'email'   => 'required|email|unique:users,'.$user->sub//Comprobar si usuario existe (duplicado)
        ]);
        //Quitar los campos que no quiero actualizar
        unset($params_array['id']);
        unset($params_array['role']);
        unset($params_array['password']);
        unset($params_array['created_at']);
        unset($params_array['remember_token']);
        //Actualizar el usuario en la base de datos
        $user_update = User::where('id', $user->sub)->update($params_array);
        //Devolver un array con el resultado
        $data = array(
          'code'=> 200,
          'status'=> 'success',
          'user'=> $user,
          'changes'=>$params_array
        );
        echo "<h1>Login Correcto</h1>";
      } else {
        //
        $data = array(
          'code'=> 400,
          'status'=> 'error',
          'message'=> "Usuario no identificado"
        );
      }
      return response()->json($data, $data['code']);
    }

    public function upload(Request $request){
      //Recoger los datos de la petición
      $image = $request->file('file0');

      //Validar si es una imagen
      $validate = \Validator::make($request->all(),[
        'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
      ]);
      //Guardar la imagen
      if(!$image || $validate->fails()){
        $data = array(
          'code'=> 400,
          'status'=> 'error',
          'message'=> "Usuario al subir imagen"
        );
      } else {
        $image_name = time().$image->getClientOriginalName();
        \Storage::disk('users')->put($image_name, \File::get($image));
        //Devolver el resultado

        $data = array(
          'image' => $image_name,
          'status' => 'success',
          'code' => 200,
        );
      }
      return response()->json($data, $data['code']);
    }

    public function getImage($filename){
      $isset = \Storage::disk('usuarios')->exists($filename);
      // $file = \Storage::disk('users')->get($filename);
      // return new Response($file,200);

      // if ($isset){
      // } else {
      //   $data = array(
      //     'code'=> 400,
      //     'status'=> 'error',
      //     'message'=> "La imagen no existe"
      //   );
      //   return response()->json($data, $data['code']);
      // }
    }
}
