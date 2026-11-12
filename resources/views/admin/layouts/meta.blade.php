<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- ===============================================-->
<!--    Document Title-->
<!-- ===============================================-->
<title>{{ env('APP_NAME') }}</title>
<!-- ===============================================-->
<!--    Favicons-->
<!-- ===============================================-->
<link rel="apple-touch-icon" sizes="180x180" href="{{ getFile('setting/app-favicon.png') }} ">
<link rel="icon" type="image/png" sizes="32x32" href="{{ getFile('setting/app-favicon.png') }} ">
<link rel="icon" type="image/png" sizes="16x16" href="{{ getFile('setting/app-favicon.png') }} ">
<link rel="shortcut icon" type="image/x-icon" href="{{ getFile('setting/app-favicon.png') }} ">
<link rel="manifest" href="{{ getFile('setting/app-favicon.png') }} ">
<meta name="msapplication-TileImage" content="{{ getFile('setting/app-favicon.png') }}">
<meta name="theme-color" content="#ffffff">
<script src="{{ asset('admin/vendors/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/config.js') }}"></script>