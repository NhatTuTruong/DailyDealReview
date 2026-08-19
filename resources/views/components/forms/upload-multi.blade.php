@props(['name', 'value', 'label', 'messages', 'type', 'help'])

<div class="form-group row">
    <label class="col-sm-3 col-form-label" for="{{ $name }}">
        {{ $label }}
    </label>
    <div class="col-sm-9">

        <div class="input-group">
           <span class="input-group-btn color-white">
                <a id="{{ $name }}" class="btn btn-primary border-radius-0">
                    <i class="far fa-image"></i>Chọn 1 hoặc nhiều file
                </a>
            </span>
            <textarea id="{{ $name }}_input" class="form-control d-none"
                      name="{{ $name }}">{{ $value }}</textarea>
            @if ($messages)
                @foreach ((array) $messages as $message)
                    <span class="text-danger d-block w-100">{{ $message }}</span><br>
                @endforeach
            @endif
        </div>

        <div id="{{ $name }}_preview" class="list_multiple_image">
            @if($value)
                @foreach(explode(';', $value) as $image)
                    <img src="{{ $image }}"/>
                @endforeach
            @endif
        </div>

        @if($help ?? '')
            <div class="text-muted">{{ $help }}</div>
        @endif
    </div>
    <script>
        let button_multi_upload = document.getElementById("{{ $name }}");
        button_multi_upload.onclick = function () {
            selectMultiFileWithCKFinder("{{ $name }}_input", "{{ $name }}_preview");
        };
    </script>
    <style>
        .list_multiple_image > img {
            display: inline-block;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 10px;
            margin-top: 10px;
        }
    </style>
</div>
