@props(['name', 'placeholder', 'value', 'label', 'messages', 'options', 'required', 'help'])

<div class="form-group row">
    <label class="col-sm-3 col-form-label @if($required ?? false)required @endif">{{ $label }}</label>
    <div class="col-sm-9">
        <select class="form-control" aria-label="{{ $label }}" name="{{ $name }}" id="{{ $name }}" {{ $attributes }}>
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
