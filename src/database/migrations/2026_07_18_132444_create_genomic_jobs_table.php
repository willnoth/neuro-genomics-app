<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('genomic_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->index(); // Bound directly to one patient
            $table->string('file_path');
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->integer('progress_percent')->default(0);
            $table->text('error_log')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('genomic_jobs');
    }
};

