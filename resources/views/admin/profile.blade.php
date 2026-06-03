@extends('admin.layouts.app')

@section('title','Profile')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="#!">Profile</a></li>
        <li class="breadcrumb-item active">Show</li>
    </ol>
</nav>
<div class="row">
    <div class="col-xl-9">
        <form class="row g-3 mb-9" action="{{ route('profile') }}" method="post">
            @csrf
            <h4 class="mb-3">Profile Details </h4>
            <div class="d-flex align-items-end position-relative mb-7">
                <input class="d-none" id="upload-avatar" type="file" name="profile_img" />
                <div class="hoverbox" style="width: 150px; height: 150px">
                    <div class="hoverbox-content rounded-circle d-flex flex-center z-1" style="--phoenix-bg-opacity: .56;">
                        <span class="fa-solid fa-camera fs-1 text-body-quaternary"></span>
                    </div>
                    <div class="position-relative bg-body-quaternary rounded-circle cursor-pointer d-flex flex-center mb-xxl-7">
                        <div class="avatar avatar-5xl">
                            <img class="rounded-circle" src="{{ asset('public/admin/assets/img/team/150x150/58.webp') }}" alt="" />
                        </div>
                        <label class="w-100 h-100 position-absolute z-1" for="upload-avatar"></label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-floating">
                    <input class="form-control" id="name" type="text" placeholder="Company Name" name="name" value="{{ old('name',$user->name) }}" />
                    <label class="required" for="name">Company Name</label>
                    @error('name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-floating">
                    <input class="form-control" id="email" type="email" placeholder="Email address" name="email" value="{{ old('email',$user->email) }}" />
                    <label class="required" for="email">Email Address</label>
                    @error('email')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-floating">
                    <input class="form-control" id="mobile" type="text" placeholder="Mobile" name="mobile" value="{{ old('mobile',$user->mobile) }}" />
                    <label class="required" for="mobile">Mobile No</label>
                    @error('mobile')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-floating">
                    <input class="form-control" id="password" type="password" placeholder="Email new password" value="" name="password" />
                    <label class="required" for="password">New Password</label>
                    @error('password')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-floating">
                    <input class="form-control" id="confirm-password" type="confirm-password" placeholder="Email confirm new password" name="confirm_password" value="" />
                    <label class="required" for="password">Confirm New Password</label>
                    @error('confirm_password')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')

@endsection