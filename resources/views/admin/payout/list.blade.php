@extends('admin.layouts.app')

@section('title','Payout List')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Payout</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Payout List</h2>
        </div>
    </div>
    <div class="mb-4">
        <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <div>
                    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('payout.index') }}">
                                <span>All </span>
                                <span class="text-body-tertiary fw-semibold">({{$total}})</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('payout.index',['status' => 'pending']) }}">
                                <span>Pending</span>
                                <span class="text-body-tertiary fw-semibold">({{$pendingTotal}})</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('payout.index',['status' => 'success']) }}">
                                <span>Success</span>
                                <span class="text-body-tertiary fw-semibold">({{$completedTotal}})</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('payout.index',['status' => 'failed']) }}">
                                <span>Failed</span>
                                <span class="text-body-tertiary fw-semibold">({{$failedTotal}})</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="ms-xxl-auto">
                    <a class="btn btn-primary" href="{{ route('payout.create') }}">
                        <span class="fas fa-plus me-2"></span>
                        Create Payout
                    </a>
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
                        <th class="py-4 text-start">UUID</th>
                        <th class="py-4 text-start">Order#</th>
                        <th class="p-4">Amount</th>
                        <th class="py-4 text-start">Account No.</th>
                        <th class="py-4 text-start">IFSC</th>
                        <th class="py-4 text-start">Holder Name</th>
                        <th class="py-4 text-start">Bank</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Created At</th>
                        <th class="p-4">Send</th>
                        <th class="p-4">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payouts as $payout)
                    <tr class="align-middle">
                        <td>{{ $payout->id }}</td>
                        <td class="text-start">{{ $payout->uuid }}</td>
                        <td class="text-start">{{ $payout->order_id }}</td>
                        <td>{{ $payout->amount }}</td>
                        <td class="text-start">{{ $payout->account_number }}</td>
                        <td class="text-start">{{ $payout->ifsc_code }}</td>
                        <td class="text-start">{{ $payout->holder_name }}</td>
                        <td class="text-start">{{ $payout->bank_name }}</td>
                        <td>
                            @if($payout->status == 'success')
                            <span class="badge badge-phoenix fs-10 badge-phoenix-success">
                                <span class="badge-label">Success</span>
                                <span class="ms-1" data-feather="check"></span>
                            </span>
                            @elseif($payout->status == 'pending')
                            <span class="badge badge-phoenix fs-10 badge-phoenix-warning">
                                <span class="badge-label">Pending</span>
                                <span class="ms-1" data-feather="alert-octagon"></span>
                            </span>
                            @elseif($payout->status == 'failed')
                            <span class="badge badge-phoenix fs-10 badge-phoenix-danger">
                                <span class="badge-label">Failed</span>
                                <span class="ms-1" data-feather="alert-octagon"></span>
                            </span>
                            @endif
                        </td>
                        <td>{{ Carbon\Carbon::parse($payout->created_at)->format('d-m-Y H:i A') }}</td>
                        <td>
                            @if ($payout->status == 'pending' && $payout->response == null)
                            <form action="{{ route('payout.send') }}" method="post">
                                @csrf
                                <input type="hidden" name="payout_id" value="{{ $payout->id }}" />
                                <button type="submit" class="btn btn-success btn-sm">
                                    <span class="fas fa-paper-plane"></span>
                                </button>
                            </form>
                            @endif
                        </td>
                        <td>
                            @if ($payout->status == 'pending' && $payout->response == null)
                            <a class="btn btn-info btn-sm" href="{{ route('payout.edit', $payout->id) }}" title="Edit Payout">
                                <span class="fas fa-edit"></span>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($payouts->hasPages())
        <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                {!! $payouts->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')

@endsection