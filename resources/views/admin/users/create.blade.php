@extends('admin.layouts.app')

@section('title','Add New User')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>
<h3 class="mb-2 lh-sm">Add User</h3>
<p class="text-body-tertiary lead mb-2">Create user for cooperate your business.</p>
<div class="row g-4">
    <div class="col-12 col-xl-6 order-1 order-xl-0">
        <div class="mb-0">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">Add New User</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.store') }}" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="name">Name</label>
                                <input class="form-control no-arrow" type="text" name="name" id="name" value="{{ old('name') }}" />
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="ps-0 form-label text-body required" for="mobile">Mobile</label>
                                <input class="form-control no-arrow" type="number" name="mobile" id="mobile" value="{{ old('mobile') }}" />
                                @error('mobile')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="ps-0 form-label text-body required" for="email">Email</label>
                                <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" />
                                @error('email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="password">Password</label>
                                <input class="form-control no-arrow" type="text" name="password" id="password" value="{{ generateStrongPassword(16) }}" />
                                @error('password')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="required p-0 form-label">Status:</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="active" type="radio" name="status" value="1" @checked(old('status',1)==1)>
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="inactive" type="radio" name="status" value="0" @checked(old('status','')==0)>
                                <label class="form-check-label" for="inactive">Inactive</label>
                            </div>
                            @error('status')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="card-footer border-top-0 pe-0 p-0">
                            <div class="d-flex pager wizard list-inline mb-0">
                                <div class="flex-1 text-end">
                                    <button class="btn btn-primary px-6 px-sm-6" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection