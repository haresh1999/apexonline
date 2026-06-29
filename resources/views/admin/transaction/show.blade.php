@extends('admin.layouts.app')

@section('title','Order')

@section('style')
<style>
    .label {
        width: 150px;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('tnx.index') }}">Sales & Transactions</a>
        </li>
        <li class="breadcrumb-item active">Sales Details</li>
    </ol>
</nav>
<h2 class="mb-2 lh-sm">Sales Details</h2>
<p class="text-body-tertiary lead mb-2">View details of the selected sale.</p>
<div class="row g-4">
    <div class="col-12 col-xl-6 offset-3 order-1 order-xl-0">
        <div class="mb-0">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">Sales Details</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group">

                        <li class="list-group-item d-flex">
                            <span class="label">User ID:</span>
                            <b class="value">{{ $tnxs->user->name }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Order ID:</span>
                            <b class="value">{{ $tnxs->mr_order_id }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Payer Name:</span>
                            <b class="value">{{ $tnxs->payer_name }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Payer Email:</span>
                            <b class="value">{{ $tnxs->payer_email }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Payer Mobile:</span>
                            <b class="value">{{ $tnxs->payer_mobile }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Status:</span>
                            <div class="value">
                                @if($tnxs->status == 'completed')
                                <span class="badge badge-phoenix badge-phoenix-success">{{ ucfirst($tnxs->status) }}</span>
                                @elseif($tnxs->status == 'pending')
                                <span class="badge badge-phoenix badge-phoenix-warning">{{ ucfirst($tnxs->status) }}</span>
                                @else
                                <span class="badge badge-phoenix badge-phoenix-danger">{{ ucfirst($tnxs->status) }}</span>
                                @endif
                            </div>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Gateway:</span>
                            <b class="value">{{ strtoupper($tnxs->gateway) }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Environment:</span>
                            <b class="value">{{ strtoupper($tnxs->env) }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Amount:</span>
                            <b class="value">₹{{ number_format($tnxs->amount ?? 0, 2) }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Reference ID:</span>
                            <b class="value">{{ $tnxs->reference_id }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Redirect URL:</span>
                            <div class="value text-break">
                                {{ $tnxs->redirect_url }}
                            </div>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Callback URL:</span>
                            <div class="value text-break">
                                {{ $tnxs->callback_url }}
                            </div>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Date Time:</span>
                            <b class="value">{{ $tnxs->created_at }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Last Updated:</span>
                            <b class="value">{{ $tnxs->updated_at }}</b>
                        </li>

                        <li class="list-group-item d-flex">
                            <span class="label">Action:</span>
                            <div>
                                <form action="{{ route('tnx.update', $tnxs->id) }}" method="POST">
                                    @csrf

                                    <div class="input-group">
                                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="">Select Status</option>
                                            <option value="completed" {{ old('status', $tnxs->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="failed" {{ old('status', $tnxs->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                            <option value="refunded" {{ old('status', $tnxs->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            <option value="processing" {{ old('status', $tnxs->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="pending" {{ old('status', $tnxs->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Update Status
                                        </button>
                                    </div>

                                    @error('status')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection