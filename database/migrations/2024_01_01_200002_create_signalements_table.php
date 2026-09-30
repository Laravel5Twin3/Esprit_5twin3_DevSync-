<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // habitant qui signale
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();       // zone concernée
            $table->foreignId('coupure_id')->nullable()->constrained()->nullOnDelete(); // coupure rattachée après validation
            $table->dateTime('date_constat');
            $table->text('description');
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->timestamps();

            $table->index(['statut', 'zone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};
