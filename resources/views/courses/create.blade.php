@extends('layouts.app')

@section('content')
<h1>Add Course</h1>
<form action="{{ route('courses.store') }}" method="POST" class="card card-body">
    @csrf
    @include('courses._form')
    <div class="mt-3">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('courses.index') }}" class="btn btn-link">Cancel</a>
    </div>
</form>
@endsection
