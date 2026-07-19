<?php

use App\Http\Controllers\GenomicPipelineController;
use Illuminate\Support\Facades\Route;

// Browser View Controls
Route::get('/dashboard', [GenomicPipelineController::class, 'index'])->name('dashboard');
Route::post('/pipeline/submit', [GenomicPipelineController::class, 'submit'])->name('pipeline.submit');
Route::get('/report/{id}', [GenomicPipelineController::class, 'showReport'])->name('report.show');
Route::get('/api/pipeline/archives', [GenomicPipelineController::class, 'archives']);

// Background AJAX Status & Python Ingestion Routing Checkpoints
Route::get('/api/pipeline/status/{id}', [GenomicPipelineController::class, 'checkStatus']);
Route::post('/api/internal/genomic-job/{id}/progress', [GenomicPipelineController::class, 'updateInternalProgress']);

