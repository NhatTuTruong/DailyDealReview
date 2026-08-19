@props(['title', 'url', 'target', 'icon'])

<a href="{{ $url ?? '' }}"
   {{ $attributes->merge(['class' => 'btn btn-sm fw-bold btn-primary']) }} target="{{ $target ?? '_self' }}">
    @if($icon ?? '')
        <i class="{{ $icon }}" aria-hidden="true"></i>
    @endif
    {{ $title }}
</a>
