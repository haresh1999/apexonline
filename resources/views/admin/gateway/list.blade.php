@extends('admin.layouts.app')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Gateways</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Gateways List</h2>
        </div>
    </div>
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('pg-index') }}">
                <span>All </span>
                <span class="text-body-tertiary fw-semibold">({{$total}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pg.index',['status' => 1]) }}">
                <span>Active </span>
                <span class="text-body-tertiary fw-semibold">({{$active}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pg.index',['status' => 0]) }}">
                <span>Inactive </span>
                <span class="text-body-tertiary fw-semibold">({{$inactive}})</span>
            </a>
        </li>
    </ul>
    <div>
        <div class="mb-4">
            <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input class="form-control search-input search" type="search" name="search" placeholder="Search anything..." value="{{ Request::get('search') }}" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </div>
                    <div class="scrollbar overflow-hidden-y">
                        <button type="submit" class="rounded btn btn-info flex-shrink-0">Filter</button>
                        <a href="{{ route('pg.index') }}" class="rounded btn btn-warning flex-shrink-0">Reset</a>
                    </div>
                    <div class="ms-xxl-auto">
                        <a class="btn btn-primary" href="{{ route('pg.create') }}">
                            <span class="fas fa-plus me-2"></span>
                            Add Gateway
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
                            <th class="p-4">Name</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Count</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Last Updated</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gateways as $key => $gateway)
                        <tr class="align-middle">
                            <td>{{ ++$key }}</td>
                            <td>{{ $gateway->name }}</td>
                            <td>{{ $gateway->slug }}</td>
                            <td>{{ $gateway->transactions_count }}</td>
                            <td>
                                @if($gateway->status == 1)
                                <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                @else
                                <span class="badge badge-phoenix badge-phoenix-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $gateway->updated_at }}</td>
                            <td>
                                <a class="btn btn btn-info btn-sm" href="{{ route('pg.edit',$gateway->id) }}">
                                    <span class="fas fa-edit"></span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($gateways->hasPages())
            <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    {!! $gateways->links() !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection