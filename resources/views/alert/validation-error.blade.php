@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-important alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    @isset($icon)
                        <i class="{{$icon}}"></i>
                    @endisset
                </div>
                <div>
                    {!! $error !!}
                </div>
            </div>
            <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endforeach
@endif
