@props(['critical', 'error', 'warning', 'info'])

<div class="card h-100 shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 py-3">
        <h5 class="mb-0 fw-bold">Issue Distribution</h5>
    </div>
    <div class="card-body">
        <div id="issuesPieChart" 
             data-critical="{{ $critical }}" 
             data-error="{{ $error }}" 
             data-warning="{{ $warning }}" 
             data-info="{{ $info }}" 
             style="width: 100%; height: 250px;"></div>
    </div>
</div>
