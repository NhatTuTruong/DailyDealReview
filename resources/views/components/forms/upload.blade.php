@props(['name', 'value', 'label', 'messages', 'type', 'help', 'class_label', 'class_input', 'uploadFolder' => ''])

<div class="form-group row">
    <label class="{{ $class_label ?? 'col-sm-3' }} col-form-label" for="{{ $name }}">
        {{ $label }}
    </label>
    <div class="{{ $class_input ?? 'col-sm-9' }}">

        <div class="input-group">
           <span class="input-group-btn color-white">
               <a id="{{ $name }}" class="btn btn-primary border-radius-0">
                   <i class="far fa-image"></i> Chọn file
               </a>
            </span>
            <input id="{{ $name }}_input" class="form-control" value="{{ html_entity_decode($value) }}" type="text"
                   name="{{ $name }}"
                    {{ $placeholder ?? $label  }}>
        </div>
        @if($help ?? '')
            <div class="text-muted">{{ $help }}</div>
        @endif
        @if ($messages ?? null)
            @foreach ((array) $messages as $message)
                <span class="text-danger d-block w-100">{{ $message }}</span><br>
            @endforeach
        @endif

        <div id="{{ $name }}_preview" class="preview_image" style="max-width: 200px">
            @if($value ?? '')
                @php
                    $file_type = \App\Libs\Util::getFileType($value);
                @endphp
                @if($file_type == 'pdf')
                    <iframe src="{{ $value }}" class="w-100 mt-3"></iframe>
                @else
                    <img src="{{ $value }}" class="mt-3">
                @endif
            @endif
        </div>

    </div>


    <script>
        var button1 = document.getElementById("{{ $name }}");
        button1.onclick = function () {
            var startupPath = @json($uploadFolder ? 'Images:/' . trim($uploadFolder, '/') . '/' : '');
            selectFileWithCKFinder("{{$name}}_input", "{{$name}}_preview", startupPath);
        };
    </script>
</div>
