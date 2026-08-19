@props(['name', 'placeholder', 'value', 'label', 'messages', 'rows', 'help', 'editor', 'required', 'class_label', 'class_input'])

<div class="form-group row">
    <label
            class="@if($required ?? false)required @endif {{ $class_label ?? 'col-sm-3' }} col-form-label"
            for="{{ $name }}">
        {{ $label }}
    </label>
    <div class="{{ $class_input ?? 'col-sm-9' }}">
        <textarea name="{{ $name }}"
                  class="form-control mb-2 {{ !empty($messages) ? 'is-invalid' : '' }}"
                  id="{{ $name }}"
                  placeholder="{{ $placeholder ?? $label }}"
                  rows="{{ $rows ?? 5 }}" {{ $attributes }}
              >{!! $value !!}</textarea>
        @if ($messages ?? null)
            @foreach ((array) $messages as $message)
                <div class="text-danger">{{ $message }}</div>
            @endforeach
        @endif
        @if($help ?? '')
            <div class="text-muted">{{ $help }}</div>
        @endif

    </div>
    @if($editor ?? false)
        <script>
            CKEDITOR.replace("{{ $name }}");
        </script>
    @endif
</div>
