<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

// ASIGNAR ROLES O CAMBIAR
// EJEMPLO DE IMPLEMENTACIÓN AL ULTIMO
// $usuario->syncRoles('admin'); // Reemplaza el rol actual con 'admin'
// $usuario->syncRoles(['editor', 'admin']); // El usuario ahora tiene los roles 'editor' y 'admin'
// $usuario->removeRole('user'); // Remueve solo el rol 'user'
// $usuario->assignRole('editor'); // Ahora el usuario tiene tanto 'user' como 'editor' - es para asignar rol al usuario y si ya tiene uno ahora tendra dos


class userController extends Controller
{
    //

    public function laravel(){
        return response()->json("Esto es un recodatorio de como se usa laravel");
    }

    public function registro(Request $peticion){

        $peticion->validate([
            "usuario" => 'required|min:3',
            "email" => 'required|email|unique:users',
            // "password" => 'required|min:8|confirmed',
            "password" => 'required|min:8',
        ]);

        $usuario = new User();

        $usuario->usuario = $peticion->usuario;
        $usuario->email = $peticion->email;
        $usuario->password = Hash::make($peticion->password);
        $usuario->activo = 1;
        // $usuario->ultimo_acceso = date('Y-m-d H:i:s');
        
        // SE PUEDE CREAR UNA TABLA DONDE SE COLOQUE UNA TABLA PARA CLIENTES (EMPRESAS)
        $usuario->assignRole('usuario');
        
        $usuario->save();


        return response()->json($usuario, Response::HTTP_CREATED);


    }


    public function login(Request $peticion){

        $bandera = false;

        $credenciales = $peticion->validate([
            'email'=>['required', 'email'],
            // 'name'=>['required'],
            'password'=>['required'],
        ]);
 
        $buscarUsuario = $peticion->email;

        if(Auth::attempt($credenciales)){
            $usuario = User::where('email', '=', $buscarUsuario)->first();
            $rol = $usuario->getRoleNames();

            $auth = Auth::user();
            $token = $auth->createToken('token')->plainTextToken;
            return response([
            "valido"=>!$bandera,
            "token"=>$token,
            "mssg" => "credenciales correctas",
            "id"=>$usuario->id,
            "usuario"=>$usuario->usuario,
            "email"=>$usuario->email,
            "rol" => $rol
            
        ], 200);


        }else{
            return response()->json([
              "mssg"=> "credenciales incorrectas",
            ],401);
        }



    }


    public function getUsuario($id){
        $usuario = new User();

        $user = User::where('id', '=', $id)->first();
        //$user->assignRole('usuario');
        $rol = $user->getRoleNames();

        // Ocultar la relación de roles en la respuesta del usuario
        $user->makeHidden('roles');

        return response()->json([
            'Usuario' => $user,
            'Roles' => $rol
        ]);
    }

    public function recuperarContrasenia(){

    }

}





// Ejemplo Completo de Cambio de Rol


// public function cambiarRol(Request $peticion, User $usuario)
// {
//     // Validar que el nuevo rol existe y que se pasa en la petición
//     $peticion->validate([
//         'rol' => 'required|exists:roles,name', // Asegúrate de que el rol exista en la tabla roles
//     ]);

//     // Reemplazar el rol actual del usuario con el nuevo rol
//     $usuario->syncRoles($peticion->rol);

//     return response()->json([
//         'message' => 'Rol actualizado correctamente',
//         'usuario' => $usuario,
//     ], 200);
// }

// Consideraciones Importantes
// syncRoles(): Este método es útil si siempre quieres que el usuario tenga exactamente el rol que le pasas, eliminando cualquier rol anterior.
// assignRole(): Solo asigna roles sin eliminar otros. Esto es útil si quieres que el usuario pueda tener varios roles.
// removeRole(): Remueve un rol específico del usuario sin afectar otros roles que pueda tener.
