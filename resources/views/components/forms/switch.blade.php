@props(['name', 'value', 'label', 'messages', 'required', 'help'])

<div class="form-group row">
    <label class="@if($required ?? false)required @endif col-sm-3 col-form-label">{{ $label }}</label>
    <div class="col-sm-9">
        <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1" @if($value == 1) checked @endif
        data-bootstrap-switch data-on-text="YES" data-off-text="NO"/>
        @if ($messages)
            @foreach ((array) $messages as $message)
                <div class="text-danger">{{ $message }}</div>
            @endforeach
        @endif
        @if($help ?? '')
            <div class="text-muted">{{ $help }}</div>
        @endif
    </div>
</div>
<script>
    $('document').ready(function () {
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    });
</script>
