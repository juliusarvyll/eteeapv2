<?php

namespace Database\Seeders;

use App\Models\PersonalInfo;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    protected $courses = [
        'Bachelor of Science in Information Technology',
        'Bachelor of Science in Computer Science',
        'Bachelor of Science in Business Administration',
        'Bachelor of Arts in Communication',
        'Bachelor of Science in Psychology'
    ];

    protected $subjects = [
        'IT' => [
            ['name' => 'Programming Fundamentals', 'units' => 3],
            ['name' => 'Database Management', 'units' => 3],
            ['name' => 'Web Development', 'units' => 3],
            ['name' => 'Network Security', 'units' => 3],
        ],
        'CS' => [
            ['name' => 'Data Structures', 'units' => 3],
            ['name' => 'Algorithms', 'units' => 3],
            ['name' => 'Operating Systems', 'units' => 3],
            ['name' => 'Software Engineering', 'units' => 3],
        ],
        'BA' => [
            ['name' => 'Principles of Management', 'units' => 3],
            ['name' => 'Financial Accounting', 'units' => 3],
            ['name' => 'Marketing Management', 'units' => 3],
            ['name' => 'Business Ethics', 'units' => 3],
        ],
        'COMM' => [
            ['name' => 'Mass Communication', 'units' => 3],
            ['name' => 'Public Speaking', 'units' => 3],
            ['name' => 'Media Writing', 'units' => 3],
            ['name' => 'Digital Media Production', 'units' => 3],
        ],
        'PSYCH' => [
            ['name' => 'General Psychology', 'units' => 3],
            ['name' => 'Developmental Psychology', 'units' => 3],
            ['name' => 'Abnormal Psychology', 'units' => 3],
            ['name' => 'Research Methods', 'units' => 3],
        ],
    ];

    protected $schedules = [
        'Monday/Wednesday 8:00 AM - 9:30 AM',
        'Tuesday/Thursday 10:00 AM - 11:30 AM',
        'Monday/Wednesday 1:00 PM - 2:30 PM',
        'Tuesday/Thursday 3:00 PM - 4:30 PM',
        'Friday 8:00 AM - 11:00 AM'
    ];

    public function run(): void
    {
        // Get all approved PersonalInfo records
        PersonalInfo::where('status', 'approved')->each(function ($personalInfo) {
            // Randomly select a course
            $course = $this->courses[array_rand($this->courses)];

            // Determine course code
            $courseCode = match(true) {
                str_contains($course, 'Information Technology') => 'IT',
                str_contains($course, 'Computer Science') => 'CS',
                str_contains($course, 'Business Administration') => 'BA',
                str_contains($course, 'Communication') => 'COMM',
                str_contains($course, 'Psychology') => 'PSYCH',
                default => 'IT'
            };

            // Get subjects for the course
            $courseSubjects = $this->subjects[$courseCode];

            // Create 4 subjects for each personal info
            foreach ($courseSubjects as $subject) {
                Subject::create([
                    'applicant_id' => $personalInfo->applicant_id,
                    'course_name' => $course,
                    'subject_name' => $subject['name'],
                    'units' => $subject['units'],
                    'schedule' => $this->schedules[array_rand($this->schedules)],
                ]);
            }
        });
    }
}
