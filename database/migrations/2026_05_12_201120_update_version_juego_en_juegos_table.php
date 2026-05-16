<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::table('juegos')->update([
            'versionJuego' => DB::raw("
            CASE
                WHEN versionJuego = 'JUEGO BASE' THEN 0
                WHEN versionJuego = 'DLC' THEN 1
                ELSE NULL
            END
            ")
        ]);

        // 2. Cambiar tipo de columna
        DB::statement("ALTER TABLE juegos MODIFY versionJuego INT");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // revertir a string
        DB::table('users')->update([
            'tipo_juego' => DB::raw("
                CASE
                    WHEN versionJuego = 1 THEN 'JUEGO BASE',
                    WHEN versionJuego = 2 THEN 'DLC',
                    ELSE NULL
                END
            ")
        ]);
    }
};
