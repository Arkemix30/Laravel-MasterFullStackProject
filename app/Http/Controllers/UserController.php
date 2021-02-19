<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return "Accion de Login de Usuario";
    }
}
