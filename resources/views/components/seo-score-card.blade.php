@props(['score', 'label' => 'SEO Score'])

<div class="card h-100 shadow-sm border-0">
    <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-muted mb-3">{{ $label }}</h5>
        
        <!-- ECharts Container -->
        <div id="seoScoreChart" data-score="{{ $score }}" style="width: 250px; height: 250px;"></div>
        
        <div class="mt-2">
            @if($score >= 90)
                <span class="badge bg-success rounded-pill px-3 py-2 fs-6">Excellent</span>
            @elseif($score >= 70)
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">Good</span>
            @elseif($score >= 50)
                <span class="badge bg-orange text-white rounded-pill px-3 py-2 fs-6" style="background-color: #fd7e14;">Fair</span>
            @else
                <span class="badge bg-danger rounded-pill px-3 py-2 fs-6">Poor</span>
            @endif
        </div>
    </div>
</div>
