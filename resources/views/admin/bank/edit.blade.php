@extends('admin.layouts.app')

@section('title','Edit Bank Info')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bank.index') }}">Bank</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>
<div class="row g-4">
    <div class="col-12 col-xl-6 order-1 order-xl-0">
        <div class="mb-0">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">Edit Bank Info</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('bank.update',$bankInfo->id) }}" method="post">
                        @csrf
                        @method('put')

                        {{-- Account Holder Name --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="holder_name">
                                    Account Holder Name
                                </label>
                                <input class="form-control" type="text" name="holder_name" id="holder_name" value="{{ old('holder_name',$bankInfo->holder_name) }}" required />
                                @error('holder_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Bank Name --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="bank_name">
                                    Bank Name
                                </label>
                                <input class="form-control" type="text" name="bank_name" id="bank_name" value="{{ old('bank_name',$bankInfo->bank_name) }}" required />
                                @error('bank_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Account Number --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="account_number">
                                    Account Number
                                </label>
                                <input class="form-control" type="text" name="account_number" id="account_number" value="{{ old('account_number',$bankInfo->account_number) }}" required />
                                @error('account_number')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- IFSC Code --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="ifsc_code">
                                    IFSC Code
                                </label>
                                <input class="form-control text-uppercase" type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code',$bankInfo->ifsc_code) }}" maxlength="11" required />
                                @error('ifsc_code')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="card-footer border-top-0 pe-0">
                            <div class="d-flex pager wizard list-inline mb-0">
                                <div class="flex-1 text-end">
                                    <button class="btn btn-primary px-6 px-sm-6" type="submit">
                                        Submit
                                    </button>
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