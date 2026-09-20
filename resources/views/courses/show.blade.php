@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ $course->code }} — {{ $course->title }}</h1>
    <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-secondary">Edit</a>
</div>
<p class="text-muted">Units: {{ $course->units }}</p>

<h2>Enrolled Students</h2>
<table class="table bg-white">
    <thead><tr><th>Student No.</th><th>Name</th><th>Year</th><th>Grade</th></tr></thead>
    <tbody>
    @forelse ($students as $student)
        <tr>
            <td>{{ $student->student_number }}</td>
            <td>{{ $student->full_name }}</td>
            <td>{{ $student->year_level }}</td>
            <td>{{ $student->pivot->grade !== null ? number_format((float) $student->pivot->grade, 2) : 'No grade yet' }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">No students enrolled yet.</td></tr>
    @endforelse
    </tbody>
</table>
<a href="{{ route('courses.index') }}" class="btn btn-secondary">Back</a>
@endsection
