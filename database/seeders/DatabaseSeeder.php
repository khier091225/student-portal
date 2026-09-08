<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Student;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Departments first (students need them)
        $this->call(DepartmentSeeder::class);
 
        // 2. Courses
        $courses = Course::factory(8)->create();
 
        // 3. Students, each enrolled in 3 random courses with a random grade
        Student::factory(30)->create()->each(function (Student $student) use ($courses) {
            $picked = $courses->random(3);
            foreach ($picked as $course) {
                $student->courses()->attach($course->id, [
                    'grade' => fake()->randomElement([1.00, 1.25, 1.50, 1.75, 2.00, 2.50, 3.00, null]),
                ]);
            }
        });

    }
}
