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
<div class="mb-4">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Payout List</h2>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-between">
        <ul class="nav nav-links mb-lg-2 mx-n3">
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
        <div>
            <form action="{{ route('payout.export') }}" method="post">
                @csrf
                <button class="btn btn-warning" type="submit">
                    <span class="fas fa-file-export me-2"></span>
                    Export
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importPayoutModal">
                    <span class="fas fa-file-import me-2"></span>
                    Import
                </button>
                <a class="btn btn-primary" href="{{ route('payout.create') }}">
                    <span class="fas fa-plus me-2"></span>
                    Create Payout
                </a>
            </form>
        </div>
    </div>
</div>
<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
    <div class="table-responsive scrollbar mx-n1 px-1">
        <table class="table fs-9 mb-0 text-center">
            <thead>
                <tr>
                    <th class="white-space-nowrap p-4">ID</th>
                    <th class="white-space-nowrap py-4 text-start">Slip</th>
                    <th class="white-space-nowrap py-4 text-start">UUID</th>
                    <th class="white-space-nowrap py-4 text-start">Order#</th>
                    <th class="white-space-nowrap p-4">Amount</th>
                    <th class="white-space-nowrap py-4 text-start">Account No.</th>
                    <th class="white-space-nowrap py-4 text-start">IFSC</th>
                    <th class="white-space-nowrap py-4 text-start">Holder Name</th>
                    <th class="white-space-nowrap py-4 text-start">Bank</th>
                    <th class="white-space-nowrap p-4">Status</th>
                    <th class="white-space-nowrap p-4">Created At</th>
                    <th class="white-space-nowrap p-4">Send Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payouts as $payout)
                <tr class="align-middle">
                    <td>{{ $payout->id }}</td>
                    <td>
                        @if ($payout->status == 'success')
                        <a target="_blank" href="{{ route('payout.slip',$payout->id) }}" class="me-2 text-success">
                            <span class="fas fa-file-pdf"></span>
                        </a>
                        @endif
                    </td>
                    <td class="text-start">{{ $payout->uuid }}</td>
                    <td class="text-start">{{ strtoupper($payout->order_id) }}</td>
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
                            <button class="btn btn-secondary btn-sm" type="submit">
                                <span class="fas fa-paper-plane"></span>
                            </button>
                        </form>
                        @else
                        ---
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

<div class="modal fade" id="importPayoutModal" tabindex="-1" aria-labelledby="importPayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('payout.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importPayoutModalLabel">
                        <i class="fas fa-file-import me-2"></i>Import Payouts
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="payout_file" class="form-label ps-0">Choose File</label>
                    <input type="file" name="file" id="payout_file" class="form-control" accept=".csv,.xlsx,.xls" required>
                    <small class="text-muted">Allowed formats: CSV, XLSX, XLS.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection