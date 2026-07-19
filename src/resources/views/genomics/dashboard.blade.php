<!-- src/resources/views/genomics/dashboard.blade.php [PART 1] -->
<!DOCTYPE html>
<html lang="en">
<!-- src/resources/views/genomics/dashboard.blade.php [REPLACE HEAD SECTION ONLY] -->
<!-- src/resources/views/genomics/dashboard.blade.php [REPLACE HEAD SECTION ONLY] -->
<!-- src/resources/views/genomics/dashboard.blade.php [REPLACE HEAD BLOCK ONLY] -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuro-Genomics Processing Hub</title>
    
    <!-- Vite compiles and injects both your Tailwind styles and global Chart.js definitions -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Standalone Vue 3 framework engine -->
    <script src="https://cloudflare.com"></script>
</head>
<!-- src/resources/views/genomics/dashboard.blade.php [REPLACE BODY AND CONTENT] -->
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Initialize the Vue Mounting Container -->
    <div id="vue-app">

        <!-- Top Diagnostic Control Panel Navigation Bar -->
        <header class="bg-slate-900 border-b border-slate-800 text-white sticky top-0 z-50 shadow-sm backdrop-blur-md bg-opacity-95">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="h-9 w-9 bg-gradient-to-tr from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-500/20">
                        <span class="text-sm font-bold text-white tracking-tighter">NG</span>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold tracking-tight text-white uppercase">Neuro-Genomics Core</h1>
                        <p class="text-[10px] tracking-widest text-slate-400 font-mono uppercase -mt-0.5">Automated Diagnostics Pipeline</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6 text-[11px] font-mono text-slate-400">
                    <div class="flex items-center space-x-2 bg-slate-800/60 px-3 py-1.5 rounded-lg border border-slate-700/50">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Node: Localhost-8000</span>
                    </div>
                    <span>User: Dr. Evans</span>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Interactive Submission Form Console -->
            <aside class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="mb-5">
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight">Sequence Ingestion Console</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Submit somatic tissue tumor variants for analysis</p>
                    </div>
                    
                    <form @submit.prevent="submitDataset" action="{{ route('pipeline.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Patient Matrix Identifier</label>
                            <input type="text" name="patient_id" required placeholder="e.g., PATIENT-2026-X8" 
                                   class="w-full font-mono text-xs rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white focus:outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Genomic Variant Dataset (.VCF)</label>
                            <label class="group flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-xl cursor-pointer bg-slate-50/50 hover:bg-indigo-50/20 transition-all">
                                <div class="flex flex-col items-center justify-center p-5 text-center">
                                    <span class="text-slate-400 text-sm font-semibold">📂 Browse Local Storage</span>
                                    <p class="text-[10px] font-mono text-slate-400 mt-1" v-text="fileName"></p>
                                </div>
                                <input type="file" name="genomic_file" required class="hidden" @change="onFileSelected">
                            </label>
                        </div>

                        <button type="submit" :disabled="isProcessing" v-text="buttonText"
                                class="w-full bg-slate-900 hover:bg-slate-800 disabled:bg-slate-400 text-white font-bold text-xs uppercase tracking-wider py-4 px-4 rounded-xl transition-all shadow-md shadow-slate-900/10 active:scale-[0.99]"></button>
                    </form>
                </div>
            </aside>

            <!-- Right Column: Live Trackers and Dynamic Reports -->
            <section class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Real-time Worker Engine Processing Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Active Computational Monitor</h3>
                                <p class="text-[10px] font-mono text-slate-400">Process Token: #<span class="font-bold text-slate-700" v-text="jobId"></span></p>
                            </div>
                            <span class="px-3 py-1 text-[10px] font-mono font-bold uppercase tracking-wider rounded-lg border shadow-sm"
                                  :class="statusBadgeClass" v-text="statusText"></span>
                        </div>

                        <div class="w-full bg-slate-100 rounded-xl h-7 overflow-hidden my-3 relative p-1 border border-slate-200/40">
                            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600 h-full rounded-lg flex items-center justify-center transition-all duration-500 ease-out shadow-inner"
                                 :style="{ width: progressPercent + '%' }">
                                 <span class="text-[10px] font-mono font-bold text-white tracking-widest drop-shadow-sm" v-text="progressPercent + '%'"></span>
                            </div>
                        </div>
                        <div class="flex items-start space-x-2 bg-slate-50 border border-slate-200/60 rounded-xl p-3">
                            <span class="text-xs font-mono text-indigo-500" v-text="monitorIcon"></span>
                            <p class="text-xs font-medium text-slate-500 italic leading-relaxed" v-text="pipelineMessage"></p>
                        </div>
                    </div>

                    <!-- Interactive Multi-Omics Chart Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Molecular Co-clustering Map</h3>
                        <div class="h-36 relative">
                            <canvas id="multiOmicsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Summary Report Component -->
                <div class="transform transition-all duration-500 ease-out bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden"
                     v-if="showReportCard">
                    <div class="bg-slate-900 px-6 py-4 text-white">
                        <h3 class="text-sm font-bold tracking-tight">Molecular Classification Analysis</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="p-4 rounded-xl text-xs font-bold border shadow-sm" :class="alertBoxClass" v-html="alertBoxText"></div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IDH Loci Mutation</span>
                                <span class="text-xs font-mono font-bold text-slate-900 mt-1 block" v-text="analysisResults.idh_status"></span>
                            </div>
                            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">1p/19q Chromosome</span>
                                <span class="text-xs font-mono font-bold mt-1 block" :class="analysisResults.has_1p19q_codeletion ? 'text-rose-600' : 'text-slate-400'" v-text="analysisResults.has_1p19q_codeletion ? 'CO-DELETED' : 'INTACT'"></span>
                            </div>
                            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MGMT Epigenetics</span>
                                <span class="text-xs font-mono font-bold text-slate-900 mt-1 block" v-text="analysisResults.mgmt_methylation_status"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    <section id="archive-section" class="max-w-7xl mx-auto px-6 pb-24">
        @include('genomics.catalog')
    </section>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- ─── WRAP EVERYTHING INSIDE THIS LOAD SANITIZER ─── -->
    <script>
        window.addEventListener('load', () => {
            console.log("[Genomics Engine] Chart.js fully present in RAM. Instantiating components.");

            // 1. Initialize the global chart instance safely
            const chartCanvas = document.getElementById('multiOmicsChart');
            if (chartCanvas && typeof Chart !== 'undefined') {
                const ctx = chartCanvas.getContext('2d');
                window.multiOmicsChart = new Chart(ctx, {
                    type: 'scatter',
                    data: {
                        datasets: [
                            {
                                label: 'Oligodendroglioma',
                                data: [],
                                backgroundColor: '#10b981',
                                borderColor: '#047857',
                                pointRadius: 8,
                                pointHoverRadius: 12,
                                showLine: false
                            },
                            {
                                label: 'Alternative Glioma',
                                data: [],
                                backgroundColor: '#f59e0b',
                                borderColor: '#b45309',
                                pointRadius: 8,
                                pointHoverRadius: 12,
                                showLine: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: { display: true, text: '1p/19q State (0=Intact, 1=Deleted)', font: { size: 9 } },
                                min: -0.2,
                                max: 1.2,
                                ticks: { stepSize: 1 }
                            },
                            y: {
                                title: { display: true, text: 'IDH Mutation (0=Normal, 1=Mutant)', font: { size: 9 } },
                                min: -0.2,
                                max: 1.2,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { display: true, labels: { boxWidth: 10, padding: 12 } }
                        }
                    }
                });
            }

            // 2. Initialize and mount your Vue 3 Instance safely
            const { createApp } = Vue;

            createApp({
                data() {
                    return {
                        fileName: 'Variant Call Format up to 500MB',
                        buttonText: 'Execute Diagnostic Run',
                        isProcessing: false,
                        jobId: {{ $job->id }},
                        progressPercent: {{ $job->progress_percent }},
                        statusText: "{{ $job->status }}",
                        pipelineMessage: 'System standing by. Deploy a somatic VCF profile inside the left panel to engage automated background computing cores.',
                        monitorIcon: '📡',
                        showReportCard: false,
                        alertBoxClass: '',
                        alertBoxText: '',
                        analysisResults: { idh_status: '—', has_1p19q_codeletion: false, mgmt_methylation_status: '—' },
                        pollInterval: null
                    }
                },
                computed: {
                    statusBadgeClass() {
                        if (this.statusText === 'completed') return 'bg-emerald-50 border-emerald-200 text-emerald-700';
                        if (this.statusText === 'processing' || this.statusText === 'uploading') return 'bg-blue-50 border-blue-200 text-blue-700 animate-pulse';
                        if (this.statusText === 'failed') return 'bg-rose-50 border-rose-200 text-rose-700';
                        return 'bg-slate-50 border-slate-200 text-slate-500';
                    }
                },
                methods: {
                    onFileSelected(event) {
                        const file = event.target.files[0];
                        this.fileName = file ? file.name : 'Variant Call Format up to 500MB';
                    },
                    submitDataset(event) {
                        this.isProcessing = true;
                        this.buttonText = "PROCESSING SEQUENCE ARRAYS...";
                        this.statusText = "uploading";
                        this.pipelineMessage = "Uploading genomic dataset to core compute nodes...";

                        const formData = new FormData(event.target);
                        formData.append('is_ajax', '1');

                        fetch(event.target.action, {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        })
                        .then(res => { if (!res.ok) throw new Error(); return res.json(); })
                        .then(jobData => {
                            this.jobId = jobData.job_id;
                            this.statusText = "queued";
                            this.refreshArchiveCatalog();
                            this.startPolling(jobData.job_id);
                        })
                        .catch(() => {
                            this.resetConsole();
                            this.statusText = "failed";
                            this.pipelineMessage = "Upload boundary failure. Check resource memory limits.";
                        });
                    },
                    startPolling(jobId) {
                        this.pipelineMessage = "Analyzing somatic structures via Python background kernels...";
                        this.monitorIcon = "🧬";

                        this.pollInterval = setInterval(() => {
                            fetch(`${window.location.origin}/api/pipeline/status/${jobId}`)
                                .then(res => res.json())
                                .then(data => {
                                    this.progressPercent = data.progress_percent;
                                    this.statusText = data.status;

                                    if (data.status === 'completed' && data.has_analysis) {
                                        clearInterval(this.pollInterval);
                                        this.displayReport(data.analysis);
                                        this.refreshArchiveCatalog();
                                    }
                                    if (data.status === 'failed') {
                                        clearInterval(this.pollInterval);
                                        this.resetConsole();
                                        this.statusText = "failed";
                                        this.pipelineMessage = "Bioinformatics process run encountered a terminal failure.";
                                    }
                                })
                                .catch(() => clearInterval(this.pollInterval));
                        }, 2000);
                    },
                    refreshArchiveCatalog() {
                        fetch(`${window.location.origin}/api/pipeline/archives`)
                            .then(res => res.json())
                            .then(data => {
                                const archiveSection = document.getElementById('archive-section');
                                if (archiveSection && data.html) {
                                    archiveSection.innerHTML = data.html;
                                }
                            })
                            .catch(() => {
                                // Keep current archive view if refresh fails
                            });
                    },
                    displayReport(analysis) {
                        this.statusText = "completed";
                        this.pipelineMessage = "Analysis matrix successfully concluded. Outputs cataloged to relational database profiles.";
                        this.monitorIcon = "✅";
                        this.analysisResults = analysis;

                        const hasIdhMutation = analysis.idh_status.indexOf('Wildtype') === -1;
                        const hasCodeletion = analysis.has_1p19q_codeletion === 1 || analysis.has_1p19q_codeletion === true;

                        if (hasIdhMutation && hasCodeletion) {
                            this.alertBoxClass = "bg-emerald-50 border-emerald-200 text-emerald-800 border-l-4 border-l-emerald-500 shadow-sm";
                            this.alertBoxText = "🎯 DIAGNOSIS SECURED: Oligodendroglioma Lineage Confirmed via WHO Biomarker Panel.";
                        } else {
                            this.alertBoxClass = "bg-amber-50 border-amber-200 text-amber-800 border-l-4 border-l-amber-500 shadow-sm";
                            this.alertBoxText = "⚠️ CLINICAL NOTICE: Alternative Glioma Profile Noted (1p/19q Chromosomes Intact).";
                        }

                        // Update scatter chart layers reactively
                        if (window.multiOmicsChart) {
                            window.multiOmicsChart.data.datasets[hasCodeletion && hasIdhMutation ? 0 : 1].data = [{ x: hasCodeletion ? 1 : 0, y: hasIdhMutation ? 1 : 0 }];
                            window.multiOmicsChart.update();
                        }

                        this.showReportCard = true;
                        this.isProcessing = false;
                        this.buttonText = 'Execute Diagnostic Run';
                    },
                    resetConsole() {
                        this.isProcessing = false;
                        this.buttonText = 'Execute Diagnostic Run';
                        this.fileName = 'Variant Call Format up to 500MB';
                    }
                }
            }).mount('#vue-app');
        });
    </script>
</body></html>

