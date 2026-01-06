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
        Schema::create('cultural_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Type and temporal classification
            $table->enum('type', ['movie', 'book', 'theater', 'series', 'concert']);
            $table->enum('temporal_type', ['instant', 'duration'])->default('instant');
            
            // Basic info
            $table->string('title');
            $table->enum('format', ['cinema', 'streaming', 'paper', 'electronic', 'audio', 'offline']);
            
            // Temporal fields
            $table->dateTime('date_at')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            
            // Content fields
            $table->text('impressions')->nullable();
            $table->float('rating')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'temporal_type']);
            $table->index(['user_id', 'date_start']);
            $table->index(['user_id', 'date_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultural_activities');
    }
};
