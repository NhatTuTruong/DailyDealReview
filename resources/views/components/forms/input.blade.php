@props(['name', 'placeholder', 'value', 'label', 'messages', 'type', 'required', 'help', 'class'])

<div class="form-group row">
    <label class="col-sm-3 col-form-label">
        {{ $label }}
    </label>
    <div class="col-sm-9">
        <input type="{{ $type ?? 'text' }}" name="{{ $name }}" value="{{ html_entity_decode($value) }}" id="{{ $name }}"
               @if($required ?? false)required @endif
               class="form-control {{ $class ?? '' }} {{ !empty($messages) ? 'is-invalid' : '' }}"
               placeholder="{{ $placeholder ?? $label }}" {{ $attributes }}>
        @if ($messages ?? '')
            @foreach ((array) $messages as $message)
                <div class="text-danger">{{ $message }}</div>
            @endforeach
        @endif
        @if($help ?? '')
            <div class="text-muted">{{ $help }}</div>
        @endif
    </div>
</div>
