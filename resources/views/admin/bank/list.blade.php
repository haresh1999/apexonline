@extends('admin.layouts.app')

@section('title','Bank Info List')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Bank Info List</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Bank Info List</h2>
        </div>
    </div>
    <div class="mb-4">
        <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <div>
                    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('bank.index') }}">
                                <span>All </span>
                                <span class="text-body-tertiary fw-semibold">({{$total}})</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="ms-xxl-auto">
                    <a class="btn btn-primary" href="{{ route('bank.create') }}">
                        <span class="fas fa-plus me-2"></span>
                        Add New Bank
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
                        <th class="white-space-nowrap p-4">ID</th>
                        <th class="white-space-nowrap py-4 text-start">Account No.</th>
                        <th class="white-space-nowrap py-4 text-start">IFSC</th>
                        <th class="white-space-nowrap py-4 text-start">Holder Name</th>
                        <th class="white-space-nowrap py-4 text-start">Bank</th>
                        <th class="white-space-nowrap p-4">Created At</th>
                        <th class="white-space-nowrap p-4">Send Payment</th>
                        <th class="white-space-nowrap p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($banks as $key => $bank)
                    <tr class="align-middle">
                        <td>{{ ++$key }}</td>
                        <td class="text-start">{{ $bank->account_number }}</td>
                        <td class="text-start">{{ $bank->ifsc_code }}</td>
                        <td class="text-start">{{ $bank->holder_name }}</td>
                        <td class="text-start">{{ $bank->bank_name }}</td>
                        <td>{{ Carbon\Carbon::parse($bank->created_at)->format('d-m-Y H:i A') }}</td>
                        <td>
                            <a class="btn btn-secondary btn-sm" href="{{ route('payout.create', $bank->id) }}" title="Edit Payout">
                                <span class="fas fa-paper-plane"></span>
                            </a>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <form action="{{ route('bank.destroy',$bank->id) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <a class="btn btn btn-info btn-sm" href="{{ route('bank.edit',$bank->id) }}">
                                        <span class="fas fa-edit"></span>
                                    </a>
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <span class="fas fa-trash-alt"></span>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($banks->hasPages())
        <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                {!! $banks->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')

@endsection