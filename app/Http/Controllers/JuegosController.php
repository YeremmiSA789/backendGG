<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cesta;
use App\Models\Galeria_Juegos;
use App\Models\Juegos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $infoGaleria = Galeria_Juegos::select('id', 'ruta_img', 'activo')->where('juego_id', '=', $id)->where('activo', '=', 1)->get();

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


    public function desactivarCesta($id)
    {
        $productoEncontrado = Cesta::find($id);

        if ($productoEncontrado) {
            // Modifica el campo 'activo' y guarda los cambios
            $productoEncontrado->activo = 0;
            $productoEncontrado->save();

            return response()->json("Producto desactivado");
        } else {
            return response()->json('producto no encontrado', 200); //colocar el 200 aunque no se encuentre el elemento, la función ha hecho su trabajo y no ha ocurrido algún error
        }
    }


    public function revisarCesta($id)
    {

        $bandera = false;


        // Consulta los productos en la cesta que están activos para este usuario
        $consulta = Cesta::select("juego_id", 'id')
            ->where("users_id", '=', $id)
            ->where('activo', '=', 1)
            ->get();


        if ($consulta->isNotEmpty()) {
            $bandera = !$bandera;

            // return response()->json($bandera);
            return response()->json([
                "ok" => $bandera,
                "Juegos_ID" => $consulta
            ], 200);
        } else {
            return response()->json($bandera, 200);
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

    public function getCategoria($id) {}

    public function Post_registroJuego(Request $peticion)
    {

        \Log::info($peticion->all());

        $peticion->validate([
            "titulo" => 'required|min:3',
            // "portada" => 'required|string',

            // 👇 portada
            // 'portada' => 'required|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'portada' => 'required|image|mimes:jpg,jpeg,png,webp,avif',
            // "versionJuego" => '';
            "categoria_id" => 'required|integer',
            "precio" => 'required|numeric|min:1',
            "precioDescontado" => 'nullable|numeric',
            "versionJuego" => 'nullable|string',
            "descuento" => 'nullable|numeric',
            "inicio_descuento" => 'nullable|date',
            "fin_descuento" => 'nullable|date',
            "Descripcion" => 'nullable|string',

            "colorFondo" => 'string',
            "plataforma" => 'string',
            "lanzamiento" => 'nullable|date',

            // 'logo' => 'required|image|mimes:jpg,jpeg,png,svg,webp,avif|max:1024',
            // 'logo' => 'required|mimes:jpg,jpeg,png,webp,avif|max:2048',
            // 'logo' => 'mimes:jpg,jpeg,png,webp,avif|max:2048',
            'logo' => 'mimes:jpg,jpeg,png,webp,avif',


            // 👇 galería
            'imagenes' => 'required|array|min:1|max:10',
            // 'imagenes.*' => 'required|string'
            // 'imagenes.*' => 'image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,webp,avif',



        ]);


        DB::beginTransaction();

        try {


            // 🖼️ Guardar portada
            $rutaPortada = $peticion->file('portada')->store('portadas', 'public');

            // guardar logo
            $rutaLogo = $peticion->file('logo')->store('logos', 'public');


            // 🎮 Crear juego
            $juego = Juegos::create([

                'titulo' => $peticion->titulo,
                'categoria_id' => $peticion->categoria_id,
                // 'portada' => $peticion->portada,
                'portada' => $rutaPortada,
                'precio' => $peticion->precio,
                'descuento' => $peticion->descuento,
                'precioDescontado' => $peticion->precioDescontado,
                'versionJuego' => $peticion->versionJuego,
                'inicio_descuento' => $peticion->inicio_descuento,
                'fin_descuento' => $peticion->fin_descuento,
                'Descripcion' => $peticion->Descripcion,
                'colorFondo' => $peticion->colorFondo,
                'plataforma' => $peticion->plataforma,
                'lanzamiento' => $peticion->lanzamiento,
                'activo' => 1
            ]);

            // aquí en lugar de usar el "Galeria_Juegos() para guardar, se usa la funcion escrita en el modelo de
            // Juegos.php, entrando primeramente a la variable declarada $juego
            // Esto se hace atraves de la id que se coloca en la tabla Galeria_Juegos para conectar la tabla juegos 
            // foreach ($peticion->imagenes as $ruta) {
            //     $juego->galeria()->create([
            //         'ruta_img' => $ruta,
            //         'activo' => 1,
            //     ]);
            // }

            // Guardar la ruta en la tabla Logo_juegos
            $juego->logo()->create([
                'ruta_logo' => $rutaLogo,
                'activo' => 1
            ]);


            // 2️⃣ Guardar imágenes (máx 10)
            foreach ($peticion->file('imagenes') as $imagen) {
                $ruta = $imagen->store('juegos', 'public');

                $juego->galeria()->create([
                    'ruta_img' => $ruta,
                    'activo' => 1
                ]);
            }


            DB::commit();

            return response()->json([
                'message' => 'Juego registrado correctamente',
                'Juego' => $juego->load('galeria'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();


            return response()->json([
                'error' => 'Error al registrarse el juego',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }



    public function getBuscador(Request $peticion)
    {

        $solicitud = $peticion->query('buscador');
        // en angular
        // crear una funcionalidad que habilite la busqueda solo cuando haya algo escrito
        // mientras esté vacío el campo, que no se active... yo que se

        $resultado = Juegos::select(
            'id',
            'titulo',
            'portada',
            'precio',
            'descuento',
            'precioDescontado'
        )
            ->where('titulo', 'LIKE', '%' . $solicitud . '%')
            ->get();


        return response()->json($resultado);
    }


    public function desactivarGaleria($id)
    {

        $imagen =  Galeria_Juegos::find($id);

        if (!$imagen) {
            return response()->json(["Error" => "Image no encontrada"], 404);
        }

        $imagen->activo = 0;
        $imagen->save();

        return response()->json(["Message" => "Imagen desactivada"]);
    }



    public function put_ActualizarJuego(Request $request, $id)
    {

        // dd($request->all());
        // PARA detectar fallos en las peticiones,  POST, PUT, DELETE, SELECT. ESAS BAINAS 
        \Log::info($request->all());

        $juego = Juegos::find($id);

        if (!$juego) {
            return response()->json(['Error' => 'Juego no encontrado'], 404);
        }

        $juego->titulo = $request->titulo;
        $juego->categoria_id = $request->categoria_id;
        $juego->precio = $request->precio;
        $juego->descuento = $request->descuento;
        $juego->descripcion = $request->descripcion ?: null;
        $juego->versionJuego = $request->versionJuego ?: null;
        $juego->inicio_descuento = $request->inicio_descuento ?: null;
        $juego->fin_descuento = $request->fin_descuento ?: null;
        $juego->colorFondo = $request->colorFondo ?: '#000000';


        // 🔹 Actualizar portada (si viene)
        if ($request->hasFile('portada')) {
            // path o ruta
            $ruta = $request->File('portada')->store('portadas', 'public');
            $juego->portada = $ruta;
        }

        // 🔹 Actualizar logo (si viene)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');

            // Si tienes relación con otra tabla, aquí cambiaría
            $juego->ruta_logo = $path;
        }

        $juego->save();

        return response()->json([
            'mensaje' => 'Juego actualizado correctamente',
            'data' => $juego
        ]);


    }
}
