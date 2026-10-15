@extends('admin.layouts.app')

@section('title','PDF List')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">PDF</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">PDF List</h2>
        </div>
    </div>
    <div class="mb-4">
        <form class="position-relative" action="{{ Request::fullUrl() }}" method="get">
            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <div>
                    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('pdf.index') }}">
                                <span>All </span>
                                <span class="text-body-tertiary fw-semibold">({{$total}})</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="ms-xxl-auto">
                    <a class="btn btn-primary" href="{{ route('pdf.create') }}">
                        <span class="fas fa-plus me-2"></span>
                        Add PDF
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
                        <th class="p-4">PDF</th>
                        <th class="p-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pdfs as $key => $pdf)
                    <tr class="align-middle">
                        <td>{{ ++$key }}</td>
                        <td>{{ $pdf->name }}</td>
                        <td>
                            <a target="_blank" href="{{ getFile($pdf->path) }}">
                                <span class="fas fa-eye"></span>
                            </a>
                        </td>
                        <td>{{ Carbon\Carbon::parse($pdf->created_at)->format('d-m-Y H:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pdfs->hasPages())
        <div class="row align-items-center justify-content-end py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                {!! $pdfs->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')

@endsection