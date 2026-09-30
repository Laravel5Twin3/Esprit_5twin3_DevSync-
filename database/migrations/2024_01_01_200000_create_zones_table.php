<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('gouvernorat', 50);
            $table->string('code_postal', 4);
            $table->unsignedInteger('population')->nullable();
            // Coordonnées utilisées pour récupérer la météo de la zone (prédiction IA)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('niveau_risque', ['faible', 'moyen', 'eleve'])->default('moyen');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
