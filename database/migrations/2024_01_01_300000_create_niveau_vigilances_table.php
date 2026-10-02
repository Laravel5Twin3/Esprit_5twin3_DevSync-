<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Niveaux de vigilance canicule (Vert, Jaune, Orange, Rouge).
     * Chaque niveau couvre une plage de température : utilisée pour déterminer
     * automatiquement le niveau d'une alerte.
     */
    public function up(): void
    {
        Schema::create('niveau_vigilances', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50)->unique();
            $table->string('couleur', 7);                       // #RRGGBB
            $table->decimal('temperature_min', 4, 1);           // seuil bas inclus
            $table->decimal('temperature_max', 4, 1)->nullable(); // null = pas de limite haute
            $table->text('consigne');
            $table->unsignedTinyInteger('ordre')->default(1);   // 1 = le moins grave
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveau_vigilances');
    }
};
