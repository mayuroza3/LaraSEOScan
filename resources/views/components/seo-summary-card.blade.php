@props(['title', 'value', 'icon', 'color' => 'primary', 'trend' => null])

<div class="card h-100 shadow-sm border-0 border-start border-4 border-{{ $color }}">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div>
            <h6 class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">{{ $title }}</h6>
            <h3 class="mb-0 fw-bold">{{ $value }}</h3>
            @if($trend)
                <small class="text-{{ $trend > 0 ? 'success' : 'danger' }} d-block mt-1">
                    <i class="bi bi-arrow-{{ $trend > 0 ? 'up' : 'down' }}"></i> {{ abs($trend) }}%
                </small>
            @endif
        </div>
        <div class="bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="{{ $icon }} fs-4"></i>
        </div>
    </div>
</div>
