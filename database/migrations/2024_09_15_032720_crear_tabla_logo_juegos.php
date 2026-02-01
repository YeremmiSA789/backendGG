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
        //

        Schema::create('logo_juegos', function (Blueprint $table) {
            $table->id();
            $table->string('ruta_logo');

            $table->unsignedBigInteger('juego_id')->nullable();
            $table->foreign('juego_id')->references('id')->on('juegos');

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
        Schema::dropIfExists('logo_juegos');
    }
};
