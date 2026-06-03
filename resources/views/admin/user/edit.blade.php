@extends('admin.layouts.app')

@section('title','Add New Company')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('company.index') }}">Company</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>
<h3 class="mb-2 lh-sm">Edit Company</h3>
<p class="text-body-tertiary lead mb-2">Update company info for cooperate your business.</p>
<div class="row g-4">
    <div class="col-12 col-xl-6 order-1 order-xl-0">
        <div class="mb-0">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">Update Company</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.update',$user->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="name">Company Name</label>
                                <input class="form-control no-arrow" type="text" name="name" id="name" value="{{ old('name',$user->name) }}" />
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="ps-0 form-label text-body required" for="mobile">Mobile</label>
                                <input class="form-control no-arrow" type="number" name="mobile" id="mobile" value="{{ old('mobile',$user->mobile) }}" />
                                @error('mobile')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="ps-0 form-label text-body required" for="email">Email</label>
                                <input class="form-control" type="email" name="email" id="email" value="{{ old('email',$user->email) }}" />
                                @error('email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="client_id">Client ID</label>
                                <input class="form-control no-arrow" type="text" name="client_id" id="client_id" value="{{ old('client_id',$user->client_id) }}" />
                                @error('client_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="client_secret">Client Secret</label>
                                <input class="form-control no-arrow" type="text" name="client_secret" id="client_secret" value="{{ old('client_secret',$user->client_secret) }}" readonly />
                                @error('client_secret')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="sbx_client_id">Sandbox Client ID</label>
                                <input class="form-control no-arrow" type="text" name="sbx_client_id" id="sbx_client_id" value="{{ old('sbx_client_id',$user->sbx_client_id) }}" />
                                @error('sbx_client_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="sbx_client_secret">Sandbox Client Secret</label>
                                <input class="form-control no-arrow" type="text" name="sbx_client_secret" id="sbx_client_secret" value="{{ old('sbx_client_secret',$user->sbx_client_secret) }}" readonly />
                                @error('sbx_client_secret')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body" for="whitelist_ip">Whitelist Ip Address (Comma seperate)</label>
                                <textarea class="form-control" name="whitelist_ip" id="whitelist_ip" placeholder="45.51.217.8, 12.43.38.103">{{old('whitelist_ip',$user->whitelist_ip)}}</textarea>
                                @error('whitelist_ip')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body" for="default_gateway">Default Gateway</label>
                                <select class="form-control" name="default_gateway" id="default_gateway">
                                    <option value="" selected>Select...</option>
                                    @foreach (gatewayList() as $pg)
                                    <option @selected(old('default_gateway',$user->default_gateway)==$pg) value="{{$pg}}">{{ucfirst($pg)}}</option>
                                    @endforeach
                                </select>
                                @error('default_gateway')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="callback_secret">Callback Secret</label>
                                <input class="form-control no-arrow" type="text" name="callback_secret" id="callback_secret" value="{{ old('callback_secret',$user->callback_secret) }}" readonly />
                                @error('callback_secret')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body" for="password">Password</label>
                                <input class="form-control no-arrow" type="text" name="password" id="password" />
                                @error('password')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="required p-0 form-label">Status:</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="active" type="radio" name="status" value="1" @checked(old('status',$user->status)==1)>
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="inactive" type="radio" name="status" value="0" @checked(old('status',$user->status)==0)>
                                <label class="form-check-label" for="inactive">Inactive</label>
                            </div>
                            @error('status')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="card-footer border-top-0 pe-0">
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