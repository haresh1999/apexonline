<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
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
  @include('admin.layouts.meta')
  <!-- ===============================================-->
  <!--    Stylesheets-->
  <!-- ===============================================-->
  @include('admin.layouts.style')
  {{-- CUSTOM PAGE STYLE HERE --}}
  @yield('style')
</head>

<body>
  <!-- ===============================================-->
  <!--    Main Content-->
  <!-- ===============================================-->
  <main class="main" id="top">

    @include('admin.layouts.navbar')

    @include('admin.layouts.header')

    <div class="content">
      @yield('content')
      <footer class="footer position-absolute">
        @include('admin.layouts.footer')
      </footer>
    </div>
  </main>
  <!-- ===============================================-->
  <!--    End of Main Content-->
  <!-- ===============================================-->

  <!-- ===============================================-->
  <!--    JavaScripts-->
  <!-- ===============================================-->
  @include('admin.layouts.script')
  {{-- CUSTOM PAGE SCRIPT HERE --}}
  @yield('script')

  {{-- MESSAGE TOAST --}}
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div class="toast toast-success align-items-center text-white dark__text-gray-1100 bg-success border-0" role="alert" data-bs-delay="5000" data-bs-autohide="true" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div class="toast toast-warning align-items-center text-white dark__text-gray-1100 bg-warning border-0" role="alert" data-bs-delay="5000" data-bs-autohide="true" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div class="toast toast-error align-items-center text-white dark__text-gray-1100 bg-danger border-0" role="alert" data-bs-delay="5000" data-bs-autohide="true" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

</body>

</html>