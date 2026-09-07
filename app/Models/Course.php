<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    protected $fillable = ['code', 'title', 'units'];

    public function students(): BelongsToMany {
        return $this->belongsToMany(Student::class)->withPivot('grade')->withTimestamps();
    }
}
