<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Juegos extends Model
{
    use HasFactory;

    protected $fillable = [
        // IMPORTANTE, poner aquí todos los campos de la tabla, sin ellos, aunque le ponga en registrar
        // en la función no se van a quedar en la tabla por no ponerlos aquí
        'titulo',
        'portada',
        'categoria_id',
        'precio',
        'descuento',
        'precioDescontado',
        'versionJuego',
        'inicio_descuento',
        'fin_descuento',
        'Descripcion',
        'colorFondo',
        'plataforma',
        'lanzamiento',
        'activo'
    ];

    // Relación: muchos a muchos con cestas (usando una tabla pivote)
    // public function cestas()
    // {
    //     return $this->belongsToMany(Cesta::class, 'cesta_productos')
    //                 ->withPivot('cantidad', 'precio_unitario')
    //                 ->withTimestamps();
    // }

    public function galeria()
    {
        return $this->hasMany(Galeria_Juegos::class, 'juego_id');
    }


    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }

    public function logo()
    {
        return $this->hasOne(Logo_Juegos::class, 'juego_id');
    }


    // public function descuentos(){
    //     return $this->belongsTo(Descuentos::class, 'game_id');
    // }

    public function getVersionJuegoTextoAttribute()
    {
        return match ((int) $this->versionJuego) {
            0 => 'JUEGO BASE',
            1 => 'DLC',
            default => 'DESCONOCIDO',
        };
    }

    protected $appends = [
        'version_juego_texto'
    ];
}
