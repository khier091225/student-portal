<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $courses = Course::orderBy('code')->paginate(10);

        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|string|max:12|unique:courses',
            'title' => 'required|string|max:255',
            'units' => 'required|integer|between:0,255',
        ]);

        Course::create($data);

        return redirect()->route('courses.index')->with('success', 'Course added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): View
    {
        $students = $course->students()->orderBy('last_name')->orderBy('first_name')->get();

        return view('courses.show', compact('course', 'students'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:12', Rule::unique('courses')->ignore($course)],
            'title' => 'required|string|max:255',
            'units' => 'required|integer|between:0,255',
        ]);

        $course->update($data);

        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted.');
    }
}
