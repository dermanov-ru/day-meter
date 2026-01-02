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
        Schema::table('metric_values', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('value_int');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metric_values', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
