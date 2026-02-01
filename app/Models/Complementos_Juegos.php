<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complementos_Juegos extends Model
{
    use HasFactory;

    
    public function categoria():BelongsTo{
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }

    public function juegoBase():BelongsTo{
        return $this->belongsTo(Juegos::class, 'juego_id');
    }

    public function logo():BelongsTo{
        return $this->belongsTo(Logo_Juegos::class, 'logoJuego_id');
    }

    
}
