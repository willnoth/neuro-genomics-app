<!-- src/resources/views/genomics/report.blade.php -->
<!DOCTYPE html>
<html lang="en">
<!-- src/resources/views/genomics/report.blade.php [REPLACE HEAD SECTION ONLY] -->
<!-- src/resources/views/genomics/report.blade.php [REPLACE HEAD SECTION ONLY] -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrated Molecular Diagnostics Report</title>
    
    <!-- ─── CORRECT VITE DIRECTIVE FOR MODERN LARAVEL ─── -->
    @vite(['resources/css/app.css'])
    
    <style>
        @media print {
            body { background-color: #ffffff !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #e2e8f0 !important; border-radius: 12px !important; }
            .print-card { box-shadow: none !important; border: none !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased py-12 px-6">

    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden print-card">
        <!-- Report Header -->
        <div class="bg-slate-900 px-8 py-6 text-white flex justify-between items-center border-b border-slate-800">
            <div>
                <h1 class="text-base font-bold tracking-tight text-white uppercase">Integrated Molecular Diagnosis</h1>
                <p class="text-[10px] tracking-widest text-slate-400 font-mono uppercase -mt-0.5">Neuro-Genomics Case Registry</p>
            </div>
            <button onclick="window.print()" class="no-print bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider py-2.5 px-4 rounded-xl shadow transition active:scale-95">
                Export Vector PDF
            </button>
        </div>

        <div class="p-8 space-y-8">
            <!-- WHO Classification Logic Alert Banner -->
            @if($analysis->idh_status !== 'Wildtype' && $analysis->has_1p19q_codeletion)
                <div class="p-5 bg-emerald-50 border border-emerald-200 text-emerald-800 border-l-4 border-l-emerald-500 rounded-2xl print-border">
                    <h2 class="text-sm font-bold uppercase tracking-wide">Diagnosis Secured: Oligodendroglioma</h2>
                    <p class="text-xs text-emerald-700 mt-1 leading-relaxed">Somatic variant mapping confirms <strong>IDH-mutant</strong> profile coupled with co-deletion of the <strong>1p/19q</strong> chromosomal arms.</p>
                </div>
            @else
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 border-l-4 border-l-amber-500 rounded-2xl print-border">
                    <h2 class="text-sm font-bold uppercase tracking-wide">Diagnostic Alert: Alternative Lineage Indicated</h2>
                    <p class="text-xs text-amber-700 mt-1 leading-relaxed">Failed to meet the joint criteria for oligodendroglioma due to an intact 1p/19q chromosome state.</p>
                </div>
            @endif

            <!-- Table Block -->
            <div class="space-y-3">
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Primary Pathognomonic Biomarkers</h3>
                <div class="border border-slate-200 rounded-2xl overflow-hidden print-border">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <tr><th class="px-6 py-4">Diagnostic Locus</th><th class="px-6 py-4">State</th><th class="px-6 py-4">Detected Genome Signature</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-600">
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-900">IDH Mutation</td>
                                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $analysis->idh_status !== 'Wildtype' ? 'bg-rose-50 text-rose-700' : 'bg-slate-50' }}">{{ $analysis->idh_status !== 'Wildtype' ? 'MUTATED' : 'WILDTYPE' }}</span></td>
                                <td class="px-6 py-4 font-mono text-slate-500">{{ $analysis->idh_status }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-900">1p/19q Chromosomes</td>
                                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $analysis->has_1p19q_codeletion ? 'bg-rose-50 text-rose-700' : 'bg-slate-50' }}">{{ $analysis->has_1p19q_codeletion ? 'DE_LOST' : 'INTACT' }}</span></td>
                                <td class="px-6 py-4 font-mono text-slate-500">{{ $analysis->has_1p19q_codeletion ? 'Dual loss observed (Log2 copy ratio ~ -1.0)' : 'No macro deletions flagged' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grid Block -->
            <div class="space-y-3">
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Secondary Co-occurring Modifiers</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl print-border"><span class="text-[10px] text-slate-400 font-bold uppercase block">CIC Gene (Chr 19q)</span><span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $analysis->cic_mutation_status }}</span></div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl print-border"><span class="text-[10px] text-slate-400 font-bold uppercase block">FUBP1 Gene (Chr 1p)</span><span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $analysis->fubp1_mutation_status }}</span></div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl print-border"><span class="text-[10px] text-slate-400 font-bold uppercase block">TERT Promoter State</span><span class="text-xs font-bold font-mono mt-1 block">{{ $analysis->tert_promoter_mutation ? 'MUTANT' : 'WILDTYPE' }}</span></div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl print-border"><span class="text-[10px] text-slate-400 font-bold uppercase block">MGMT Methylation</span><span class="text-xs font-bold font-mono mt-1 block text-emerald-600">{{ strtoupper($analysis->mgmt_methylation_status) }}</span></div>
                </div>
            </div>

            <div class="pt-4 text-center no-print">
                <a href="{{ route('dashboard') }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider py-3.5 px-8 rounded-xl transition">
                    &larr; Return to Core Console
                </a>
            </div>
        </div>
    </div>

</body>
</html>

