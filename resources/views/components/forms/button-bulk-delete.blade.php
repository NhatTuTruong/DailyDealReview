@props(['title', 'url', 'icon'])

<button type="button" class="btn btn-sm bg-maroon BulkDeleteRecords"
        data-checker=".checker"
        data-url="{{ $url ?? '' }}">
    <i class="{{ $icon ?? 'fa fa-trash mr-1' }} mr-1" aria-hidden="true"></i>
    {{ $title ?? 'Xóa' }}
</button>
