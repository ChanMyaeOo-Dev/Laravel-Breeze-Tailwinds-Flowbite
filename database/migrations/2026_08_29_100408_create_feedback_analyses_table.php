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
        Schema::create('feedback_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained()->onDelete('cascade');
            $table->string('sentiment'); // positive, negative, neutral
            $table->float('confidence')->nullable();
            $table->json('probabilities')->nullable();
            $table->json('categories')->nullable(); // still can be rule-based
            $table->json('keywords')->nullable();
            $table->text('summary')->nullable();
            $table->string('model_version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_analyses');
    }
};
