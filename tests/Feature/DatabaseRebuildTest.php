<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseRebuildTest extends TestCase
{
    public function test_database_can_be_rebuilt_seeded_and_rolled_back_from_scratch(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));

        $this->artisan('migrate:fresh', ['--seed' => true, '--no-interaction' => true])->assertSuccessful();

        $this->assertDatabaseCount('departments', 4);
        $this->assertDatabaseCount('courses', 8);
        $this->assertDatabaseCount('students', 30);
        $this->assertDatabaseCount('course_student', 90);
        $this->assertSame('departments', (new Department)->getTable());
        $this->assertSame('courses', (new Course)->getTable());
        $this->assertSame('students', (new Student)->getTable());
        $this->assertSame('course_student', (new Student)->courses()->getTable());
        $this->assertSame('course_student', (new Course)->students()->getTable());

        foreach (Student::with('department', 'courses')->get() as $student) {
            $this->assertNotNull($student->department);
            $this->assertCount(3, $student->courses);
            $this->assertMatchesRegularExpression('/^09\d{9}$/', $student->phone);
            foreach ($student->courses as $course) {
                $grade = $course->pivot->grade;
                $this->assertContains($grade === null ? null : (float) $grade, [null, 1.00, 1.25, 1.50, 1.75, 2.00, 2.50, 3.00]);
            }
        }

        $this->artisan('migrate:reset', ['--no-interaction' => true])->assertSuccessful();
        foreach (['departments', 'courses', 'students', 'course_student'] as $table) {
            $this->assertFalse(Schema::hasTable($table));
        }
    }
}
