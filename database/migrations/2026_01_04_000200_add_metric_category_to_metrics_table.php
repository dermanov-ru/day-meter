<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MetricCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->foreignId('metric_category_id')->nullable()->constrained('metric_categories')->cascadeOnDelete();
        });

        // Get the "other" category id
        $otherCategory = DB::table('metric_categories')->where('key', 'other')->first();
        
        if ($otherCategory) {
            // Assign all existing metrics to the "other" category
            DB::table('metrics')->update([
                'metric_category_id' => $otherCategory->id,
            ]);
        }

        // Make the column NOT NULL after assigning defaults
        Schema::table('metrics', function (Blueprint $table) {
            $table->foreignId('metric_category_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->dropForeign(['metric_category_id']);
            $table->dropColumn('metric_category_id');
        });
    }
};
