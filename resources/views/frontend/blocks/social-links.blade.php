@php
    $socialLinks = \App\Models\Setting::getSocialLinks($setting ?? []);
    $listClass = $listClass ?? 'social-links';
    $listId = $listId ?? null;
@endphp

@if(!empty($socialLinks))
    <ul @if($listId) id="{{ $listId }}" @endif class="{{ $listClass }}">
        @foreach($socialLinks as $social)
            <li class="menu-item menu-item-type-custom menu-item-object-custom">
                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                   aria-label="{{ $social['label'] }}">
                    <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
                    <span class="screen-reader-text">{{ $social['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
@endif
