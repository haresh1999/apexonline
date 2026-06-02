<link rel="preconnect" href="https://fonts.googleapis.com/">
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="{{ asset('public/admin/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
<link href="{{ asset('public/admin/assets/css/theme-rtl.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
<link href="{{ asset('public/admin/assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
<link href="{{ asset('public/admin/assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
<link href="{{ asset('public/admin/assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script>
  var phoenixIsRTL = window.config.config.phoenixIsRTL;
      if (phoenixIsRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
      } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
      }
</script>
<style>
  .required:after {
    content: " *";
    color: red;
  }
</style>