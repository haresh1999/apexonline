@extends('admin.layouts.app')

@section('title','Bank Setting')

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
                            <h4 class="text-body mb-0">UAE Bank Info</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('setting.bank') }}" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="bank-name">Bank Name:</label>
                                <input class="form-control" id="bank-name" type="text" name="bank_name" value="{{ old('bank_name',$setting['bank_name'] ?? null) }}">
                                @error('bank_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="bank-holder-name">Account holder's Name:</label>
                                <input class="form-control" id="bank-holder-name" type="text" name="bank_holder_name" value="{{ old('bank_holder_name',$setting['bank_holder_name'] ?? null) }}">
                                @error('bank_holder_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="bank-account-no">Account No:</label>
                                <input class="form-control" id="bank-account-no" type="text" name="account_no" value="{{ old('account_no',$setting['account_no'] ?? null) }}">
                                @error('account_no')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <label class="required p-0 form-label" for="iban">IBAN:</label>
                                <input class="form-control" id="iban" type="text" name="iban" value="{{ old('iban',$setting['iban'] ?? null) }}">
                                @error('iban')
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