<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logo_Juegos extends Model
{
    use HasFactory;

    public function juegos(){
        return $this->hasMany(Juegos::class, 'logoJuego_id');
    }
}
