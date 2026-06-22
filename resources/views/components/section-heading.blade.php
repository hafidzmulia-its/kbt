@props([
    'kicker' => null,
    'title',
    'copy' => null,
    'align' => 'left',
    'kickerClass' => '',
    'titleClass' => '',
    'copyClass' => '',
])

@php
    $alignmentClasses = $align === 'center'
        ? 'items-center text-center mx-auto'
        : 'items-start text-left';
@endphp

<div {{ $attributes->class(['section-heading', $alignmentClasses]) }}>
    @if ($kicker)
        <p class="section-heading-kicker {{ $kickerClass }}">{{ $kicker }}</p>
    @endif

    <h2 class="section-heading-title {{ $titleClass }}">
        {{ $title }}
    </h2>

    @if ($copy)
        <p class="section-heading-copy {{ $copyClass }}">
            {{ $copy }}
        </p>
    @endif
</div>
