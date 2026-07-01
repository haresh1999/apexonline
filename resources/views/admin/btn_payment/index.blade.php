@extends('admin.layouts.app')

@section('style')
<link href="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Button Payment</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Button Payment</h2>
        </div>
    </div>
    <div>
        <div class="mb-4">
            <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input class="form-control search-input search" type="search" name="search" placeholder="Search anything..." value="{{ Request::get('search') }}" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </div>
                    <div class="w-25">
                        <input class="form-control datetimepicker" name="date" id="datepicker" type="text" placeholder="Select date range" data-options='{"disableMobile":true,"dateFormat":"Y-m-d","mode":"range"}' value="{{ request('date') }}">
                    </div>
                    <div class="scrollbar overflow-hidden-y">
                        <button type="submit" class="rounded btn btn-info flex-shrink-0">Filter</button>
                        <a href="{{ route('btn.payment') }}" class="rounded btn btn-warning flex-shrink-0">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <table class="table fs-9 mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="py-4 text-start">ID</th>
                            <th class="py-4 text-start">Order#</th>
                            <th class="py-4 text-start">Gateway Name</th>
                            <th class="py-4 text-start">Created At</th>
                            <th class="py-4 text-start">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $key => $payment)
                        <tr class="align-middle">
                            <td class="text-start">{{ $payment->id }}</td>
                            <td class="text-start">{{ $payment->order_id }}</td>
                            <td class="white-space-nowrap text-start">
                                <h6 class="mb-0">
                                    {{ strtoupper($payment->gateway) }}
                                </h6>
                            </td>
                            <td class="text-start">{{ $payment->created_at }}</td>
                            <td class="text-start">{{ $payment->updated_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    {!! $payments->links() !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
@endsection