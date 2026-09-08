<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'  => strtoupper(fake()->unique()->bothify('??###')), // CS101
            'title' => fake()->sentence(3),
            'units' => fake()->randomElement([2, 3]),
        ];
    }
}

