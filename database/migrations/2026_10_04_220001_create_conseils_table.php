<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conseils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_conseil_id')
                  ->constrained('categorie_conseils')
                  ->onDelete('cascade');
            $table->string('titre');
            $table->string('resume')->nullable();
            $table->text('contenu');
            $table->string('public_cible')->default('tous'); // tous | personnes_agees | enfants | malades_chroniques
            $table->string('priorite')->default('info');     // info | important | urgent
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conseils');
    }
};
