@props(['name', 'placeholder', 'value', 'label', 'messages', 'options', 'required', 'help', 'multiple', 'id'])

<div class="form-group row">
    <label class="col-sm-3 col-form-label @if($required ?? false)required @endif">{{ $label }}</label>
    <div class="col-sm-9">
        <select class="form-control select2bs4" aria-label="{{ $label }}" name="{{ $name }}"
                @if(!empty($multiple))multiple @endif
                id="{{ $id ?? $name }}" {{ $attributes }} >
            {{$options}}
        </select>
        @if ($messages)
            @foreach ((array) $messages as $message)
                <span class="text-danger">{{ $message }}</span>
            @endforeach
        @endif
        @if($help ?? '')
            <p class="text-muted">{{ $help }}</p>
        @endif
    </div>
</div>

@once
    <link href="{{ asset('backend_assets/vendor/select2/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend_assets/vendor/select2/custom.css') }}" rel="stylesheet"/>
    <script src="{{ asset('backend_assets/vendor/select2/select2.min.js') }}"></script>
@endonce

<script>
    $(document).ready(function () {
        $('#' + "{{ $id ?? $name }}").select2();
    });
</script>
