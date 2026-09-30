@extends('admin.layouts.app')

@section('title','Course Edit')

@section('style')

@endsection

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('course.index') }}">Course</a>
        </li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>
<div class="row g-4">
    <div class="col-12 col-xl-6 order-1 order-xl-0">
        <div class="mb-0">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0">Update Course</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('course.update',$course->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="name">Course Name</label>
                                <input class="form-control no-arrow" type="text" name="name" id="name" value="{{ old('name',$course->name) }}" />
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body required" for="subject">Subject</label>
                                <input class="form-control" type="text" name="subject" id="subject" value="{{ old('subject',$course->subject) }}" />
                                @error('subject')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="ps-0 form-label text-body required" for="min_amt">Min Amount</label>
                                <input class="form-control no-arrow" type="number" name="min_amt" id="min_amt" value="{{ old('min_amt',$course->min_amt) }}" />
                                @error('min_amt')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="ps-0 form-label text-body required" for="max_amt">Max Amount</label>
                                <input class="form-control no-arrow" type="number" name="max_amt" id="max_amt" value="{{ old('max_amt',$course->max_amt) }}" />
                                @error('max_amt')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="ps-0 form-label text-body" for="pdf">Course PDF</label>
                                <input class="form-control mb-2" type="file" name="pdf" id="pdf" value="{{ old('pdf') }}" />
                                @if ($course->pdf_path)
                                <a href="{{ getFile($course->pdf_path) }}">
                                    <span class="fas fa-eye"></span>
                                    {{ $course->pdf }}
                                </a>
                                @endif
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