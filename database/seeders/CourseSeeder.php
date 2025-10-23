<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Emergency Services Operations',
                'name' => 'Emergency Services Operations',
                'description' => 'Comprehensive training in emergency services operations and procedures.',
                'price' => 199.00,
                'duration' => 'Full Day',
                'time' => '9:00 AM - 5:00 PM',
                'location' => 'ESO Training Center',
                'instructor' => 'John Smith',
                'capacity' => 20,
            ],
            [
                'title' => 'Advanced Emergency Response',
                'name' => 'Advanced Emergency Response',
                'description' => 'Advanced techniques for emergency response and crisis management.',
                'price' => 299.00,
                'duration' => '2 Days',
                'time' => '9:00 AM - 4:00 PM',
                'location' => 'ESO Training Center',
                'instructor' => 'Sarah Johnson',
                'capacity' => 15,
            ],
            [
                'title' => 'Emergency Communication Systems',
                'name' => 'Emergency Communication Systems',
                'description' => 'Training on emergency communication protocols and systems.',
                'price' => 149.00,
                'duration' => 'Half Day',
                'time' => '9:00 AM - 1:00 PM',
                'location' => 'ESO Training Center',
                'instructor' => 'Mike Wilson',
                'capacity' => 25,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}
