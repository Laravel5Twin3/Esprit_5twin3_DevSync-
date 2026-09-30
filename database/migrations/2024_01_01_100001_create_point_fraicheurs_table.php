<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_fraicheurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_point_id')
                  ->constrained('categorie_points')
                  ->onDelete('cascade');
            $table->string('nom');
            $table->string('adresse');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('horaires')->nullable();    // ex: "08h00 - 20h00"
            $table->integer('capacite')->nullable();   // nombre de personnes
            $table->boolean('actif')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_fraicheurs');
    }
};
