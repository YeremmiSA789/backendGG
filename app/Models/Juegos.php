<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Juegos extends Model
{
    use HasFactory;

    // Relación: muchos a muchos con cestas (usando una tabla pivote)
    // public function cestas()
    // {
    //     return $this->belongsToMany(Cesta::class, 'cesta_productos')
    //                 ->withPivot('cantidad', 'precio_unitario')
    //                 ->withTimestamps();
    // }


    public function categoria():BelongsTo{
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }


    // public function descuentos(){
    //     return $this->belongsTo(Descuentos::class, 'game_id');
    // }


}
