<div class="row g-0 justify-content-between align-items-center h-100">
    <div class="col-12 col-sm-auto text-center">
        <p class="mb-0 mt-2 mt-sm-0 text-body">{{env('APP_NAME')}}
            <span class="d-none d-sm-inline-block"></span>
            <span class="d-none d-sm-inline-block mx-1">|</span>
            <br class="d-sm-none" />{{ date('Y') }} &copy;
            <a class="mx-1" href="{{ env('APP_URL') }}">{{ env('APP_NAME') }}</a>
        </p>
    </div>
    <div class="col-12 col-sm-auto text-center">
        <p class="mb-0 text-body-tertiary text-opacity-85">V1</p>
    </div>
</div>