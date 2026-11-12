@extends('admin.layouts.app')

@section('title','App Setting')

@section('style')

@endsection

@section('content')
<div class="row g-4">
    <div class="col-12 col-sm-6 col-md-6 col-xl-6 col-xxl-4 mt-2">
        <div class="mb-9">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">App Settings</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('setting.app') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="basic-form-name">App Name:</label>
                                <input class="form-control" id="basic-form-name" type="text" name="app_name" value="{{ old('app_name',$setting['app_name'] ?? null) }}">
                                @error('app_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="basic-form-email">App Email:</label>
                                <input class="form-control" id="basic-form-email" type="email" name="app_email" value="{{ old('app_email',$setting['app_email'] ?? null) }}">
                                @error('app_email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="basic-form-mobile">App Mobile:</label>
                                <input class="form-control" id="basic-form-mobile" type="text" name="app_mobile" value="{{ old('app_mobile',$setting['app_mobile'] ?? null) }}">
                                @error('app_mobile')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="required p-0 form-label" for="address">Company Address:</label>
                                <input class="form-control" id="address" type="text" name="company_address" value="{{ old('company_address',$setting['company_address'] ?? null) }}">
                                @error('company_address')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="required p-0 form-label" for="about-company">About Company:</label>
                                <textarea id="about-company" class="form-control" name="about_company">{{ old('about_company',$setting['about_company'] ?? null) }}</textarea>
                                @error('about_company')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="required p-0 form-label" for="app-favicon">App Favicon:</label>
                                <input class="form-control" id="app-favicon" type="file" name="app_favicon" />
                                @if(isset($setting['app_favicon']))
                                <a target="_blank" href="{{ getFile($setting['app_favicon']) }}"><small><i class="fas fa-eye"></i></small></a>
                                @endif
                                @error('app_favicon')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="required p-0 form-label" for="app-logo">App Logo:</label>
                                <input class="form-control" id="app-logo" type="file" name="app_logo" />
                                @if(isset($setting['app_logo']))
                                <a target="_blank" href="{{ getFile($setting['app_logo']) }}"><small><i class="fas fa-eye"></i></small></a>
                                @endif
                                @error('app_logo')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection