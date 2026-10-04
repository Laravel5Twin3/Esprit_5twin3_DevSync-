<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('help_responses', function (Blueprint $table) {
                $table->id();

                $table->foreignId('help_request_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->text('message')->nullable();

                $table->enum('status', [
                    'pending',
                    'accepted',
                    'rejected',
                    'cancelled',
                    'completed',
                ])->default('pending');

                $table->timestamps();

                $table->unique([
                    'help_request_id',
                    'user_id',
                ]);
            });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_responses');
    }
};
