<?php

namespace App\Http\Controllers;

use App\Models\GenomicJob;
use App\Models\OligodendrogliomaAnalysis;
use App\Jobs\ProcessGenomicData;
use Illuminate\Http\Request;

class GenomicPipelineController extends Controller
{
    public function index()
    {
        $job = GenomicJob::latest()->first();
        $historicalJobs = GenomicJob::with('analysis')->orderBy('created_at', 'desc')->paginate(10);

        if (!$job) {
            $job = new GenomicJob(['id' => 1, 'progress_percent' => 0, 'status' => 'queued', 'patient_id' => '—']);
        }

        return view('genomics.dashboard', compact('job', 'historicalJobs'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|string',
            'genomic_file' => 'required|file|max:512000', // 500MB maximum ceiling path
        ]);

        $path = $request->file('genomic_file')->store('genomics/inputs');

        $jobRecord = GenomicJob::create([
            'patient_id' => $request->patient_id,
            'file_path' => $path,
            'status' => 'queued',
            'progress_percent' => 0
        ]);

        ProcessGenomicData::dispatch($jobRecord);

        // Hand over the execution token immediately if the request uses AJAX
        if ($request->has('is_ajax') || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'job_id' => $jobRecord->id
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function showReport($id)
    {
        $analysis = OligodendrogliomaAnalysis::where('genomic_job_id', $id)->firstOrFail();
        return view('genomics.report', compact('analysis'));
    }

    public function checkStatus($id)
    {
        $job = GenomicJob::findOrFail($id);
        $analysis = OligodendrogliomaAnalysis::where('genomic_job_id', $id)->first();

        return response()->json([
            'status' => $job->status,
            'progress_percent' => $job->progress_percent,
            'has_analysis' => !is_null($analysis),
            'analysis' => $analysis
        ]);
    }

    public function archives()
    {
        $historicalJobs = GenomicJob::with('analysis')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'html' => view('genomics.catalog', compact('historicalJobs'))->render()
        ]);
    }

    public function updateInternalProgress(Request $request, $id)
    {
        if ($request->header('X-Internal-Secret') !== env('GENOMICS_INTERNAL_SECRET')) {
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }

        $request->validate(['progress' => 'required|integer|min:0|max:100']);
        
        $job = GenomicJob::findOrFail($id);
        $job->update(['progress_percent' => $request->progress]);

        return response()->json(['status' => 'updated']);
    }
}

