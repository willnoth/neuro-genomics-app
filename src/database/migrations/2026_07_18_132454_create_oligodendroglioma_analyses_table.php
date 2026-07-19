<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('oligodendroglioma_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genomic_job_id')->constrained('genomic_jobs')->onDelete('cascade');
            $table->string('idh_status')->nullable();               // e.g., 'IDH1-R132H'
            $table->boolean('has_1p19q_codeletion')->default(false); // Crucial WHO hallmark
            $table->string('cic_mutation_status')->nullable();      // Chromosome 19q marker
            $table->string('fubp1_mutation_status')->nullable();    // Chromosome 1p marker
            $table->boolean('tert_promoter_mutation')->default(false);
            $table->string('mgmt_methylation_status')->nullable();  // Chemo sensitivity predictor
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('oligodendroglioma_analyses');
    }
};

