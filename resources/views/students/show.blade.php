@extends('layouts.app')
 
@section('content')
<h1>{{ $student->full_name }}</h1>
<p class="text-muted">{{ $student->student_number }} · {{ $student->department->name }} · Year {{ $student->year_level }}</p>
<p>Email: {{ $student->email }} · Age: {{ $student->birth_date->age }}</p>
 
<h4>Enrolled Courses</h4>
<table class="table bg-white">
    <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Grade</th></tr></thead>
    <tbody>
    @foreach ($student->courses as $course)
        <tr>
            <td>{{ $course->code }}</td>
            <td>{{ $course->title }}</td>
            <td>{{ $course->units }}</td>
            <td>{{ $course->pivot->grade ?? 'No grade yet' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
@endsection
