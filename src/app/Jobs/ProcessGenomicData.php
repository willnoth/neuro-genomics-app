<?php

namespace App\Jobs;

use App\Models\GenomicJob;
use App\Models\OligodendrogliomaAnalysis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProcessGenomicData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Extend timeout execution window to 30 minutes for heavy sequencing files
    public $timeout = 1800;

    public function __construct(protected GenomicJob $jobRecord) {}

    public function handle(): void
    {
        $this->jobRecord->update(['status' => 'processing', 'progress_percent' => 10]);

        $absoluteFilePath = Storage::path($this->jobRecord->file_path);
        
        // Execute the background container Python script array parameters safely
        $result = Process::timeout($this->timeout)->run([
            'python3', 
            base_path('bioinformatics/oligodendroglioma_pipeline.py'), 
            '--input', $absoluteFilePath,
            '--job-id', $this->jobRecord->id
        ]);

        if ($result->successful()) {
            $terminalOutput = $result->output();

            // Extract the encapsulated clean JSON data blocks using regex
            if (preg_match('/---PIPELINE_OUTPUT_START---\s*(.*?)\s*---PIPELINE_OUTPUT_END---/s', $terminalOutput, $matches)) {
                $data = json_decode($matches[1], true);

                OligodendrogliomaAnalysis::create([
                    'genomic_job_id'          => $this->jobRecord->id,
                    'idh_status'              => $data['idh_status'],
                    'has_1p19q_codeletion'    => $data['has_1p19q_codeletion'],
                    'cic_mutation_status'     => $data['cic_mutation_status'],
                    'fubp1_mutation_status'   => $data['fubp1_mutation_status'],
                    'tert_promoter_mutation'  => $data['tert_promoter_mutation'],
                    'mgmt_methylation_status' => $data['mgmt_methylation_status'],
                ]);

                $this->jobRecord->update(['status' => 'completed', 'progress_percent' => 100]);
                return;
            }

            $this->jobRecord->update(['status' => 'failed', 'error_log' => 'Parsing failure: Signature markers missing from python stdout.']);
        } else {
            $this->jobRecord->update([
                'status' => 'failed',
                'error_log' => $result->errorOutput()
            ]);
        }
    }
}

