@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif
 
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Student Number</label>
        <input name="student_number" class="form-control"
               value="{{ old('student_number', $student->student_number ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">First Name</label>
        <input name="first_name" class="form-control"
               value="{{ old('first_name', $student->first_name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Last Name</label>
        <input name="last_name" class="form-control"
               value="{{ old('last_name', $student->last_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $student->email ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="tel" id="phone" name="phone" class="form-control" maxlength="20"
               value="{{ old('phone', $student->phone ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Birth Date</label>
        <input type="date" name="birth_date" class="form-control"
               value="{{ old('birth_date', isset($student) ? $student->birth_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Year Level</label>
        <select name="year_level" class="form-select">
            @foreach ([1, 2, 3, 4] as $y)
                <option value="{{ $y }}" @selected(old('year_level', $student->year_level ?? 1) == $y)>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Department</label>
        <select name="department_id" class="form-select">
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" @selected(old('department_id', $student->department_id ?? '') == $dept->id)>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
