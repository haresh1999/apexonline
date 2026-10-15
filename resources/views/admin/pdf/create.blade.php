@extends('admin.layouts.app')

@section('title','Add New PDF')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pg.index') }}">PDF</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>
<div class="row g-4">
    <div class="col-12 col-xl-6 order-1 order-xl-0">
        <div class="mb-0">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">Add New PDF</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('pdf.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="name">Pdf Name</label>
                                <input class="form-control no-arrow" type="text" name="name" id="name" value="{{ old('name') }}" />
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="pdf">Pdf File</label>
                                <input class="form-control" type="file" name="pdf" id="pdf" />
                                @error('pdf')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer border-top-0 pe-0">
                            <div class="d-flex pager wizard list-inline mb-0">
                                <div class="flex-1 text-end">
                                    <button class="btn btn-primary px-6 px-sm-6" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection