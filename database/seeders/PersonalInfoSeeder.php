<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalInfo;
use App\Models\LearningObjective;
use App\Models\Education;
use App\Models\WorkExperience;
use App\Models\AcademicAward;
use App\Models\CommunityAward;
use App\Models\WorkAward;
use App\Models\CreativeWork;
use App\Models\LifelongLearning;
use App\Models\Essay;

class PersonalInfoSeeder extends Seeder
{
    public function run()
    {
        PersonalInfo::factory()->count(10)->create()->each(function ($personalInfo) {
            // Create related records
            $personalInfo->learningObjective()->create([
                'firstPriority' => 'Learn PHP',
                'secondPriority' => 'Learn Laravel',
                'thirdPriority' => 'Build a web application',
                'goalStatement' => 'To become a proficient web developer.',
                'timeCommitment' => '10 hours per week',
                'overseasPlan' => 'Attend a coding bootcamp in the USA',
                'costPayment' => 'Self-funded',
                'completionTimeline' => '6 months',
            ]);

            $personalInfo->education()->createMany([
                [
                    'type' => 'elementary',
                    'school_name' => 'Elementary School A',
                    'address' => '123 Main St',
                    'date_from' => '2000-01-01',
                    'date_to' => '2006-01-01',
                    'has_diploma' => true,
                ],
                [
                    'type' => 'high_school',
                    'school_name' => 'High School B',
                    'address' => '456 Elm St',
                    'date_from' => '2006-01-01',
                    'date_to' => '2010-01-01',
                    'has_diploma' => true,
                ],
                [
                    'type' => 'post_secondary',
                    'school_name' => 'University C',
                    'address' => '789 Oak St',
                    'date_from' => '2010-01-01',
                    'date_to' => '2014-01-01',
                    'has_diploma' => true,
                ],
            ]);

            $personalInfo->workExperiences()->createMany([
                [
                    'designation' => 'Intern',
                    'companyName' => 'Company X',
                    'companyAddress' => '101 Pine St',
                    'dateFrom' => '2014-06-01',
                    'dateTo' => '2014-12-01',
                    'employmentStatus' => 'Completed',
                    'supervisorName' => 'John Doe',
                    'reasonForLeaving' => 'Internship completed',
                    'responsibilities' => 'Assisted in project development.',
                ],
            ]);

            $personalInfo->academicAwards()->createMany([
                [
                    'applicant_id' => $personalInfo->applicant_id,
                    'title' => 'Best Student',
                    'institution' => 'School A',
                    'dateReceived' => '2010-05-01',
                    'description' => 'Awarded for outstanding academic performance.',
                    'document' => null,
                ],
                [
                    'applicant_id' => $personalInfo->applicant_id,
                    'title' => 'Excellence in Mathematics',
                    'institution' => 'School B',
                    'dateReceived' => '2012-05-01',
                    'description' => 'Recognized for exceptional skills in mathematics.',
                    'document' => null,
                ],
            ]);

            $personalInfo->communityAwards()->createMany([
                [
                    'title' => 'Volunteer of the Year',
                    'organization' => 'Community Center',
                    'dateAwarded' => '2015-12-01',
                ],
            ]);

            $personalInfo->workAwards()->createMany([
                [
                    'title' => 'Employee of the Month',
                    'organization' => 'Company X',
                    'dateAwarded' => '2014-11-01',
                ],
            ]);

            $personalInfo->creativeWorks()->createMany([
                [
                    'title' => 'My First App',
                    'description' => 'A simple web application.',
                    'significance' => 'Learned a lot about web development.',
                    'date_completed' => '2015-01-01',
                    'corroborating_body' => 'Self',
                ],
            ]);

            $personalInfo->lifelongLearning()->createMany([
                [
                    'type' => 'skill',
                    'description' => 'Learning JavaScript',
                ],
            ]);

            $personalInfo->essay()->create([
                'content' => 'This is a sample essay content.',
            ]);
        });
    }
} 