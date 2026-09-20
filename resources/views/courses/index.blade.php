@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Courses</h1>
    <a href="{{ route('courses.create') }}" class="btn btn-primary">+ Add Course</a>
</div>

<table class="table table-striped bg-white">
    <thead><tr><th>Code</th><th>Title</th><th>Units</th><th></th></tr></thead>
    <tbody>
    @forelse ($courses as $course)
        <tr>
            <td><a href="{{ route('courses.show', $course) }}">{{ $course->code }}</a></td>
            <td>{{ $course->title }}</td>
            <td>{{ $course->units }}</td>
            <td class="text-end">
                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this course and its enrollments?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">No courses yet.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $courses->links() }}
@endsection
