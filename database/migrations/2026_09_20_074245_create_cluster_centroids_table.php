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
        Schema::create('cluster_centroids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clustering_run_id')->constrained('clustering_runs')->cascadeOnDelete();
            $table->unsignedTinyInteger('cluster_number');
            $table->string('cluster_label')->nullable();
            $table->json('values');
            $table->timestamps();

            $table->unique(['clustering_run_id', 'cluster_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_centroids');
    }
};
