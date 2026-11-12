<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_teeth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('tooth_number');
            $table->enum('status', ['healthy', 'treated', 'planned', 'missing'])
                ->default('healthy');
            $table->string('treatment_type')->nullable();
            $table->text('notes')->nullable();
            $table->date('treated_at')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'tooth_number']);
            $table->index(['patient_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_teeth');
    }
};
