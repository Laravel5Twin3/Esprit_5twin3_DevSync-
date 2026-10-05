<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorie_conseils', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('icone')->default('bi-lightbulb');
            $table->string('couleur')->default('#fd7e14');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorie_conseils');
    }
};
