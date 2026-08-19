@props(['id_form', 'title'])

<button type="button" onclick="$('#{{ $id_form ?? 'formDataGrid' }}').submit()"
        class="btn btn-primary btn-sm">
    <i class="fa fa-save" aria-hidden="true"></i>
    {{ $title ?? 'Cập nhật' }}
</button>
