@extends('admin.layouts.app')

@section('title','Order')

@section('style')
<link href="{{ asset('admin/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
<style>
    .table-wrapper table {
        min-width: 1800px;
    }

    .table td:last-child,
    .table th:last-child {
        padding-right: 2rem !important;
    }

    .table td:first-child,
    .table th:first-child {
        padding-left: 1rem !important;
    }
</style>
@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Sales & Transactions</li>
        <li class="breadcrumb-item active">History</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Transactions</h2>
        </div>
    </div>
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('tnx.index') }}">
                <span>All </span>
                <span class="text-body-tertiary fw-semibold">({{$total}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tnx.index',['status' => 'pending']) }}">
                <span>Pending</span>
                <span class="text-body-tertiary fw-semibold">({{$pendingTotal}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tnx.index',['status' => 'processing']) }}">
                <span>Processing</span>
                <span class="text-body-tertiary fw-semibold">({{ $processingTotal }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tnx.index',['status' => 'completed']) }}">
                <span>Completed</span>
                <span class="text-body-tertiary fw-semibold">({{$completedTotal}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tnx.index',['status' => 'failed']) }}">
                <span>Failed</span>
                <span class="text-body-tertiary fw-semibold">({{$failedTotal}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tnx.index',['status' => 'refunded']) }}">
                <span>Refunded</span>
                <span class="text-body-tertiary fw-semibold">({{$refundedTotal}})</span>
            </a>
        </li>
    </ul>
    <div id="orderTable">
        <div class="mb-3">
            <form class="position-relative" action="{{ route('tnx.index') }}" method="get">
                <div class="row g-3 align-items-end">

                    {{-- Search --}}
                    <div class="col-12 col-lg-2">
                        <label class="ps-0 form-label mb-1">Search</label>
                        <div class="search-box position-relative w-auto">
                            <input class="form-control search-input search" type="search" name="search" placeholder="Search anything..." value="{{ request('search') }}" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="ps-0 form-label mb-1">Date</label>
                        <input class="form-control datetimepicker" name="date" id="datepicker" type="text" data-options='{"disableMobile":true,"dateFormat":"Y-m-d","mode":"range"}' value="{{ request('date') }}" placeholder="Date Range">
                    </div>

                    {{-- Gateway --}}
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="ps-0 form-label mb-1">Gateway</label>
                        <select class="form-select" name="pg">
                            <option value="">All Gateways</option>
                            @foreach ($gateways as $pg)
                            <option value="{{ $pg }}" @selected(request('pg')==$pg)>
                                {{ ucfirst($pg) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="ps-0 form-label mb-1">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="pending" @selected(request('status')=='pending' )>
                                Pending
                            </option>
                            <option value="processing" @selected(request('status')=='processing' )>
                                Processing
                            </option>
                            <option value="completed" @selected(request('status')=='completed' )>
                                Completed
                            </option>
                            <option value="failed" @selected(request('status')=='failed' )>
                                Failed
                            </option>
                            <option value="refunded" @selected(request('status')=='refunded' )>
                                Refunded
                            </option>
                        </select>
                    </div>

                    {{-- Environment --}}
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="ps-0 form-label mb-1">Environment</label>
                        <select class="form-select" name="env">
                            <option value="">All Environments</option>
                            <option value="sandbox" @selected(request('env')=='sandbox' )>
                                Sandbox
                            </option>
                            <option value="production" @selected(request('env')=='production' )>
                                Production
                            </option>
                        </select>
                    </div>

                    {{-- Company --}}
                    @if(auth()->id() == 1)
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="ps-0 form-label mb-1">Company</label>
                        <select class="form-select" name="user_id">
                            <option value="">All Companies</option>
                            @foreach ($users as $uId => $user)
                            <option value="{{ $uId }}" @selected(request('user_id')==$uId)>
                                {{ $user }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Buttons --}}
                    <div class="col-12 col-lg-auto ms-lg-auto">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">

                            <button type="submit" class="btn btn-info flex-grow-1 flex-lg-grow-0">
                                <span class="fas fa-filter me-1"></span>
                                Filter
                            </button>

                            <a href="{{ route('tnx.export', request()->query()) }}" class="btn btn-success flex-grow-1 flex-lg-grow-0">
                                <span class="fas fa-file-excel me-1"></span>
                                Export Excel
                            </a>

                            <a href="{{ route('tnx.index') }}" class="btn btn-warning flex-grow-1 flex-lg-grow-0">
                                <span class="fas fa-redo me-1"></span>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <div class="table-wrapper">
                    <table class="table table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 50px">NAME</th>
                                <th class="white-space-nowrap align-middle py-3 text-center" style="max-width: 50px">ORDER</th>
                                <th class="white-space-nowrap align-middle py-3 text-center" style="max-width: 60px">STATUS</th>
                                <th class="white-space-nowrap align-middle py-3 text-center" style="max-width: 60px">AMOUNT</th>
                                <th class="white-space-nowrap align-middle py-3 text-center" style="max-width: 60px">PDF</th>
                                <th class="white-space-nowrap align-middle py-3 text-center" style="max-width: 60px">RESEND</th>
                                <th class="white-space-nowrap align-middle py-3 text-center" style="max-width: 60px">ENV</th>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 50px">GATEWAY</th>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 100px">DATETIME</th>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 170px">REF#</th>
                                <th class="white-space-nowrap align-middle py-3 text-start">NAME</th>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 110px">EMAIL</th>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 70px">MOBILE</th>
                                <th class="white-space-nowrap align-middle py-3 text-start" style="max-width: 80px">UPDATED TIME</th>
                            </tr>
                        </thead>

                        <tbody class="list" id="order-table-body">
                            @foreach ($tnxs as $tnx)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                <td class="align-middle white-space-nowrap py-3" style="max-width: 50px">
                                    {{ $tnx->user->name }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-center" style="max-width: 50px">
                                    <h6 class="mb-0">
                                        <a href="{{ route('tnx.show',$tnx->id) }}">{{ $tnx->mr_order_id }}</a>
                                    </h6>
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-center" style="max-width: 70px">
                                    @if(in_array($tnx->status, ['completed']))
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-success">
                                        <span class="badge-label">{{ ucfirst($tnx->status) }}</span>
                                        <span class="ms-1" data-feather="check"></span>
                                    </span>
                                    @elseif(in_array($tnx->status, ['pending','processing']))
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-warning">
                                        <span class="badge-label">{{ ucfirst($tnx->status) }}</span>
                                        <span class="ms-1" data-feather="alert-octagon"></span>
                                    </span>
                                    @elseif(in_array($tnx->status, ['refunded','failed']))
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-danger">
                                        <span class="badge-label">{{ ucfirst($tnx->status) }}</span>
                                        <span class="ms-1" data-feather="alert-octagon"></span>
                                    </span>
                                    @endif
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-center" style="max-width: 60px">
                                    <h6 class="mb-0">₹{{ $tnx->amount }}</h6>
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-center" style="max-width: 60px">
                                    @if ($tnx->status == 'completed')
                                    <a target="_blank" href="{{ route('declaration',$tnx->id) }}" class="{{ $tnx->esign_status == 'completed' ? 'text-success' : 'text-danger' }} me-2">
                                        <span class="fas fa-file-pdf"></span>
                                    </a>
                                    <a href="{{ route('invoice',$tnx->id) }}" class="text-success">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    @endif
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-center" style="max-width: 60px">
                                    <a href="{{ route('send.email',$tnx->id) }}">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-center" style="max-width: 60px">
                                    <h6 class="mb-0">{{ strtoupper($tnx->env) }}</h6>
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-start" style="max-width: 50px">
                                    <h6 class="mb-0">{{ strtoupper($tnx->gateway) }}</h6>
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-start" style="max-width: 100px">
                                    {{ $tnx->created_at }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3" style="max-width: 170px">
                                    {{ $tnx->reference_id }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->payer_name }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3" style="max-width: 110px">
                                    {{ $tnx->payer_email }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3" style="max-width: 70px">
                                    {{ $tnx->payer_mobile }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-start" style="max-width: 80px">
                                    {{ $tnx->updated_at }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center justify-content-center py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        {!! $tnxs->withQueryString()->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
@endsection