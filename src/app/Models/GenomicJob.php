<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GenomicJob extends Model
{
    protected $fillable = ['patient_id', 'file_path', 'status', 'progress_percent', 'error_log'];

    /**
     * Get the biomarker analysis associated with this job run tracking key.
     */
    public function analysis(): HasOne
    {
        return $this->hasOne(OligodendrogliomaAnalysis::class, 'genomic_job_id');
    }
}

