<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('admin/vendors/popper/popper.min.js') }}"></script>
<script src="{{ asset('admin/vendors/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/vendors/anchorjs/anchor.min.js') }}"></script>
<script src="{{ asset('admin/vendors/is/is.min.js') }}"></script>
<script src="{{ asset('admin/vendors/fontawesome/all.min.js') }}"></script>
<script src="{{ asset('admin/vendors/lodash/lodash.min.js') }}"></script>
<script src="{{ asset('admin/vendors/list.js/list.min.js') }}"></script>
<script src="{{ asset('admin/vendors/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('admin/vendors/dayjs/dayjs.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/phoenix.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
  toastr.options = { closeButton: true, progressBar: true, timeOut: 5000 };
  
    @if (Session::has('res'))
      let response = @json(Session::get('res'));
      Object.entries(response).forEach(([type, message]) => {
        if (toastr[type]) toastr[type](message);
      });
    @endif
  
    @if($errors->any())
      toastr.error(@json($errors->first()));
    @endif
</script>