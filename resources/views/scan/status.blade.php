<h2>Scan Status: {{ $scan->status }}</h2>

@if($scan->status === 'PENDING')
    <p>We are scanning your website... Refresh in a few moments.</p>
@else
    <a href="{{ route('scan.results', $scan->id) }}">View Results</a>
@endif
