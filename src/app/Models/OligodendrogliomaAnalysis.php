<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OligodendrogliomaAnalysis extends Model
{
    protected $table = 'oligodendroglioma_analyses';

    protected $fillable = [
        'genomic_job_id', 'idh_status', 'has_1p19q_codeletion', 
        'cic_mutation_status', 'fubp1_mutation_status', 
        'tert_promoter_mutation', 'mgmt_methylation_status'
    ];

    public function genomicJob(): BelongsTo
    {
        return $this->belongsTo(GenomicJob::class, 'genomic_job_id');
    }
}

