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
        Schema::create('clustering_runs', function (Blueprint $table) {
            $table->id();
            $table->string('period')->nullable();
            $table->unsignedInteger('total_data')->default(0);
            $table->unsignedTinyInteger('total_clusters')->default(3);
            $table->unsignedInteger('iterations')->default(0);
            $table->decimal('silhouette_score', 8, 6)->nullable();
            $table->json('attributes')->nullable();
            $table->json('iteration_history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clustering_runs');
    }
};
