<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupures', function (Blueprint $table) {
            $table->id();
            // Une zone ne peut pas être supprimée tant qu'elle a des coupures
            $table->foreignId('zone_id')->constrained('zones')->restrictOnDelete();
            $table->string('titre', 150);
            $table->enum('type', ['delestage', 'panne', 'maintenance', 'surcharge']);
            $table->enum('statut', ['prevue', 'en_cours', 'resolue', 'annulee'])->default('prevue');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin')->nullable();
            $table->unsignedInteger('foyers_touches')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['statut', 'date_debut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupures');
    }
};
