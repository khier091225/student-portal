<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StudentPhoneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Department::create(['name' => 'Computer Studies', 'code' => 'CS']);
    }

    public static function validPhones(): array
    {
        return [
            'leading zero' => ['09123456789'],
            'maximum length' => [str_repeat('1', 20)],
            'optional' => [null],
        ];
    }

    #[DataProvider('validPhones')]
    public function test_phone_can_be_saved_when_creating_a_student(?string $phone): void
    {
        $data = Student::factory()->raw(['phone' => $phone, 'birth_date' => '2005-01-02']);

        $this->post(route('students.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', ['email' => $data['email'], 'phone' => $phone]);
    }

    #[DataProvider('validPhones')]
    public function test_phone_can_be_updated_or_cleared(?string $phone): void
    {
        $student = Student::factory()->create(['phone' => '09999999999']);
        $data = $student->only($student->getFillable());
        $data['birth_date'] = $student->birth_date->format('Y-m-d');
        $data['phone'] = $phone ?? '';

        $this->put(route('students.update', $student), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('students.index'));

        $this->assertSame($phone, $student->refresh()->phone);
    }

    public function test_phone_longer_than_twenty_characters_is_rejected_on_create_and_update(): void
    {
        $data = Student::factory()->raw(['phone' => str_repeat('1', 21), 'birth_date' => '2005-01-02']);

        $this->post(route('students.store'), $data)->assertSessionHasErrors('phone');
        $this->assertDatabaseCount('students', 0);

        $student = Student::factory()->create(['phone' => '09123456789']);
        $this->put(route('students.update', $student), $data)->assertSessionHasErrors('phone');
        $this->assertSame('09123456789', $student->refresh()->phone);
    }

    public function test_phone_is_shown_in_the_forms_and_index(): void
    {
        $student = Student::factory()->create(['phone' => '09123456789']);

        $this->get(route('students.create'))->assertOk()->assertSee('name="phone"', false);
        $this->get(route('students.edit', $student))->assertOk()->assertSee('value="09123456789"', false);
        $this->get(route('students.index'))->assertOk()->assertSee('09123456789');
    }

    public function test_factory_generates_a_phone_with_the_required_format(): void
    {
        $student = Student::factory()->create();

        $this->assertMatchesRegularExpression('/^09\d{9}$/', $student->phone);
    }

    public function test_phone_migration_can_be_rolled_back_and_reapplied(): void
    {
        $migration = require database_path('migrations/2026_09_20_030234_add_phone_to_students_table.php');

        $this->assertTrue(Schema::hasColumn('students', 'phone'));
        $migration->down();
        $this->assertFalse(Schema::hasColumn('students', 'phone'));
        $migration->up();
        $this->assertTrue(Schema::hasColumn('students', 'phone'));
    }
}
