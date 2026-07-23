@extends('admin.layouts.app')

@section('title','User List')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">User</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Users</h2>
        </div>
    </div>
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('user.index') }}">
                <span>All </span>
                <span class="text-body-tertiary fw-semibold">({{$users->total()}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.index',['status' => 'active']) }}">
                <span>Active </span>
                <span class="text-body-tertiary fw-semibold">({{$active}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.index',['status' => 'inactive']) }}">
                <span>Inactive </span>
                <span class="text-body-tertiary fw-semibold">({{$inactive}})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.index',['status' => 'trashed']) }}">
                <span>Trashed </span>
                <span class="text-body-tertiary fw-semibold">({{$deleted}})</span>
            </a>
        </li>
    </ul>
    <div>
        <div class="mb-4">
            <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
                <div class="d-flex flex-wrap gap-3">
                    <div class="search-box">
                        <input class="form-control search-input search" type="search" name="search" placeholder="Search anything..." value="{{ Request::get('search') }}" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </div>
                    <div class="scrollbar overflow-hidden-y">
                        <div class="btn-group position-static gap-2" role="group">
                            <div class="btn-group position-static text-nowrap">
                                <select class="form-select" name="status">
                                    <option selected="" value="">Status</option>
                                    <option @selected(Request::get('status')==='1' ) value="1">Active</option>
                                    <option @selected(Request::get('status')==='0' ) value="0">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="rounded btn btn-info flex-shrink-0">Filter</button>
                            <a href="{{ route('user.index') }}" class="rounded btn btn-warning flex-shrink-0">Reset</a>
                        </div>
                    </div>
                    <div class="ms-xxl-auto">
                        <a class="btn btn-primary" href="{{ route('user.create') }}">
                            <span class="fas fa-plus me-2"></span>
                            Add New User
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
                            <th class="py-4 text-start">Company</th>
                            <th class="py-4 text-start">Name</th>
                            <th class="py-4 text-start">Mobile</th>
                            <th class="py-4 text-start">Email</th>
                            <th class="py-4 text-start">Last Login</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                        <tr class="align-middle">
                            <td>{{ ++$key }}</td>
                            <td class="text-start">{{ $user->company->name }}</td>
                            <td class="text-start">{{ $user->name }}</td>
                            <td class="text-start">{{ $user->mobile }}</td>
                            <td class="text-start">{{ $user->email }}</td>
                            <td class="text-start">{{ $user->updated_at }}</td>
                            <td>
                                @if($user->status == 1)
                                <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                @else
                                <span class="badge badge-phoenix badge-phoenix-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <form onsubmit="return confirm('Are you sure want do this action?')" action="{{ route('user.destroy',$user->id) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        @if($user->deleted_at == null)
                                        <a class="btn btn btn-info btn-sm" href="{{ route('user.edit',$user->id) }}">
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
            @if($users->hasPages())
            <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    {!! $users->links() !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection