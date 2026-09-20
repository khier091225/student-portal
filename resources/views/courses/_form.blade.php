@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-3">
        <label for="code" class="form-label">Code</label>
        <input id="code" name="code" class="form-control" maxlength="12" required
               value="{{ old('code', $course->code ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="title" class="form-label">Title</label>
        <input id="title" name="title" class="form-control" maxlength="255" required
               value="{{ old('title', $course->title ?? '') }}">
    </div>
    <div class="col-md-3">
        <label for="units" class="form-label">Units</label>
        <input type="number" id="units" name="units" class="form-control" min="0" max="255" step="1" required
               value="{{ old('units', $course->units ?? 3) }}">
    </div>
</div>
