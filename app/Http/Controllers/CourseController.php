<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::when($request->filled('search'), function ($q) {
            $q->where('name', 'like', "%" . request('name') . "%");
        })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.course.list', compact('courses'));
    }

    public function create()
    {
        return view('admin.course.create');
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'min_amt' => ['required', 'numeric', 'min:1'],
            'max_amt' => ['required', 'numeric', 'gt:min_amt'],
            'name' => ['required', 'string', 'max:255', 'unique:courses'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'subject' => ['required', 'string', 'max:255'],
        ]);

        $fileName = str()->slug($request->name, '-');

        $input['pdf_path'] = uploadFile($request->pdf, 'course', $fileName);

        Course::create($input);

        return redirect()->route('course.index')->with('res.success', 'Course created successful.');
    }

    public function edit(string $id)
    {
        $course = Course::findOrFail($id);

        return view('admin.course.edit', compact('course'));
    }

    public function update(Request $request, string $id)
    {
        $input = $request->validate([
            'min_amt' => ['required', 'numeric', 'min:1'],
            'max_amt' => ['required', 'numeric', 'gt:min_amt'],
            'name' => ['required', 'string', 'max:255', 'unique:courses,name,' . $id . ',id'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'subject' => ['required', 'string', 'max:255'],
        ]);

        $course = Course::find($id);

        if ($request->hasFile('pdf')) {

            $fileName = str()->slug($request->name, '-');

            $input['pdf_path'] = uploadFile($request->pdf, 'course', $fileName);

            Storage::disk('public')->delete($course->pdf);
        }

        $course->update($input);

        return redirect()->route('course.index')->with('res.success', 'Course updated successful.');
    }

    public function destroy(string $id)
    {
        $course = Course::find($id);

        Storage::disk('public')->delete($course->pdf_path);

        $course->delete();

        return redirect()->route('course.index')->with('res.success', 'Course deleted successful.');
    }
}
