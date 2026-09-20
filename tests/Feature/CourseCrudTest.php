<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourseCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_courses_in_code_order_with_pagination(): void
    {
        foreach (range(11, 1) as $number) {
            Course::factory()->create(['code' => sprintf('CS%03d', $number)]);
        }

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSeeInOrder(['CS001', 'CS002', 'CS010'])
            ->assertDontSee('CS011');
        $this->get(route('courses.index', ['page' => 2]))->assertOk()->assertSee('CS011');
    }

    public function test_empty_course_and_enrollment_lists_are_displayed(): void
    {
        $this->get(route('courses.index'))->assertOk()->assertSee('No courses yet.');

        $course = Course::factory()->create();
        $this->get(route('courses.show', $course))->assertOk()->assertSee('No students enrolled yet.');
    }

    public function test_course_can_be_created_and_updated_without_changing_its_code(): void
    {
        $data = ['code' => 'CS101', 'title' => 'Introduction to Computing', 'units' => 3];

        $this->get(route('courses.create'))->assertOk()->assertSee('name="code"', false);
        $this->post(route('courses.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success', 'Course added.');
        $this->assertDatabaseHas('courses', $data);

        $course = Course::sole();
        $this->get(route('courses.edit', $course))->assertOk()->assertSee('value="CS101"', false);
        $data['title'] = 'Updated Computing';
        $data['units'] = 4;

        $this->put(route('courses.update', $course), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success', 'Course updated.');
        $this->assertDatabaseHas('courses', ['id' => $course->id, ...$data]);
    }

    public static function invalidFields(): array
    {
        return [
            'missing code' => ['code', ''],
            'long code' => ['code', str_repeat('C', 13)],
            'missing title' => ['title', ''],
            'long title' => ['title', str_repeat('T', 256)],
            'missing units' => ['units', ''],
            'fractional units' => ['units', 1.5],
            'negative units' => ['units', -1],
            'overflow units' => ['units', 256],
        ];
    }

    #[DataProvider('invalidFields')]
    public function test_invalid_fields_are_rejected_on_create_and_update(string $field, mixed $value): void
    {
        $course = Course::factory()->create();
        $original = $course->only(['code', 'title', 'units']);
        $data = ['code' => 'NEW101', 'title' => 'New Course', 'units' => 3];
        $data[$field] = $value;

        $this->post(route('courses.store'), $data)->assertSessionHasErrors($field);
        $this->put(route('courses.update', $course), $data)->assertSessionHasErrors($field);

        $this->assertDatabaseCount('courses', 1);
        $this->assertDatabaseHas('courses', ['id' => $course->id, ...$original]);
    }

    public function test_duplicate_codes_are_rejected_on_create_and_update(): void
    {
        $existing = Course::factory()->create(['code' => 'CS101']);
        $course = Course::factory()->create(['code' => 'CS102']);
        $data = $existing->only(['code', 'title', 'units']);

        $this->post(route('courses.store'), $data)->assertSessionHasErrors('code');
        $this->put(route('courses.update', $course), $data)->assertSessionHasErrors('code');

        $this->assertDatabaseCount('courses', 2);
        $this->assertSame('CS102', $course->refresh()->code);
    }

    public function test_invalid_submission_keeps_form_input_and_displays_errors(): void
    {
        $this->from(route('courses.create'))
            ->post(route('courses.store'), ['code' => 'CS101', 'title' => 'Retained title', 'units' => -1])
            ->assertRedirect(route('courses.create'));

        $this->get(route('courses.create'))
            ->assertOk()
            ->assertSee('value="Retained title"', false)
            ->assertSee('alert-danger');
    }

    public function test_show_lists_only_enrolled_students_with_their_own_grades(): void
    {
        Department::create(['name' => 'Computer Studies', 'code' => 'CS']);
        $graded = Student::factory()->create(['first_name' => 'Anna', 'last_name' => 'Alpha']);
        $ungraded = Student::factory()->create(['first_name' => 'Ben', 'last_name' => 'Beta']);
        $outsider = Student::factory()->create();
        $course = Course::factory()->create();
        $course->students()->attach($ungraded, ['grade' => null]);
        $course->students()->attach($graded, ['grade' => 1.00]);
        $otherCourse = Course::factory()->create();
        $otherCourse->students()->attach($graded, ['grade' => 2.50]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSeeInOrder([$graded->student_number, 'Anna Alpha', '1.00', $ungraded->student_number, 'Ben Beta', 'No grade yet'])
            ->assertDontSee($outsider->student_number)
            ->assertDontSee('2.50');
    }

    public function test_deleting_a_course_removes_its_enrollments_but_preserves_students_and_other_courses(): void
    {
        Department::create(['name' => 'Computer Studies', 'code' => 'CS']);
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $student->courses()->attach([$course->id, $otherCourse->id], ['grade' => 1.00]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success', 'Course deleted.');

        $this->assertModelMissing($course);
        $this->assertModelExists($student);
        $this->assertModelExists($otherCourse);
        $this->assertDatabaseMissing('course_student', ['course_id' => $course->id]);
        $this->assertDatabaseHas('course_student', ['course_id' => $otherCourse->id, 'student_id' => $student->id]);
    }

    public function test_missing_courses_return_not_found(): void
    {
        $this->get(route('courses.show', 999))->assertNotFound();
        $this->get(route('courses.edit', 999))->assertNotFound();
        $this->put(route('courses.update', 999), [])->assertNotFound();
        $this->delete(route('courses.destroy', 999))->assertNotFound();
    }
}
