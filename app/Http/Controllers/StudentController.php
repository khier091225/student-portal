<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('department')->orderBy('last_name')->paginate(10);
        return view('students.index', compact('students'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('students.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_number' => 'required|string|max:20|unique:students',
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'required|string|max:60',
            'email'          => 'required|email|unique:students',
            'birth_date'     => 'required|date',
            'year_level'     => 'required|integer|between:1,4',
            'department_id'  => 'required|exists:departments,id',
        ]);
 
        Student::create($data);
 
        return redirect()->route('students.index')->with('success', 'Student added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student->load('department', 'courses');
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $departments = Department::orderBy('name')->get();
        return view('students.edit', compact('student', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'student_number' => 'required|string|max:20|unique:students,student_number,'
                                . $student->id,
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'required|string|max:60',
            'email'          => 'required|email|unique:students,email,'.$student->id,
            'birth_date'     => 'required|date',
            'year_level'     => 'required|integer|between:1,4',
            'department_id'  => 'required|exists:departments,id',
        ]);
 
        $student->update($data);
 
        return redirect()->route('students.index')->with('success', 'Student updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }
}
