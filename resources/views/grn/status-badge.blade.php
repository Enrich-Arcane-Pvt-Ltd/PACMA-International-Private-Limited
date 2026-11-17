@php
    $statuses = [
        '0' => ['danger', 'Rejected'],
        '1' => ['warning', 'Cancelled'],
        '2' => ['info', 'Not Yet Approved'],
        '3' => ['success', 'Approved'],
        '4' => ['primary', 'Issued'],
    ];
    $badge = $statuses[(string) $status] ?? ['secondary', 'Unknown'];
@endphp
<span class="badge badge-{{ $badge[0] }}">{{ __($badge[1]) }}</span>
