<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cesta;
use App\Models\Galeria_Juegos;
use App\Models\Juegos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;

class JuegosController extends Controller
{
    //
    public function getCarruselJuegos()
    {
        $juegos = new Juegos();


        $carrusel = Juegos::select(
            'id',
            'titulo',
            'portada',
            'versionJuego',
            'precio',
            'descuento',
            'precioDescontado',
        )->where('descuento', '!=', 0)
            ->get();



        return response()->json($carrusel);
    }

    public function getJuegoCompleto($id)
    {
        // Obtener el juego con su logo relacionado
        $juegos = Juegos::join('logo_juegos', 'logo_juegos.juego_id', '=', 'juegos.id')
            // ->join('galeria_juegos', 'galeria_juegos.juego_id', '=', 'juegos.id')
            // ->select('juegos.*', 'logo_juegos.*', 'galeria_juegos.*') // seleccionamos los campos necesarios
            ->select('juegos.*', 'logo_juegos.*') // seleccionamos los campos necesarios
            ->where('juegos.id', '=', $id) // id de búsqueda
            ->first(); //SE COLOCA FIRST para que devuelva llaves {} y no corchetes []
        // en este caso es get, porque como se consultan muchas imagenes con el mismo id del juego //... en este caso no, porque la galería es en otra consulta

        // $imagenesGaleria = Galeria_Juegos::where('juego_id', '=', $id)
        // ->pluck('ruta_img');


        return response()->json($juegos);

        // return response()->json([
        //     "Juegos" => $juegos,
        //     "Galeria" => $imagenesGaleria
        // ]);
    }


    public function getGaleria($id)
    {

        $infoGaleria = Galeria_Juegos::select('id', 'ruta_img', 'activo')->where('juego_id', '=', $id)->get();

        // Obtener solo las rutas de imágenes de la galería asociadas al juego
        $galeria = Galeria_Juegos::where('juego_id', '=', $id)
            ->pluck('ruta_img'); // Devuelve solo las rutas de las imágenes
        return response()->json($infoGaleria);
    }

    public function getNormal()
    {

        $carrusel = Juegos::select(
            'id',
            'titulo',
            'portada',
            'versionJuego',
            'precio'
        )
            ->where('descuento', 0)
            ->orWhere('descuento', null)
            ->get();



        return response()->json($carrusel);
    }


    public function addCesta(Request $peticion)
    {

        $id_usuario = $peticion->usuario;
        $id_juego = $peticion->juego;

        $cesta = new Cesta();
        $cesta->users_id = $id_usuario;
        $cesta->juego_id = $id_juego;
        $cesta->activo = 1;
        $cesta->save();

        // $juego = New Juegos();

        // $juego = User::join('cesta','cesta.users_id', '=', 'users.id')
        // ->join('juegos','juegos.id', '=', '');

        $cestasInfo = Cesta::with(['user:id,usuario', 'juego:id,titulo,portada,versionJuego,precio,descuento,precioDescontado'])
            ->where('activo', 1) // Puedes filtrar por cestas activas
            ->get();


        return response()->json([
            // "Cesta creada" => $cesta,
            "Info de la cesta" => $cestasInfo
        ]);
    }


    public function getCesta($id) //esta es la funcion que se esta usando, aunque la segunda tiene la consulta normal
    {

        // if ($id != 0) {

            // $cestasInfo = Cesta::with(['user', 'juego'])
            $cestasInfo = Cesta::with(['user:id,usuario', 'juego:id,titulo,portada,versionJuego,precio,descuento,precioDescontado'])
                ->where('activo', 1)
                ->where('users_id', '=', $id) // Puedes filtrar por cestas activas
                ->get();

            // Calcula el total sumando los precios descontados
            $total = $cestasInfo->sum(function ($cesta) {
                // Si hay un precio descontado, úsalo; de lo contrario, usa el precio normal
                return $cesta->juego->precioDescontado ?? $cesta->juego->precio;
            });

            return response()->json([
                "Info de la cesta" => $cestasInfo,
                "Total" => $total
            ]);
            
        // }else{
        //     return response()->json([
        //         "Info de la cesta" => "No tiene ningun producto",
        //         "Total" => 0
        //     ]);
        // }
    }


    public function desactivarCesta($id){
        $productoEncontrado = Cesta::find($id);

        if($productoEncontrado){
            // Modifica el campo 'activo' y guarda los cambios
            $productoEncontrado->activo = 0;
            $productoEncontrado->save();

            return response()->json("Producto desactivado");
        }else{
            return response()->json('producto no encontrado', 200); //colocar el 200 aunque no se encuentre el elemento, la función ha hecho su trabajo y no ha ocurrido algún error
        }
    }


    public function revisarCesta($id){

        $bandera = false;


        // Consulta los productos en la cesta que están activos para este usuario
        $consulta = Cesta::select("juego_id", 'id')
        ->where("users_id", '=', $id)
        ->where('activo', '=', 1)
        ->get();


        if($consulta->isNotEmpty()){
            $bandera = !$bandera;

            // return response()->json($bandera);
            return response()->json([
                "ok" => $bandera,
                "Juegos_ID" => $consulta
            ],200);

        }else{
            return response()->json($bandera,200);
        }
        

    }

    

    

    public function getCesta2($id)
    {
        $cestas = DB::table('cestas')
            ->join('users', 'cestas.users_id', '=', 'users.id')
            ->join('juegos', 'cestas.juego_id', '=', 'juegos.id')
            ->select('cestas.*', 'users.name as user_name', 'juegos.titulo as juego_titulo')
            ->where('cestas.activo', 1)
            ->where('users.id', '=', $id)
            ->get();

        return response()->json([
            "Info de la cesta" => $cestas,
        ]);
    }


    // hace falta crear una tabla que contenga el nombre de las empresas, ejemplo
    // 1. BEHAVIOR
    // 2. ROCKSTAR GAMES
    // 3. EPIC GAMES
    // 4. EA SPORTS
    // para que cuando se haga la consulta a la información del juego, busque deacuerdo al id que lleva el juego
    // juegos => id,titulo,precio, ID_EMPRESA, no se si aplicar una segunda consulta exclusiva para los carruseles Ó junto al getJuegoCompleto()... pendiente


    public function getCarruselDestacados() {}

    public function getDescuentos() {}



    // FUNCIONES PARA GESTIONAR LOS JUEGOS

    public function getCategoria($id){

        

    }


}
