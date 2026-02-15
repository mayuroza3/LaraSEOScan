@props(['issues'])

<div class="table-responsive shadow-sm border rounded bg-white">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">
            <tr>
                <th scope="col" class="py-3 ps-4">Issue</th>
                <th scope="col" class="py-3">Severity</th>
                <th scope="col" class="py-3 text-end pe-4">Details</th>
            </tr>
        </thead>
        <tbody class="border-top-0">
            @forelse($issues as $issue)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($issue->severity == 'critical' || $issue->severity == 'error')
                                <i class="bi bi-x-circle text-danger fs-5 me-3"></i>
                            @elseif($issue->severity == 'warning')
                                <i class="bi bi-exclamation-triangle text-warning fs-5 me-3"></i>
                            @else
                                <i class="bi bi-info-circle text-info fs-5 me-3"></i>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $issue->message }}</h6>
                                <small class="text-muted">{{ $issue->page_url ?? 'Site-wide' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill 
                            @if($issue->severity == 'critical') bg-danger 
                            @elseif($issue->severity == 'error') bg-danger bg-opacity-75
                            @elseif($issue->severity == 'warning') bg-warning text-dark 
                            @else bg-info text-dark @endif">
                            {{ ucfirst($issue->severity) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#issue-{{ $loop->index }}">
                            View <i class="bi bi-chevron-down"></i>
                        </button>
                    </td>
                </tr>
                <tr class="collapse bg-light" id="issue-{{ $loop->index }}">
                    <td colspan="3" class="p-4">
                        <strong>Context:</strong>
                        <pre class="mt-2 mb-0 bg-white p-3 border rounded text-wrap">{{ json_encode($issue->context, JSON_PRETTY_PRINT) }}</pre>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-1 text-success mb-3 d-block"></i>
                        No issues found! Great job.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
