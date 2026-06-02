<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    @include('admin.layouts.meta')
    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    @include('admin.layouts.style')
</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="px-3">
            <div class="row min-vh-100 flex-center p-5">
                <div class="col-12 col-xl-10 col-xxl-8">
                    <div class="row justify-content-center g-5">
                        <div class="col-12 col-lg-6 text-center order-lg-1">
                            <img class="img-fluid w-lg-100 d-light-none" src="{{ asset('public/admin/assets/img/spot-illustrations/500-illustration.png') }}" alt="" width="400" />
                            <img class="img-fluid w-md-50 w-lg-100 d-dark-none" src="{{ asset('public/admin/assets/img/spot-illustrations/dark_500-illustration.png') }}" alt="" width="540" />
                        </div>
                        <div class="col-12 col-lg-6 text-center text-lg-start">
                            <img class="img-fluid mb-6 w-50 w-lg-75 d-dark-none" src="{{ asset('public/admin/assets/img/spot-illustrations/500.png') }}" alt="" />
                            <img class="img-fluid mb-6 w-50 w-lg-75 d-light-none" src="{{ asset('public/admin/assets/img/spot-illustrations/dark_500.png') }}" alt="" />
                            <h2 class="text-body-secondary fw-bolder mb-3">Internal Server Error!</h2>
                            <p class="text-body mb-5">{{ session('error','But relax! Our cat is here to play you some music.') }}</p>
                            <a class="btn btn-lg btn-primary" href="{{ url()->previous() }}">Go Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    @include('admin.layouts.script')
</body>

</html>