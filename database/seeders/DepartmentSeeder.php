<?php
 
namespace Database\Seeders;
 
use App\Models\Department;
use Illuminate\Database\Seeder;
 
class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Studies',       'code' => 'CCS'],
            ['name' => 'Business Administration','code' => 'CBA'],
            ['name' => 'Education',              'code' => 'COED'],
            ['name' => 'Engineering',            'code' => 'COE'],
        ];
 
        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
