@props(['status'])

@php
    $normalized = strtolower(trim($status ?? ''));
    $config = match($normalized) {
        'approved'     => ['class' => 'approved', 'icon' => 'fa-check', 'label' => 'Approved'],
        'rejected'     => ['class' => 'rejected', 'icon' => 'fa-times', 'label' => 'Rejected'],
        'under review', 'review' => ['class' => 'review', 'icon' => 'fa-magnifying-glass', 'label' => 'Under Review'],
        'returned'     => ['class' => 'bg-warning text-dark border border-warning border-opacity-50', 'icon' => 'fa-reply', 'label' => 'Returned'],
        'forfeited'    => ['class' => 'bg-dark text-white', 'icon' => 'fa-user-slash', 'label' => 'Forfeited'],
        'cancelled'    => ['class' => 'bg-secondary text-white', 'icon' => 'fa-ban', 'label' => 'Cancelled'],
        default        => ['class' => 'pending', 'icon' => 'fa-hourglass-half', 'label' => ucfirst($status ?? 'Pending')],
    };
@endphp

<span class="status-badge {{ $config['class'] }}" {{ $attributes }}>
    <i class="fa-solid {{ $config['icon'] }}" style="font-size:0.65rem;"></i>
    {{ $config['label'] }}
</span>
