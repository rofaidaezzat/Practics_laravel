<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'name' => 'PHP',
                'code' => 'CS101',
                'description' => 'Introduction to PHP Programming and Web Development',
                'credits' => 3,
            ],
            [
                'name' => 'Laravel',
                'code' => 'CS102',
                'description' => 'Advanced Laravel Web Application Development',
                'credits' => 4,
            ],
            [
                'name' => 'Database',
                'code' => 'CS103',
                'description' => 'Relational Database Design and SQL with MySQL',
                'credits' => 3,
            ],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}