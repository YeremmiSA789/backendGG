<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\Juegos;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    //

    public function getCategorias(){

        $categorias = Categorias::select(
            "id",
            "categoria",
            "descripcion",
            "activo",
        )->get();


        return response()->json(
            $categorias
        );

    }
    public function getCategoria_juego($id){

        $categoria_id = Categorias::select(
            "id",
            "categoria",
            "descripcion",
            "activo",
        )->where("id","=", $id)
        ->first();


        return response()->json(
            $categoria_id
        );

    }
    
    public function getVersionJuego($id){
        $version_juego_id = Juegos::select(
            
        );
    }

}
