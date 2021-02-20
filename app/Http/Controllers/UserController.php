<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de prueba USER-CONTROLLER";
    }

    public function register(Request $request){

        //Recoger los datos del usuario por post
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $params_array = array_map('trim', $params_array);

        //Validar los datos
        $validate =  \Validator::make($params_array, [
            'name'      => 'required|alpha',
            'surname'   => 'required|alpha',
            'email'     => 'required|email|unique:users',
            'password'  => 'required',
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha creado',
                'errors' => $validate->errors()
            );
        }else {

            //Cifrar contrase;a
            $hashed_pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4 ]);
            
            //crear el usuario
            $user = new User();
            $user->name = $params_array['name'];
            $user->surname = $params_array['surname'];
            $user->email = $params_array['email'];
            $user->password = $hashed_pwd;
            $user->role = 'ROLE_USER';

            $user->save();

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El usuario se ha creado correctamente',
                'data' => $user
            );
        }

        
        

        

        //Devolver Mensaje
        return response()->json($data, $data['code']);  
    }

    public function login(Request $request){
        $jwtAuth = new \JwtAuth();

        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //Validar los datos
        $validate =  \Validator::make($params_array, [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        }else {

            $stored_user = User::where('email',$params->email)->first();
            $stored_pwd = $stored_user->password;
            
            if (password_verify($params->password, $stored_pwd)){
                $signup = $jwtAuth->signup($params->email, $stored_pwd);
                if (!empty($params->getToken)){
                    $signup = $jwtAuth->signup($params->email, $stored_pwd, true);
                }
            } else { 
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Contraseña Incorrecta'
                );
            }
        }
        return response()->json($signup);
        //return response()->json($signup, $signup->code || 200);
    }

    public function update(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);


        if ($checkToken && !empty($params_array)){
            
            $user = $jwtAuth->checkToken($token, true);

            $validate =  \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email'
            ]);

            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error en la validacion de los parametros del usuario',
                    'errors' => $validate->errors()
                );
            }else {
                $user_update = User::where('id', $user->sub)->update($params_array);

                $data = array(
                    'code'=> 200,
                    'status' => 'success',
                    'user'=> $user,
                    'changes'=>$params_array
                );
            }
        }else{
            $data= array(
                'code'=> 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado o posee campos vacios'
            );
        }
        return response()->json($data, $data['code']);
    }


    public function upload(Request $request){

        //Recoger datos de la peticion
        $image = $request->file('file0');

        $imagevalidate = \Validator::make($request->all(), [
            'file0'=> 'required|image|mimes:png,jpg,jpeg,gif,svg'
        ]);
        //Subir imagen
        if(!$image || $imagevalidate->fails()){
            $data = array(
                'code'=> 400,
                'status'=> 'error',
                'Mesage'=> 'Error al subir imagen',
                'Error'=> $imagevalidate->errors()
            );
        }else{

            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name,\File::get($image));

            $data = array(
                'code'=> 200,
                'status'=> 'success',
                'Mesage'=> 'Imagen guardada',
                'image'=> $image_name
            );
            
        }        
        return response()->json($data, $data['code']);
    }
    public function getImage($filename){

        $isset = \Storage::disk('users')->exists($filename);
        if ($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else{
            
            $data = array(
                'code'=> 404,
                'status'=> 'error',
                'Mesage'=> 'La imagen no existe'
            );
            return response()->json($data, $data['code']);
        }

    }

    
}
