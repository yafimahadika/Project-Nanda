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
        Schema::create('customer_satisfactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->unsignedTinyInteger('satisfaction_score');
            $table->unsignedInteger('customers_handled')->default(0);
            $table->unsignedInteger('complaints')->default(0);
            $table->unsignedInteger('complaints_resolved')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'assessment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_satisfactions');
    }
};
