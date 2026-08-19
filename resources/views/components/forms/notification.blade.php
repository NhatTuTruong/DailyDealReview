@props(['success', 'error'])


<script type="text/javascript">
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    @if($success ?? '')
        toastr["success"]("{{ $success }}", "Success!")
    @endif

        @if($error ?? '')
        toastr["error"]("{{ $error }}", "Error!")
    @endif
</script>
