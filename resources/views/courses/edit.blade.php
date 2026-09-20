@extends('layouts.app')

@section('content')
<h1>Edit Course</h1>
<form action="{{ route('courses.update', $course) }}" method="POST" class="card card-body">
    @csrf
    @method('PUT')
    @include('courses._form')
    <div class="mt-3">
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('courses.index') }}" class="btn btn-link">Cancel</a>
    </div>
</form>
@endsection
