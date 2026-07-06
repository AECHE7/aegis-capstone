<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center p-4 border-bottom gap-3" style="background: var(--card-bg);">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-users-viewfinder text-primary me-2"></i> Applicant Evaluation Queue</h6>
            <small class="text-muted">{{ $applications->total() }} total {{ $applications->total() === 1 ? 'application' : 'applications' }}</small>
        </div>
        <div>
            @if(request('status') === 'Cancelled')
                <button type="button" onclick="document.getElementById('statusSelect').value = ''; reloadQueue();" class="btn btn-sm btn-outline-success fw-bold px-3 py-1.5" style="border-radius: 8px;">
                    <i class="fa-solid fa-folder-open me-1"></i> Active Queue
                </button>
            @elseif(request('archived') == '1')
                <button type="button" data-archived="0" class="btn btn-sm btn-outline-success fw-bold px-3 py-1.5 active-queue-toggle" style="border-radius: 8px;">
                    <i class="fa-solid fa-folder-open me-1"></i> Active Queue
                </button>
            @else
                <div class="d-flex gap-2">
                    <button type="button" data-archived="1" class="btn btn-sm btn-outline-secondary fw-bold px-3 py-1.5 active-queue-toggle" style="border-radius: 8px;">
                        <i class="fa-solid fa-box-archive me-1"></i> Archived Queue ({{ $archivedCount }})
                    </button>
                    <button type="button" id="cancelledQueueToggle" class="btn btn-sm btn-outline-danger fw-bold px-3 py-1.5" style="border-radius: 8px;">
                        <i class="fa-solid fa-trash-can me-1"></i> Cancelled Queue ({{ $cancelledCount ?? 0 }})
                    </button>
                </div>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 w-100 w-md-auto justify-content-start justify-content-md-end flex-wrap">
        <a id="exportCsvBtn" href="{{ route('admin.export', request()->query()) }}" class="btn-export btn-export-csv">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </a>
        <a id="exportPdfBtn" href="{{ route('admin.exportPdf', request()->query()) }}" class="btn-export btn-export-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table mb-0" style="border-collapse: separate;">
        <thead>
            <tr>
                <th class="ps-4" style="width: 45px; text-align: center; vertical-align: middle;">
                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor: pointer; transform: scale(1.15);">
                </th>
                <th>Ref ID</th>
                <th>Applicant</th>
                <th>Program / Grant</th>
                <th class="text-center">GWA</th>
                <th class="text-center">AI Risk</th>
                <th class="text-center">Status</th>
                <th>Submitted</th>
                <th class="pe-4 text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr onclick="window.location='{{ route('admin.review', $app->id) }}'" style="cursor:pointer;" class="app-row" data-id="{{ $app->id }}">
                <td class="ps-4 text-center" onclick="event.stopPropagation();" style="vertical-align: middle;">
                    <input type="checkbox" class="app-checkbox" value="{{ $app->id }}" onchange="toggleAppSelect(this)" style="cursor: pointer; transform: scale(1.15);">
                </td>
                <td>
                    <span class="fw-bold text-dark monospace-data" style="font-size:0.8rem;">APP-{{ $app->id }}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="student-avatar">{{ strtoupper(substr($app->user->name ?? 'U', 0, 2)) }}</div>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size:0.875rem;">{{ $app->user->name ?? 'Unknown' }}</div>
                            <div class="text-muted monospace-data" style="font-size:0.72rem;">{{ $app->user->profile?->clsu_id_number ?? 'N/A' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-medium text-dark" style="font-size:0.875rem;">{{ $app->program_name }}</div>
                </td>
                <td class="text-center">
                    <span class="badge rounded-pill px-2 py-1 fw-bold monospace-data" style="background:#f1f5f9;color:#475569;font-size:0.8rem;border:1px solid var(--border-color);">{{ $app->gwa !== null ? number_format($app->gwa, 2) : 'N/A' }}</span>
                </td>
                <td class="text-center">
                    @if($app->document && $app->document->aiResult && !in_array($app->document->aiResult->classification, ['scanning','failed']))
                        @php $score = $app->document->aiResult->fraud_probability; @endphp
                        <span class="fraud-chip monospace-data {{ $score >= 70 ? 'fraud-high' : ($score >= 40 ? 'fraud-mod' : 'fraud-low') }}">
                            <i class="fa-solid fa-microchip" style="font-size:0.6rem;"></i>
                            {{ $score }}%
                        </span>
                    @elseif($app->document && $app->document->aiResult && $app->document->aiResult->classification === 'scanning')
                        <span class="fraud-chip fraud-none"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:0.6rem;"></i> Scanning</span>
                    @else
                        <span class="fraud-chip fraud-none"><i class="fa-solid fa-minus" style="font-size:0.6rem;"></i> N/A</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($app->trashed())
                        <span class="status-badge bg-secondary text-white"><i class="fa-solid fa-ban" style="font-size:0.65rem;"></i> Cancelled</span>
                    @elseif($app->status == 'Pending')
                        <span class="status-badge pending"><i class="fa-solid fa-hourglass-half" style="font-size:0.65rem;"></i> Pending</span>
                    @elseif($app->status == 'Under Review')
                        <span class="status-badge review"><i class="fa-solid fa-magnifying-glass" style="font-size:0.65rem;"></i> Under Review</span>
                        @php $days = $app->updated_at ? $app->updated_at->diffInDays(now()) : 0; @endphp
                        @if($days >= 7)
                            <span class="status-badge bg-danger text-white ms-1" style="font-size: 0.7rem; padding: 2px 8px; border: 1px solid #dc2626;" title="Review pending for 7+ days"><i class="fa-solid fa-circle-exclamation" style="font-size:0.65rem;"></i> Critical</span>
                        @elseif($days >= 3)
                            <span class="status-badge bg-warning text-dark ms-1" style="font-size: 0.7rem; padding: 2px 8px; border: 1px solid #d97706;" title="Review pending for 3+ days"><i class="fa-solid fa-triangle-exclamation" style="font-size:0.65rem;"></i> Overdue</span>
                        @endif
                    @elseif($app->status == 'Approved')
                        <span class="status-badge approved"><i class="fa-solid fa-check" style="font-size:0.65rem;"></i> Approved</span>
                    @else
                        <span class="status-badge rejected"><i class="fa-solid fa-times" style="font-size:0.65rem;"></i> Rejected</span>
                    @endif
                </td>
                <td>
                    <div class="monospace-data" style="font-size:0.82rem;color:#64748b;">{{ $app->created_at->format('M d, Y') }}</div>
                    <div style="font-size:0.72rem;color:#94a3b8;">{{ $app->created_at->format('h:i A') }}</div>
                </td>
                <td class="pe-4 text-end" onclick="event.stopPropagation()">
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        @if($app->trashed())
                            <a href="{{ route('admin.review', $app->id) }}" class="btn btn-sm btn-outline-secondary fw-bold px-2 py-1.5" style="border-radius: 8px; font-size: 0.75rem;">
                                <i class="fa-solid fa-eye me-1"></i> View Details
                            </a>
                            <form action="{{ route('admin.restore', $app->id) }}" method="POST" class="d-inline restore-app-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold px-2 py-1.5" style="border-radius: 8px; font-size: 0.75rem; color: white;">
                                    <i class="fa-solid fa-trash-arrow-up me-1"></i> Restore
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.review', $app->id) }}" class="btn-evaluate">
                                Evaluate <i class="fa-solid fa-arrow-right ms-1" style="font-size:0.7rem;"></i>
                            </a>
                            @if($app->is_archived)
                                <form action="{{ route('admin.unarchive', $app->id) }}" method="POST" class="d-inline archive-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-2 py-1.5 btn-archive-toggle" style="border-radius: 8px; font-size: 0.75rem;" title="Unarchive Application">
                                        <i class="fa-solid fa-box-open"></i> Unarchive
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.archive', $app->id) }}" method="POST" class="d-inline archive-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary fw-bold px-2 py-1.5 btn-archive-toggle" style="border-radius: 8px; font-size: 0.75rem;" title="Archive Application">
                                        <i class="fa-solid fa-box-archive"></i> Archive
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($applications->isEmpty())
<div class="text-center py-5">
    <div class="mb-3">
        <div style="width:80px;height:80px;border-radius:20px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto;">
            <i class="fa-solid fa-inbox fa-2x" style="color:#cbd5e1;"></i>
        </div>
    </div>
    <h6 class="fw-bold text-muted">The queue is empty</h6>
    <p class="text-muted small mb-0">No applications match your current filters.</p>
</div>
@endif

<div class="px-4 py-3 border-top bg-white pagination-container shadow-none" style="border-radius: 0 0 16px 16px;">
    {{ $applications->links('pagination::bootstrap-5') }}
</div>
