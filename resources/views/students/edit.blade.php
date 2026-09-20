@extends('layouts.app')
 
@section('content')
<h1>Edit Student</h1>
<form action="{{ route('students.update', $student) }}" method="POST" class="card card-body">
    @csrf
    @method('PUT')
    @include('students._form')
    <div class="mt-3">
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('students.index') }}" class="btn btn-link">Cancel</a>
    </div>
</form>
@endsection
