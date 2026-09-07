<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = ['student_number', 'first_name', 'last_name', 'email', 'birth_date', 'year_level', 'department_id'];
    protected $casts = ['birth_date' => 'date'];

    // A student belongs to one department
    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }

    // A student is enrolled in many courses (through course_student)
    public function courses(): BelongsToMany {
        return $this->belongsToMany(Course::class)->withPivot('grade')->withTimestamps();
    }

    // Handy Helper: full name
    public function getFullnameAttribute(): string {
        return "{$this->first_name} {$this->last_name}";
    }
}
