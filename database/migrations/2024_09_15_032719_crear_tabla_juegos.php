<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->unique();
            $table->string('portada');
            $table->string('versionJuego')->nullable(); //Juego Base porDefecto

            $table->float('precio',5,2);
            $table->float('descuento',2,0)->nullable();
            $table->float('precioDescontado',5,2)->nullable();
            $table->text('Descripcion');

            $table->date('inicio_descuento')->nullable();
            $table->date('fin_descuento')->nullable();
            
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categorias');

            $table->unsignedBigInteger('logoJuego_id')->nullable();  // nullable para evitar problemas iniciales
            
            $table->string('colorFondo')->nullable();
            $table->string('plataforma')->nullable();
            $table->date('lanzamiento')->nullable();
            $table->integer('activo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('juegos');
    }
};
