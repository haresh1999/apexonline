@extends('admin.layouts.app')

@section('title','Course')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Course</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Course</h2>
        </div>
    </div>
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
                            <button type="submit" class="rounded btn btn-info flex-shrink-0">Filter</button>
                            <a href="{{ route('company.index') }}" class="rounded btn btn-warning flex-shrink-0">Reset</a>
                        </div>
                    </div>
                    <div class="ms-xxl-auto">
                        <a class="btn btn-primary" href="{{ route('course.create') }}">
                            <span class="fas fa-plus me-2"></span>
                            Add New Course
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
                            <th class="p-4">Id</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">Min Amount</th>
                            <th class="p-4">Max Amount</th>
                            <th class="p-4">Course PDF</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $key => $course)
                        <tr class="align-middle">
                            <td>{{ ++$key }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->subject }}</td>
                            <td>{{ $course->min_amt }}</td>
                            <td>{{ $course->max_amt }}</td>
                            <td>
                                <a href="{{ getFile($course->pdf_path) }}">
                                    <span class="fas fa-eye"></span>
                                    {{ $course->pdf }}
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <form action="{{ route('course.destroy',$course->id) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <a class="btn btn btn-info btn-sm" href="{{ route('course.edit',$course->id) }}">
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
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection