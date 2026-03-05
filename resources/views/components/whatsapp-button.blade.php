@props([
    'number' => '',
    'message' => '',
    'label' => 'WhatsApp',
])

@php
    $digits = preg_replace('/\D+/', '', (string) $number);
    $digits = is_string($digits) ? $digits : '';
    $url = $digits !== ''
        ? 'https://wa.me/' . $digits . '?text=' . rawurlencode((string) $message)
        : null;
@endphp

@if($url !== null)
    <a href="{{ $url }}" target="_blank" rel="noopener" {{ $attributes }}>
        {{ $label }}
    </a>
@endif
