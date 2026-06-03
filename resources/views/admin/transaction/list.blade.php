@extends('admin.layouts.app')

@section('title','Order')

@section('style')
<link href="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
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
            <a class="nav-link" href="{{ route('tnx.index',['status' => 'pencing']) }}">
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
        <div class="mb-4">
            <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
                <div class="d-flex flex-wrap gap-3">
                    <div class="search-box">
                        <input class="form-control search-input search" type="search" name="search" placeholder="Search anything..." value="{{ Request::get('search') }}" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </div>
                    <div class="scrollbar overflow-hidden-y">
                        <div class="btn-group position-static gap-2" role="group">
                            <div class="col-8 col-sm-4">
                                <input onchange="window.location.href = '{{ route('tnx.index') }}?date='+this.value" class="form-control datetimepicker" id="datepicker" type="text" placeholder="May 1 - 31, 2025" data-options='{"disableMobile":true,"dateFormat":"Y-m-d"}' value="{{ Request::get('date') }}" />
                            </div>
                            <div class="btn-group position-static text-nowrap">
                                <select class="form-select" name="pg">
                                    <option selected="" value="">Gateway</option>
                                    @foreach (gatewayList() as $pg)
                                    <option @selected(Request::get('pg')==$pg) value="{{ $pg }}">{{ucfirst($pg)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="btn-group position-static text-nowrap">
                                <select class="form-select" name="env">
                                    <option selected="" value="">Env</option>
                                    <option @selected(Request::get('env')=='sandbox' ) value="sandbox">Sandbox</option>
                                    <option @selected(Request::get('env')=='production' ) value="production">Production</option>
                                </select>
                            </div>
                            @if(auth()->id() == 1)
                            <div class="btn-group position-static text-nowrap">
                                <select class="form-select" name="user_id">
                                    <option selected="" value="" selected>All</option>
                                    @foreach ($users as $uId => $user)
                                    <option @selected(Request::get('user_id')==$uId ) value="{{$uId}}">{{$user}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <button type="submit" class="rounded btn btn-info flex-shrink-0">Filter</button>
                            <a href="{{ route('tnx.index') }}" class="rounded btn btn-warning flex-shrink-0">Reset</a>
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
                                <th class="white-space-nowrap align-middle py-4 text-start">NAME</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">ORDER</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">REF#</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">NAME</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">EMAIL</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">MOBILE</th>
                                <th class="white-space-nowrap align-middle py-4 text-center">GATEWAY</th>
                                <th class="white-space-nowrap align-middle py-4 text-center">ENV</th>
                                <th class="white-space-nowrap align-middle py-4 text-center">AMOUNT</th>
                                <th class="white-space-nowrap align-middle py-4 text-center">STATUS</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">REDIRECT URL</th>
                                <th class="white-space-nowrap align-middle py-4 text-start">CALLBACK URL</th>
                                <th class="white-space-nowrap align-middle py-4 text-end">CREATED TIME</th>
                                <th class="white-space-nowrap align-middle py-4 text-end">UPDATED TIME</th>
                            </tr>
                        </thead>

                        <tbody class="list" id="order-table-body">
                            @foreach ($tnxs as $tnx)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->user->name }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3">
                                    <h6 class="mb-0">{{ $tnx->order_id }}</h6>
                                </td>

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->reference_id }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->payer_name }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->payer_email }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->payer_mobile }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3 text-center">
                                    <h6 class="mb-0">{{ strtoupper($tnx->gateway) }}</h6>
                                </td>

                                <td class="align-middle white-space-nowrap py-3 text-center">
                                    <h6 class="mb-0">{{ strtoupper($tnx->env) }}</h6>
                                </td>

                                <td class="align-middle white-space-nowrap py-3 text-center">
                                    <h6 class="mb-0">₹{{ $tnx->amount }}</h6>
                                </td>

                                <td class="align-middle white-space-nowrap py-3 text-center">
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

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->redirect_url }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3">
                                    {{ $tnx->callback_url }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3 text-end">
                                    {{ $tnx->created_at }}
                                </td>

                                <td class="align-middle white-space-nowrap py-3 text-end">
                                    {{ $tnx->updated_at }}
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
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
<script src="{{ asset('public/admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
@endsection