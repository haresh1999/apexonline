@extends('admin.layouts.app')

@section('title','User List')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Button Gateway</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Button Gateways</h2>
        </div>
    </div>
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('bg.index') }}">
                <span>All </span>
                <span class="text-body-tertiary fw-semibold">({{$bgs->count()}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('bg.index',['status' => 1]) }}">
                <span>Active </span>
                <span class="text-body-tertiary fw-semibold">({{$active}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('bg.index',['status' => 0]) }}">
                <span>Inactive </span>
                <span class="text-body-tertiary fw-semibold">({{$inactive}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('bg.index',['status' => 'trashed']) }}">
                <span>Trashed </span>
                <span class="text-body-tertiary fw-semibold">({{$deleted}})</span>
            </a>
        </li>
    </ul>
    <div class="mb-4 w-100 text-end">
        <a class="btn btn-primary" href="{{ route('bg.create') }}">
            <span class="fas fa-plus me-2"></span>
            Add New Company
        </a>
    </div>
    <div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <table class="table fs-9 mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Gateway Name</th>
                            <th class="p-4">URL</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Last Updated</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bgs as $key => $bg)
                        <tr class="align-middle">
                            <td>{{ ++$key }}</td>
                            <td class="white-space-nowrap text-start">
                                <h6 class="mb-0">
                                    {{ strtoupper($bg->name) }}
                                </h6>
                            </td>
                            <td>
                                <a target="_blank" href="{{ $bg->url }}">
                                    <span class="fas fa-link"></span>
                                </a>
                            </td>
                            <td>
                                @if($bg->status == 1)
                                <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                @else
                                <span class="badge badge-phoenix badge-phoenix-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $bg->updated_at }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <form onsubmit="return confirm('Are you sure want do this action?')" action="{{ route('bg.destroy',$bg->id) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        @if($bg->deleted_at == null)
                                        <a class="btn btn btn-info btn-sm" href="{{ route('bg.edit',$bg->id) }}">
                                            <span class="fas fa-edit"></span>
                                        </a>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <span class="fas fa-trash-alt"></span>
                                        </button>
                                        @else
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <span class="fas fa-sync-alt"></span>
                                        </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- @if($bgs->hasPages())
            <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    {!! $bgs->links() !!}
                </div>
            </div>
            @endif --}}
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection