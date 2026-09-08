<?php
 
namespace Database\Factories;
 
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_number' => fake()->unique()->numerify('2026-#####'),
            'first_name'     => fake()->firstName(),
            'last_name'      => fake()->lastName(),
            'email'          => fake()->unique()->safeEmail(),
            'birth_date'     => fake()->dateTimeBetween('-24 years', '-17 years'),
            'year_level'     => fake()->numberBetween(1, 4),
            'department_id'  => Department::inRandomOrder()->first()->id,
        ];
    }
}
