@props(['name' => 'User', 'size' => 'md'])

@php
    $initials = strtoupper(substr(trim($name), 0, 2));
    if (empty($initials)) $initials = 'U';

    $dimensions = match($size) {
        'sm' => ['wh' => '32px', 'fs' => '0.75rem', 'radius' => '8px'],
        'lg' => ['wh' => '48px', 'fs' => '1rem', 'radius' => '12px'],
        default => ['wh' => '40px', 'fs' => '0.85rem', 'radius' => '10px'],
    };
@endphp

<div class="avatar-circle" style="width: {{ $dimensions['wh'] }}; height: {{ $dimensions['wh'] }}; border-radius: {{ $dimensions['radius'] }}; background: var(--clsu-green-muted); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--clsu-green); font-size: {{ $dimensions['fs'] }}; flex-shrink: 0;" {{ $attributes }}>
    {{ $initials }}
</div>
