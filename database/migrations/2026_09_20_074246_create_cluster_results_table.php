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
        Schema::create('cluster_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clustering_run_id')->constrained('clustering_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('cluster_number');
            $table->string('cluster_label')->nullable();
            $table->decimal('distance', 12, 6)->nullable();
            $table->decimal('silhouette_score', 8, 6)->nullable();
            $table->json('attribute_values')->nullable();
            $table->json('normalized_values')->nullable();
            $table->timestamps();

            $table->unique(['clustering_run_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_results');
    }
};
