@props(['issues'])

@php
    $items = is_a($issues, \Illuminate\Contracts\Pagination\Paginator::class) 
        ? $issues->items() 
        : (is_array($issues) ? $issues : (is_object($issues) && method_exists($issues, 'all') ? $issues->all() : $issues));

    $groupedIssues = collect($items)->groupBy(function($item) {
        return trim($item->message);
    });
@endphp

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
            @forelse($groupedIssues as $message => $group)
                @php
                    $first = $group->first();
                    $count = $group->count();
                    $groupId = 'issue-grp-' . md5($message . $loop->index);
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($first->severity == 'critical' || $first->severity == 'error')
                                <i class="bi bi-x-circle text-danger fs-5 me-3"></i>
                            @elseif($first->severity == 'warning')
                                <i class="bi bi-exclamation-triangle text-warning fs-5 me-3"></i>
                            @else
                                <i class="bi bi-info-circle text-info fs-5 me-3"></i>
                            @endif
                            <div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $first->message }}</h6>
                                    @if($count > 1)
                                        <span class="badge bg-secondary bg-opacity-75 rounded-pill px-2 py-1 font-monospace" style="font-size: 0.75rem;">
                                            {{ $count }} {{ \Illuminate\Support\Str::plural('instance', $count) }}
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $first->page->url ?? $first->page_url ?? 'Site-wide' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill 
                            @if($first->severity == 'critical') bg-danger 
                            @elseif($first->severity == 'error') bg-danger bg-opacity-75
                            @elseif($first->severity == 'warning') bg-warning text-dark 
                            @else bg-info text-dark @endif">
                            {{ ucfirst($first->severity) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#{{ $groupId }}">
                            View @if($count > 1) ({{ $count }}) @endif <i class="bi bi-chevron-down ms-1"></i>
                        </button>
                    </td>
                </tr>
                <tr class="collapse bg-light" id="{{ $groupId }}">
                    <td colspan="3" class="p-4">
                        @if($count > 1)
                            <div class="p-3 bg-white rounded border">
                                <strong class="d-block mb-2 text-dark"><i class="bi bi-list-task me-1"></i> All {{ $count }} Occurrences:</strong>
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm table-striped align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Target Selector / Element</th>
                                                <th>Context Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group as $idx => $item)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><code>{{ $item->selector ?? 'N/A' }}</code></td>
                                                    <td>
                                                        <span class="text-monospace text-wrap" style="word-break: break-word;">
                                                            {{ is_array($item->context) ? json_encode($item->context, JSON_UNESCAPED_SLASHES) : ($item->context ?? '-') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-white rounded border">
                                @if(!empty($first->selector))
                                    <div class="mb-2"><strong>Target Selector:</strong> <code>{{ $first->selector }}</code></div>
                                @endif
                                <strong>Context:</strong>
                                <pre class="mt-2 mb-0 bg-light p-3 border rounded text-wrap font-monospace small" style="max-height: 200px; overflow-y: auto;">{{ is_string($first->context) ? $first->context : json_encode($first->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        @endif
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
