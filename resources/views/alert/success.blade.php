<div class="alert alert-important alert-success alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            @isset($icon)
                <i class="{{$icon}}"></i>
            @endisset
        </div>
        <div>
            {!! $message !!}
        </div>
    </div>
    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
</div>
