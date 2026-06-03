@extends('admin.layouts.app')

@section('title','Dashboard')

@section('style')
<link href="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="pb-6">
    <div class="row align-items-center justify-content-between g-3 mb-6">
        <div class="col-12 col-md-auto">
            <h3 class="mb-0">Sales Dasbhaord & Analytics</h3>
        </div>
        <div class="col-12 col-md-auto">
            <div class="flatpickr-input-container d-flex gap-2">
                <input class="form-control datetimepicker text-start" id="datepicker" type="text" data-options='{"dateFormat":"Y-m-d","disableMobile":true}' placeholder="Filter by date" value="{{ request()->get('date') }}" />
                <span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
                <a class="btn btn-danger" href="{{ route('dashboard') }}">Reset</a>
            </div>
        </div>
    </div>
    <div class="px-3 mb-0">
        <hr>
        <div class="row justify-content-between">
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0 "><span class="uil fs-5 lh-1 uil-envelope text-primary"></span>
                <h1 class="fs-5 pt-3">{{ $count }}</h1>
                <p class="fs-9 mb-0">Total Transactions</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-4 pb-xxl-0 pt-4 pt-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-open text-info"></span>
                <h1 class="fs-5 pt-3">{{ $completedCount }}</h1>
                <p class="fs-9 mb-0">Completed Transactions</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-4 pb-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-upload text-info"></span>
                <h1 class="fs-5 pt-3">{{ $pendingCount }}</h1>
                <p class="fs-9 mb-0">Pending Transactions</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-bottom-xxl-0 border-bottom border-end border-end-md-0 pb-4 pb-xxl-0 pt-4 pt-md-0"><span class="uil fs-5 lh-1 uil-envelopes text-primary"></span>
                <h1 class="fs-5 pt-3">{{ $processingCount }}</h1>
                <p class="fs-9 mb-0">Processing Transactions</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-md-4 pb-xxl-0 pt-4 pt-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-check text-success"></span>
                <h1 class="fs-5 pt-3">{{ $failedCount }}</h1>
                <p class="fs-9 mb-0">Failed Transactions</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl pb-md-4 pb-xxl-0 pt-4 pt-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-block text-danger"></span>
                <h1 class="fs-5 pt-3">{{ $refundedCount }}</h1>
                <p class="fs-9 mb-0">Refunded Transactions</p>
            </div>
        </div>
        <hr>
        <div class="row justify-content-between mb-3">
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl pb-md-4 pb-xxl-0 pt-4 pt-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-block text-danger"></span>
                <h1 class="fs-5 pt-3">₹{{ number_format($total) }}</h1>
                <p class="fs-9 mb-0">Total Amount</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-bottom-xxl-0 border-bottom border-end border-end-md-0 pb-4 pb-xxl-0 pt-4 pt-md-0">
                <span class="uil fs-5 lh-1 uil-envelopes text-primary"></span>
                <h1 class="fs-5 pt-3">₹{{ number_format($completedTotal) }}
                </h1>
                <p class="fs-9 mb-0">Completed Amount</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0 ">
                <span class="uil fs-5 lh-1 uil-envelope text-primary"></span>
                <h1 class="fs-5 pt-3">₹{{ number_format($pendingTotal) }}</h1>
                <p class="fs-9 mb-0">Pending Amount</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-4 pb-xxl-0">
                <span class="uil fs-5 lh-1 uil-envelope-upload text-info"></span>
                <h1 class="fs-5 pt-3">₹{{ number_format($processingTotal) }}</h1>
                <p class="fs-9 mb-0">Processing Amount</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-4 pb-xxl-0 pt-4 pt-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-open text-info"></span>
                <h1 class="fs-5 pt-3">₹{{ number_format($failedTotal) }}</h1>
                <p class="fs-9 mb-0">Failed Amount</p>
            </div>
            <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-md-4 pb-xxl-0 pt-4 pt-xxl-0"><span class="uil fs-5 lh-1 uil-envelope-check text-success"></span>
                <h1 class="fs-5 pt-3">₹{{ number_format($refundedTotal) }}</h1>
                <p class="fs-9 mb-0">Refunded Amount</p>
            </div>
        </div>
        <hr>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
<script>
    $('#datepicker').change(function(){
        window.location.href = "{{ route('dashboard') }}?date="+this.value
    });
</script>
@endsection