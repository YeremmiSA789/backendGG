<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cesta extends Model
{
    use HasFactory;
    protected $table = 'cesta'; // Cambia 'nombre_real_de_la_tabla' por el nombre correcto

    // Relación: una cesta pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // Relación: una cesta pertenece a un juego
    public function juego()
    {
        return $this->belongsTo(Juegos::class, 'juego_id');
    }
}
