<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Department::create(['name' => 'Computer Studies', 'code' => 'CCS']);
    }

    public function test_student_details_show_their_department_courses_and_grades(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        $student->courses()->attach($course, ['grade' => 1.25]);

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee($student->full_name)
            ->assertSee('Computer Studies')
            ->assertSee($course->code)
            ->assertSee('1.25');
    }

    public function test_deleting_a_student_removes_their_enrollments_and_preserves_other_records(): void
    {
        $student = Student::factory()->create();
        $otherStudent = Student::factory()->create();
        $course = Course::factory()->create();
        $course->students()->attach([$student->id, $otherStudent->id], ['grade' => 1.25]);

        $this->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success', 'Student deleted.');

        $this->assertModelMissing($student);
        $this->assertModelExists($course);
        $this->assertModelExists($otherStudent);
        $this->assertDatabaseMissing('course_student', ['student_id' => $student->id]);
        $this->assertDatabaseHas('course_student', ['student_id' => $otherStudent->id, 'course_id' => $course->id]);
    }

    public function test_missing_students_return_not_found(): void
    {
        $this->get(route('students.show', 999))->assertNotFound();
        $this->delete(route('students.destroy', 999))->assertNotFound();
    }
}
