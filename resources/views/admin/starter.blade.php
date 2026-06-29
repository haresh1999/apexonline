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
    <div class="px-3">

        <div class="row g-3">

            <!-- Total Transactions -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-envelope fs-4 text-primary"></span>
                    <h4 class="mt-3 mb-1">{{ $count }}</h4>
                    <small>Total Transactions</small>
                </div>
            </div>

            <!-- Completed -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-envelope-open fs-4 text-success"></span>
                    <h4 class="mt-3 mb-1">{{ $completedCount }}</h4>
                    <small>Completed Transactions</small>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-envelope-upload fs-4 text-warning"></span>
                    <h4 class="mt-3 mb-1">{{ $pendingCount }}</h4>
                    <small>Pending Transactions</small>
                </div>
            </div>

            <!-- Processing -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-envelopes fs-4 text-info"></span>
                    <h4 class="mt-3 mb-1">{{ $processingCount }}</h4>
                    <small>Processing Transactions</small>
                </div>
            </div>

            <!-- Failed -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-envelope-check fs-4 text-danger"></span>
                    <h4 class="mt-3 mb-1">{{ $failedCount }}</h4>
                    <small>Failed Transactions</small>
                </div>
            </div>

            <!-- Refunded -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-envelope-block fs-4 text-secondary"></span>
                    <h4 class="mt-3 mb-1">{{ $refundedCount }}</h4>
                    <small>Refunded Transactions</small>
                </div>
            </div>

        </div>

        <hr class="my-3">

        <div class="row g-3">

            <!-- Total Amount -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-wallet fs-4 text-primary"></span>
                    <h4 class="mt-3 mb-1">₹{{ number_format($total) }}</h4>
                    <small>Total Amount</small>
                </div>
            </div>

            <!-- Completed Amount -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-check-circle fs-4 text-success"></span>
                    <h4 class="mt-3 mb-1">₹{{ number_format($completedTotal) }}</h4>
                    <small>Completed Amount</small>
                </div>
            </div>

            <!-- Pending Amount -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-clock fs-4 text-warning"></span>
                    <h4 class="mt-3 mb-1">₹{{ number_format($pendingTotal) }}</h4>
                    <small>Pending Amount</small>
                </div>
            </div>

            <!-- Processing Amount -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-sync fs-4 text-info"></span>
                    <h4 class="mt-3 mb-1">₹{{ number_format($processingTotal) }}</h4>
                    <small>Processing Amount</small>
                </div>
            </div>

            <!-- Failed Amount -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-times-circle fs-4 text-danger"></span>
                    <h4 class="mt-3 mb-1">₹{{ number_format($failedTotal) }}</h4>
                    <small>Failed Amount</small>
                </div>
            </div>

            <!-- Refunded Amount -->
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="border rounded text-center p-3 h-100">
                    <span class="uil uil-redo fs-4 text-secondary"></span>
                    <h4 class="mt-3 mb-1">₹{{ number_format($refundedTotal) }}</h4>
                    <small>Refunded Amount</small>
                </div>
            </div>

        </div>

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