<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerte_meteos', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->decimal('temperature_max', 4, 1);
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->text('message');
            // Relation 1-N : un niveau de vigilance a plusieurs alertes
            $table->foreignId('niveau_vigilance_id')->constrained('niveau_vigilances')->restrictOnDelete();
            // Zone concernée (table créée par le module Coupures)
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['date_debut', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerte_meteos');
    }
};
