@extends('admin.layouts.app')

@section('title','Webhook Logs')

@section('style')
<link href="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Webhook</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Webhook Logs</h2>
        </div>
    </div>
    <div class="mb-4">
        <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
            <div class="d-flex flex-wrap gap-2">
                <div class="search-box">
                    <input class="form-control search-input search" type="search" name="search" placeholder="Search anything..." value="{{ Request::get('search') }}" aria-label="Search" />
                    <span class="fas fa-search search-box-icon"></span>
                </div>
                <div>
                    <div class="btn-group position-static gap-2" role="group">
                        <input class="form-control datetimepicker" name="date" id="datepicker" type="text" data-options='{"disableMobile":true,"dateFormat":"Y-m-d","mode":"range"}' value="{{ request('date') }}" placeholder="Date Range">
                    </div>
                </div>
                <div class="scrollbar overflow-hidden-y">
                    <button type="submit" class="rounded btn btn-info flex-shrink-0">Filter</button>
                    <a href="{{ route('webhoook.index') }}" class="rounded btn btn-warning flex-shrink-0">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
        <div class="table-responsive scrollbar mx-n1 px-1">
            <table class="table fs-9 mb-0 text-center">
                <thead>
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="py-4 text-start">URL</th>
                        <th class="py-4 text-start">ORDER#</th>
                        <th class="py-4 text-start">Env</th>
                        <th class="p-4">Status</th>
                        <th class="py-4 text-start">Date Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $key => $log)
                    <tr class="align-middle">
                        <td>{{ ++$key }}</td>
                        <td class="text-start">{{ $log->url }}</td>
                        <td class="text-start">{{ $log->transaction->mr_order_id }}</td>
                        <td class="text-start">{{ strtoupper($log->env) }}</td>
                        <td>{{ $log->status }}</td>
                        <td class="text-start">{{ $log->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                {!! $logs->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
@endsection