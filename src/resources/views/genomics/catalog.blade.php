<!-- src/resources/views/genomics/catalog.blade.php -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
        <div>
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Archived Sequencing Directory</h3>
            <p class="text-xs text-slate-500 mt-0.5">Historical clinical genomics cases archived on this node</p>
        </div>
        <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full">
            {{ $historicalJobs->total() }} Cohorts Registered
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200/60 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Profile Reference</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">IDH Loci Mutation</th>
                    <th class="px-6 py-4">1p/19q Chromosome</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-600">
                @if(count($historicalJobs) > 0)
                    @foreach($historicalJobs as $pastJob)
                        <tr class="hover:bg-slate-50/60 transition duration-150 group">
                            <td class="px-6 py-4 font-bold text-slate-900 font-mono tracking-tight">{{ $pastJob->patient_id }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold font-mono tracking-wide border uppercase
                                    {{ $pastJob->status === 'completed' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : '' }}
                                    {{ $pastJob->status === 'processing' ? 'bg-blue-50 border-blue-200 text-blue-700' : '' }}
                                    {{ $pastJob->status === 'failed' ? 'bg-rose-50 border-rose-200 text-rose-700' : '' }}
                                    {{ $pastJob->status === 'queued' ? 'bg-slate-50 border-slate-200 text-slate-600' : '' }}">
                                    <span class="h-1.5 w-1.5 rounded-full mr-1.5 
                                        {{ $pastJob->status === 'completed' ? 'bg-emerald-500' : '' }}
                                        {{ $pastJob->status === 'processing' ? 'bg-blue-500 animate-pulse' : '' }}
                                        {{ $pastJob->status === 'failed' ? 'bg-rose-500' : '' }}
                                        {{ $pastJob->status === 'queued' ? 'bg-slate-400' : '' }}">
                                    </span>
                                    {{ $pastJob->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">
                                {{ $pastJob->analysis ? $pastJob->analysis->idh_status : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($pastJob->analysis)
                                    <span class="inline-flex items-center text-[10px] font-bold font-mono px-2 py-0.5 rounded border {{ $pastJob->analysis->has_1p19q_codeletion ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                        {{ $pastJob->analysis->has_1p19q_codeletion ? 'CO-DELETED' : 'INTACT' }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-mono">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($pastJob->status === 'completed' && $pastJob->analysis)
                                    <a href="{{ route('report.show', $pastJob->id) }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 bg-indigo-50/50 hover:bg-indigo-600 hover:text-white px-3 py-1.5 rounded-xl border border-indigo-100/50 transition duration-200">
                                        View Case Metrics
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px] italic pr-2">Processing...</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400 italic font-medium bg-slate-50/30">
                            No genomic profiles submitted to the database warehouse archives yet.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($historicalJobs->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
            {!! $historicalJobs->links() !!}
        </div>
    @endif
</div>

