<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorie_points', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                          // parc, salle climatisée, fontaine
            $table->string('icone')->default('bi-geo-alt'); // Bootstrap Icons
            $table->string('couleur')->default('#0d6efd');  // couleur du badge
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorie_points');
    }
};
