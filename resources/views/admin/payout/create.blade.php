@extends('admin.layouts.app')

@section('title','Create Payout')

@section('style')
<link href="{{ asset('admin/vendors/choices/choices.min.css') }}" rel="stylesheet" />
<style>
    /* Remove (X) button */
    .choices[data-type*="select-multiple"] .choices__button,
    .choices__list--multiple .choices__item .choices__button {
        border-left: 1px solid #f8c59f !important;
        background-image: none !important;
        color: #a85a22 !important;
        opacity: 1 !important;
        margin-left: 6px !important;
        padding: 0 0 0 6px !important;
        width: auto !important;
        height: auto !important;
        text-indent: 0 !important;
        font-size: 0 !important;
    }

    /* Dropdown container */
    .choices__list--dropdown,
    .choices__list[aria-expanded] {
        background: #fff !important;
        color: #333 !important;
        border: 1px solid #f8c59f !important;
    }

    /* Normal options */
    .choices__list--dropdown .choices__item--choice,
    .choices__list[aria-expanded] .choices__item--choice {
        background: #fff !important;
        color: #333 !important;
        cursor: pointer !important;
        padding: 10px 12px !important;
    }

    /* Hover / keyboard-highlighted option */
    .choices__list--dropdown .choices__item--choice.is-highlighted,
    .choices__list[aria-expanded] .choices__item--choice.is-highlighted {
        background: #fff3e9 !important;
        color: #a85a22 !important;
    }

    /* Keep selectable options interactive */
    .choices__list--dropdown .choices__item--choice[data-selectable],
    .choices__list[aria-expanded] .choices__item--choice[data-selectable] {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    /* Disabled options only */
    .choices__list--dropdown .choices__item--choice.is-disabled,
    .choices__list[aria-expanded] .choices__item--choice.is-disabled {
        background: #f8fafc !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
    }

    .choices__list--single .choices__item,
    .choices__list--dropdown .choices__item--choice,
    .choices__list[aria-expanded] .choices__item--choice {
        font-size: 0.8rem !important;
    }
</style>
@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payout.index') }}">Payout</a></li>
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
                            <h4 class="text-body mb-0">Create Payout</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('payout.store') }}" method="post">
                        @csrf

                        @if (! empty($banks))
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="bank_info">
                                    Select Bank
                                </label>

                                <select class="form-select" id="bank_info" name="bank_id" data-choices="data-choices" data-options='{"removeItemButton":true,"placeholder":true}'>
                                    <option value="">Select...</option>
                                    @foreach ($banks as $bank)
                                    <option value="{{$bank->id}}">{{ $bank->holder_name }} - ({{ $bank->account_number }})</option>
                                    @endforeach
                                </select>

                                @error('bank_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="bank_id" value="{{ $bankInfo->id }}" />
                        @endif

                        {{-- Amount --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="amount">
                                    Amount
                                </label>
                                <input class="form-control" type="number" name="amount" id="amount" value="{{ old('amount') }}" min="1" step="0.01" required />
                                @error('amount')
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
<script src="{{ asset('admin/vendors/choices/choices.min.js') }}"></script>
@endsection