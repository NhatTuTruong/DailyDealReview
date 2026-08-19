<ul class="nav-search-results">
    @foreach($items as $item)
        <li class="list-item">
            <a href="{{ $item['url'] }}" class="tw-block" title="{{ $item['name'] }}">
                <img width="120px"
                     src="{{ $item['image'] }}"
                     alt="{{ $item['name'] }}"
                     class="tw-mr-2 tw-h-auto tw-rounded-sm"
                     @if($item['type'] == 'post') style="object-fit: cover" @endif>
                <h5 class="title_search">
                    {{ $item['name'] }}
                </h5>
            </a>
        </li>
    @endforeach
</ul>