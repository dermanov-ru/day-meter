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
        Schema::create('disease_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained('diseases')->cascadeOnDelete();
            $table->dateTime('datetime');
            $table->enum('type', ['symptom', 'treatment', 'condition', 'medication', 'doctor', 'free'])->default('free');
            $table->text('text')->nullable();
            $table->float('temperature')->nullable();
            $table->tinyInteger('pain_level')->nullable()->min(0)->max(10);
            $table->enum('state_flag', ['worse', 'better'])->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['disease_id', 'datetime']);
            $table->index(['disease_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_notes');
    }
};
