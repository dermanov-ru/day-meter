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
        Schema::create('day_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('day_entry_id')->constrained('day_entries')->onDelete('cascade');
            $table->string('file_path'); // Path to stored file (e.g., photos/1/2026-01-06/timestamp_filename.jpg)
            $table->text('comment')->nullable();
            $table->integer('sort_order')->default(0); // To maintain insertion order
            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'day_entry_id']);
            $table->index('day_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_photos');
    }
};
