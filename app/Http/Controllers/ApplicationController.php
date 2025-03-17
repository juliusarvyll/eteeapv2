<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use App\Models\LearningObjective;
use App\Models\WorkExperience;
use App\Models\AcademicAward;
use App\Models\CommunityAward;
use App\Models\WorkAward;
use App\Models\Education;
use App\Models\HighSchool;
use App\Models\PostSecondaryEducation;
use App\Models\NonFormalEducation;
use App\Models\Certification;
use App\Models\CreativeWork;
use App\Models\LifelongLearning;
use App\Models\Essay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApplicationSubmitted;
use App\Models\User;
use App\Jobs\SendApplicationNotifications;
use App\Events\ApplicantNotification;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

class ApplicationController extends Controller
{
    private function generateApplicantId()
    {
        $year = date('Y');
        $lastApplication = PersonalInfo::where('applicant_id', 'like', "APP-$year-%")
            ->orderBy('applicant_id', 'desc')
            ->first();

        if ($lastApplication) {
            $lastNumber = intval(substr($lastApplication->applicant_id, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("APP-%s-%05d", $year, $newNumber);
    }

    public function saveStep(Request $request, $step)
    {
        DB::beginTransaction();
        try {
            switch ($step) {
                case 1: // Personal Info
                    $validatedData = $request->validate([
                        'firstName' => 'required|string|max:255',
                        'middleName' => 'nullable|string|max:255',
                        'lastName' => 'required|string|max:255',
                        'suffix' => 'nullable|string|max:255',
                        'address' => 'required|string',
                        'email' => 'required|email|max:255',
                        'phoneNumber' => 'required|string|max:50',
                        'zipCode' => 'required|string|max:20',
                        'birthDate' => 'required|date',
                        'placeOfBirth' => 'required|string|max:255',
                        'civilStatus' => 'required|string|in:Single,Married,Separated,Widow,Divorced',
                        'document' => $request->hasFile('document') ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable',
                        'sex' => 'required|string|in:Male,Female',
                        'nationality' => 'required|string|max:255',
                        'languages' => 'required|string|max:255'
                    ]);

                    if ($request->hasFile('document')) {
                        $path = $request->file('document')->store('documents', 'public');
                        $validatedData['document'] = $path;
                    }

                    // Check if this is a new application
                    $isNewApplication = !$request->has('applicant_id');

                    $personalInfo = PersonalInfo::updateOrCreate(
                        ['applicant_id' => $request->applicant_id ?? $this->generateApplicantId()],
                        $validatedData
                    );

                    // Send notification to admins if this is a new application
                    if ($isNewApplication) {
                        $admins = User::all();
                        \Log::info('Sending new application notification', [
                            'applicant_id' => $personalInfo->applicant_id,
                            'admins_count' => $admins->count(),
                            'admins' => $admins->pluck('email')->toArray()
                        ]);

                        Notification::send($admins, new ApplicationSubmitted(
                            "{$personalInfo->firstName} {$personalInfo->lastName}",
                            $personalInfo->applicant_id,
                            'started'
                        ));

                        \Log::info('Notification sent successfully');
                    }

                    $response = ['applicant_id' => $personalInfo->applicant_id];
                    break;

                case 2: // Learning Objectives
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',
                        'firstPriority' => 'required|string',
                        'secondPriority' => 'required|string',
                        'thirdPriority' => 'required|string',
                        'goalStatement' => 'required|string',
                        'timeCommitment' => 'required|string',
                        'overseasPlan' => 'nullable|string',
                        'costPayment' => 'required|string',
                        'otherCostPayment' => 'nullable|string',
                        'completionTimeline' => 'required|string'
                    ]);

                    LearningObjective::updateOrCreate(
                        ['applicant_id' => $request->applicant_id],
                        $validatedData
                    );
                    break;

                case 3: // Education
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',

                        // Elementary Education
                        'elementarySchool' => 'required|string|max:255',
                        'elementaryAddress' => 'required|string',
                        'elementaryDateFrom' => 'required|integer|min:1900|max:'.(date('Y')),
                        'elementaryDateTo' => [
                            'required',
                            'integer',
                            'min:1900',
                            'max:'.(date('Y')),
                            function ($attribute, $value, $fail) use ($request) {
                                if ($value < $request->elementaryDateFrom) {
                                    $fail('The completion year must be after the start year.');
                                }
                            }
                        ],
                        'hasElementaryDiploma' => 'boolean',
                        'elementaryDiplomaFile' => 'nullable|array',
                        'elementaryDiplomaFile.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',

                        // PEPT
                        'hasPEPT' => 'boolean',
                        'peptYear' => 'nullable|integer|min:1900|max:'.(date('Y')),
                        'peptGrade' => 'nullable|string',

                        // High School
                        'highSchools' => 'required_if:hasPEPT,0|array',
                        'highSchools.*.name' => 'required_if:hasPEPT,0|string',
                        'highSchools.*.address' => 'required_if:hasPEPT,0|string',
                        'highSchools.*.type' => 'required_if:hasPEPT,0|string|in:Junior High School,Senior High School',
                        'highSchools.*.dateFrom' => 'required_if:hasPEPT,0|integer|min:1900|max:'.(date('Y')),
                        'highSchools.*.dateTo' => [
                            'required_if:hasPEPT,0',
                            'integer',
                            'min:1900',
                            'max:'.(date('Y')),
                            function ($attribute, $value, $fail) use ($request) {
                                $index = explode('.', $attribute)[1];
                                if ($value < $request->highSchools[$index]['dateFrom']) {
                                    $fail('The completion year must be after the start year.');
                                }
                            }
                        ],
                        'highSchools.*.strand' => 'nullable|string|required_if:highSchools.*.type,Senior High School',
                        'highSchools.*.hasDiplomaFile' => 'boolean',
                        'highSchools.*.diplomaFile' => 'nullable|array',
                        'highSchools.*.diplomaFile.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',

                        // Post Secondary Education - optional
                        'postSecondary' => 'nullable|array',
                        'postSecondary.*.program' => 'required|string|max:255',
                        'postSecondary.*.institution' => 'required|string|max:255',
                        'postSecondary.*.schoolYear' => 'required|integer|min:1900|max:'.(date('Y')),
                        'postSecondary.*.diplomaFile' => 'nullable|array',
                        'postSecondary.*.diplomaFile.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',

                        // Non-Formal Education - optional
                        'nonFormal' => 'nullable|array',
                        'nonFormal.*.title' => 'required|string|max:255',
                        'nonFormal.*.organization' => 'required|string|max:255',
                        'nonFormal.*.certificateFiles' => 'nullable|array',
                        'nonFormal.*.certificateFiles.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',

                        // Certifications - optional with integer year
                        'certifications' => 'nullable|array',
                        'certifications.*.title' => 'nullable|string',
                        'certifications.*.agency' => 'required|string|max:255',
                        'certifications.*.dateCertified' => 'required|integer|min:1900|max:'.(date('Y')),
                        'certifications.*.certificateFiles' => 'nullable|array',
                        'certifications.*.certificateFiles.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
                        'certifications.*.rating' => 'nullable|string',
                        'certifications.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    ]);

                    // Convert string boolean values to actual booleans
                    $validatedData['hasElementaryDiploma'] = filter_var($request->input('hasElementaryDiploma'), FILTER_VALIDATE_BOOLEAN);
                    $validatedData['hasPEPT'] = filter_var($request->input('hasPEPT'), FILTER_VALIDATE_BOOLEAN);

                    // Handle file uploads with proper error handling
                    $files = [];

                    // Process elementary diploma files
                    if ($request->hasFile('elementaryDiplomaFile')) {
                        $files['elementaryDiplomaFile'] = [];
                        foreach ($request->file('elementaryDiplomaFile') as $file) {
                            $path = $file->store('diplomas', 'public');
                            $files['elementaryDiplomaFile'][] = $path;
                        }
                    }

                    // Process high school diploma files
                    foreach ($request->input('highSchools', []) as $index => $school) {
                        if ($request->hasFile("highSchools.{$index}.diplomaFile")) {
                            $files["highSchools.{$index}.diplomaFile"] = [];
                            foreach ($request->file("highSchools.{$index}.diplomaFile") as $file) {
                                $path = $file->store('diplomas', 'public');
                                $files["highSchools.{$index}.diplomaFile"][] = $path;
                            }
                        }
                    }

                    // Process post-secondary diploma files
                    if (isset($validatedData['postSecondary'])) {
                        foreach ($validatedData['postSecondary'] as $index => $postSecondary) {
                            $postSecondaryFiles = [];
                            if ($request->hasFile("postSecondary.{$index}.diplomaFile")) {
                                foreach ($request->file("postSecondary.{$index}.diplomaFile") as $file) {
                                    $path = $file->store('diplomas', 'public');
                                    $postSecondaryFiles[] = $path;
                                }
                            }
                            $validatedData['postSecondary'][$index]['diploma_files'] = $postSecondaryFiles;
                        }
                    }

                    // Process non-formal certificates
                    if (isset($validatedData['nonFormal'])) {
                        foreach ($validatedData['nonFormal'] as $index => $nonFormal) {
                            $certificateFiles = [];
                            if ($request->hasFile("nonFormal.{$index}.certificateFiles")) {
                                foreach ($request->file("nonFormal.{$index}.certificateFiles") as $file) {
                                    $path = $file->store('certificates', 'public');
                                    $certificateFiles[] = $path;
                                }
                            }
                            $validatedData['nonFormal'][$index]['certificate_files'] = $certificateFiles;
                        }
                    }

                    // Process certification certificates
                    if (isset($validatedData['certifications'])) {
                        foreach ($validatedData['certifications'] as $index => $certification) {
                            $certificateFiles = [];
                            if ($request->hasFile("certifications.{$index}.certificateFiles")) {
                                foreach ($request->file("certifications.{$index}.certificateFiles") as $file) {
                                    $path = $file->store('certifications', 'public');
                                    $certificateFiles[] = $path;
                                }
                            }
                            $validatedData['certifications'][$index]['certificate_files'] = $certificateFiles;
                        }
                    }

                    // Delete existing education records for this applicant
                    Education::where('applicant_id', $request->applicant_id)->delete();

                    // Save Elementary Education
                    $education = Education::create([
                        'applicant_id' => $request->applicant_id,
                        'type' => 'elementary',
                        'school_name' => $validatedData['elementarySchool'],
                        'address' => $validatedData['elementaryAddress'],
                        'date_from' => (int)$validatedData['elementaryDateFrom'],
                        'date_to' => (int)$validatedData['elementaryDateTo'],
                        'has_diploma' => $validatedData['hasElementaryDiploma'] ?? false,
                        'diploma_files' => $files['elementaryDiplomaFile'] ?? null,
                    ]);

                    // Save High School Education
                    if (!($validatedData['hasPEPT'] ?? false)) {
                        foreach ($validatedData['highSchools'] as $index => $school) {
                            Education::create([
                                'applicant_id' => $request->applicant_id,
                                'type' => $school['type'],
                                'school_name' => $school['name'],
                                'address' => $school['address'],
                                'date_from' => (int)$school['dateFrom'],
                                'date_to' => (int)$school['dateTo'],
                                'strand' => $school['type'] === 'Senior High School' ? $school['strand'] : null,
                                'has_diploma' => $school['hasDiplomaFile'] ?? false,
                                'diploma_files' => $files["highSchools.{$index}.diplomaFile"] ?? null,
                            ]);
                        }
                    }

                    // Save Post Secondary Education to specific table
                    if (isset($validatedData['postSecondary'])) {
                        foreach ($validatedData['postSecondary'] as $postSecondary) {
                            PostSecondaryEducation::create([
                                'education_id' => $education->id,
                                'program' => $postSecondary['program'],
                                'institution' => $postSecondary['institution'],
                                'school_year' => $postSecondary['schoolYear'],
                                'diploma_files' => $postSecondary['diploma_files'] ?? null,
                            ]);
                        }
                    }

                    // Save Non-Formal Education
                    if (isset($validatedData['nonFormal'])) {
                        foreach ($validatedData['nonFormal'] as $nonFormal) {
                            NonFormalEducation::create([
                                'education_id' => $education->id,
                                'title' => $nonFormal['title'],
                                'organization' => $nonFormal['organization'],
                                'certificate_files' => $nonFormal['certificate_files'] ?? null,
                                'participation' => $nonFormal['participation'] ?? null,
                            ]);
                        }
                    }

                    // Save Certifications
                    if (isset($validatedData['certifications'])) {
                        foreach ($validatedData['certifications'] as $certification) {
                            Certification::create([
                                'education_id' => $education->id,
                                'agency' => $certification['agency'],
                                'date_certified' => $certification['dateCertified'],
                                'rating' => $certification['rating'] ?? null,
                                'certificate_files' => $certification['certificate_files'] ?? null,
                            ]);
                        }
                    }

                    // Save PEPT if applicable
                    if ($validatedData['hasPEPT'] ?? false) {
                        Education::create([
                            'applicant_id' => $request->applicant_id,
                            'type' => 'pept',
                            'pept_year' => (int)$validatedData['peptYear'],
                            'pept_grade' => $validatedData['peptGrade'],
                        ]);
                    }

                    break;

                case 4: // Work Experience
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',
                        'employment_type' => 'required|in:employed,self_employed,no_employment',
                        'workExperiences' => 'required_if:employment_type,employed,self_employed|array',
                        'workExperiences.*.designation' => 'required_if:employment_type,employed,self_employed|string|max:255',
                        'workExperiences.*.companyName' => 'required_if:employment_type,employed,self_employed|string|max:255',
                        'workExperiences.*.companyAddress' => 'required_if:employment_type,employed,self_employed|string',
                        'workExperiences.*.dateFrom' => 'required_if:employment_type,employed,self_employed|integer',
                        'workExperiences.*.dateTo' => 'required_if:employment_type,employed,self_employed|integer',
                        'workExperiences.*.employmentStatus' => 'required_if:employment_type,employed|string|max:255',
                        'workExperiences.*.supervisorName' => 'required_if:employment_type,employed|string|max:255',
                        'workExperiences.*.reference1_name' => 'required_if:employment_type,self_employed|string|max:255',
                        'workExperiences.*.reference1_contact' => 'required_if:employment_type,self_employed|string|max:255',
                        'workExperiences.*.reference2_name' => 'required_if:employment_type,self_employed|string|max:255',
                        'workExperiences.*.reference2_contact' => 'required_if:employment_type,self_employed|string|max:255',
                        'workExperiences.*.reference3_name' => 'required_if:employment_type,self_employed|string|max:255',
                        'workExperiences.*.reference3_contact' => 'required_if:employment_type,self_employed|string|max:255',
                        'workExperiences.*.reasonForLeaving' => 'required_if:employment_type,employed,self_employed|string',
                        'workExperiences.*.responsibilities' => 'required_if:employment_type,employed,self_employed|string',
                        'workExperiences.*.documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
                    ]);

                    // Delete existing work experiences
                    WorkExperience::where('applicant_id', $request->applicant_id)->delete();

                    // Only create records if not "no_employment"
                    if ($validatedData['employment_type'] !== 'no_employment') {
                        foreach ($request->workExperiences as $index => $experience) {
                            $workExperience = new WorkExperience([
                                'applicant_id' => $request->applicant_id,
                                'employment_type' => $validatedData['employment_type'],
                                'designation' => $experience['designation'],
                                'companyName' => $experience['companyName'],
                                'companyAddress' => $experience['companyAddress'],
                                'dateFrom' => $experience['dateFrom'],
                                'dateTo' => $experience['dateTo'],
                                'reasonForLeaving' => $experience['reasonForLeaving'],
                                'responsibilities' => $experience['responsibilities'],
                                'employmentStatus' => $experience['employmentStatus'] ?? null,
                                'supervisorName' => $experience['supervisorName'] ?? null,
                                'reference1_name' => $experience['reference1_name'] ?? null,
                                'reference1_contact' => $experience['reference1_contact'] ?? null,
                                'reference2_name' => $experience['reference2_name'] ?? null,
                                'reference2_contact' => $experience['reference2_contact'] ?? null,
                                'reference3_name' => $experience['reference3_name'] ?? null,
                                'reference3_contact' => $experience['reference3_contact'] ?? null
                            ]);

                            // Handle document upload - updated for proper file checking
                            if ($request->hasFile("workExperiences.{$index}.documents")) {
                                $file = $request->file("workExperiences.{$index}.documents");
                                $path = $file->store('work-documents', 'public');
                                $workExperience->documents = $path;
                            }

                            $workExperience->save();
                        }
                    }
                    break;

                case 5: // Honors and Awards
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',

                        // Academic Awards
                        'academicAwards' => 'present|array',
                        'academicAwards.*.title' => 'nullable|string',
                        'academicAwards.*.institution' => 'nullable|string',
                        'academicAwards.*.dateReceived' => 'nullable|date',
                        'academicAwards.*.description' => 'nullable|string',
                        'academicAwards.*.document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

                        // Community Awards
                        'communityAwards' => 'present|array',
                        'communityAwards.*.title' => 'nullable|string',
                        'communityAwards.*.organization' => 'nullable|string',
                        'communityAwards.*.dateAwarded' => 'nullable|date',

                        // Work Awards
                        'workAwards' => 'present|array',
                        'workAwards.*.title' => 'nullable|string',
                        'workAwards.*.organization' => 'nullable|string',
                        'workAwards.*.dateAwarded' => 'nullable|date'
                    ]);

                    // Handle Academic Awards
                    if ($request->has('academicAwards')) {
                        AcademicAward::where('applicant_id', $request->applicant_id)->delete();

                        foreach ($request->academicAwards as $award) {
                            // Debug log
                            \Log::info('Creating academic award with data:', [
                                'award_data' => $award,
                                'applicant_id' => $request->applicant_id
                            ]);

                            try {
                                // Create award with explicit data array
                                $awardData = [
                                    'applicant_id' => $request->applicant_id,
                                    'title' => $award['title'],
                                    'institution' => $award['institution'],
                                    'dateReceived' => $award['dateReceived'],
                                    'description' => $award['description'],
                                ];

                                // Handle document if present
                                if (isset($award['document']) && $award['document'] instanceof \Illuminate\Http\UploadedFile) {
                                    $awardData['document'] = $award['document']->store('awards', 'public');
                                }

                                // Debug log the final data being sent to create
                                \Log::info('Final award data for creation:', $awardData);

                                $createdAward = AcademicAward::create($awardData);

                                // Debug log success
                                \Log::info('Successfully created award:', ['award_id' => $createdAward->id]);

                            } catch (\Exception $e) {
                                // Debug log error
                                \Log::error('Failed to create academic award:', [
                                    'error' => $e->getMessage(),
                                    'data' => $awardData ?? null
                                ]);
                                throw $e;
                            }
                        }
                    }

                    // Handle Community Awards
                    if ($request->has('communityAwards')) {
                        CommunityAward::where('applicant_id', $request->applicant_id)->delete();
                        foreach ($request->communityAwards as $award) {
                            CommunityAward::create([
                                'applicant_id' => $request->applicant_id,
                                'title' => $award['title'],
                                'organization' => $award['organization'],
                                'dateAwarded' => $award['dateAwarded']
                            ]);
                        }
                    }

                    // Handle Work Awards
                    if ($request->has('workAwards')) {
                        WorkAward::where('applicant_id', $request->applicant_id)->delete();
                        foreach ($request->workAwards as $award) {
                            WorkAward::create([
                                'applicant_id' => $request->applicant_id,
                                'title' => $award['title'],
                                'organization' => $award['organization'],
                                'dateAwarded' => $award['dateAwarded']
                            ]);
                        }
                    }
                    break;

                case 6: // Creative Works
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',
                        'creativeWorks' => 'nullable|array|min:1',
                        'creativeWorks.*.title' => 'nullable|string|max:255',
                        'creativeWorks.*.description' => 'nullable|string',
                        'creativeWorks.*.significance' => 'nullable|string',
                        'creativeWorks.*.dateCompleted' => 'nullable|date',
                        'creativeWorks.*.corroboratingBody' => 'nullable|string|max:255',
                    ]);

                    try {
                        // Delete existing records first
                        CreativeWork::where('applicant_id', $request->applicant_id)->delete();

                        // Create new records only if data exists
                        if (!empty($validatedData['creativeWorks'])) {
                            foreach ($validatedData['creativeWorks'] as $work) {
                                CreativeWork::create([
                                    'applicant_id' => $validatedData['applicant_id'],
                                    'title' => $work['title'] ?? null,
                                    'description' => $work['description'] ?? null,
                                    'significance' => $work['significance'] ?? null,
                                    'date_completed' => $work['dateCompleted'] ?? null,
                                    'corroborating_body' => $work['corroboratingBody'] ?? null
                                ]);
                            }
                        }

                        // Debug logging
                        \Log::info('Creative works saved successfully', [
                            'applicant_id' => $request->applicant_id,
                            'count' => count($validatedData['creativeWorks'] ?? [])
                        ]);

                    } catch (\Exception $e) {
                        \Log::error('Failed to save creative works', [
                            'error' => $e->getMessage(),
                            'applicant_id' => $request->applicant_id ?? null
                        ]);
                        throw $e;
                    }
                    break;

                case 7: // Lifelong Learning
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',
                        'hobbies' => 'present|array',
                        'hobbies.*.description' => 'required|string',
                        'specialSkills' => 'present|array',
                        'specialSkills.*.description' => 'required|string',
                        'workActivities' => 'present|array',
                        'workActivities.*.description' => 'required|string',
                        'volunteerActivities' => 'present|array',
                        'volunteerActivities.*.description' => 'nullable|string',
                        'travels' => 'present|array',
                        'travels.*.description' => 'nullable|string'
                    ]);

                    // Ensure at least one category has entries
                    if (empty($validatedData['hobbies']) &&
                        empty($validatedData['specialSkills']) &&
                        empty($validatedData['workActivities']) &&
                        empty($validatedData['volunteerActivities']) &&
                        empty($validatedData['travels'])) {
                        return response()->json([
                            'error' => 'At least one lifelong learning experience is required'
                        ], 422);
                    }

                    // Delete existing records first
                    LifelongLearning::where('applicant_id', $request->applicant_id)->delete();

                    // Helper function to save experiences
                    $saveExperiences = function($experiences, $type) use ($request) {
                        foreach ($experiences as $experience) {
                            LifelongLearning::create([
                                'applicant_id' => $request->applicant_id,
                                'type' => $type,
                                'description' => $experience['description']
                            ]);
                        }
                    };

                    // Save all categories
                    if (!empty($validatedData['hobbies'])) {
                        $saveExperiences($validatedData['hobbies'], 'hobby');
                    }
                    if (!empty($validatedData['specialSkills'])) {
                        $saveExperiences($validatedData['specialSkills'], 'skill');
                    }
                    if (!empty($validatedData['workActivities'])) {
                        $saveExperiences($validatedData['workActivities'], 'work');
                    }
                    if (!empty($validatedData['volunteerActivities'])) {
                        $saveExperiences($validatedData['volunteerActivities'], 'volunteer');
                    }
                    if (!empty($validatedData['travels'])) {
                        $saveExperiences($validatedData['travels'], 'travel');
                    }
                    break;

                case 8: // Essay
                    $validatedData = $request->validate([
                        'applicant_id' => 'required|exists:personal_infos,applicant_id',
                        'essay' => 'required|string|min:500'
                    ]);

                    // Update or create essay
                    Essay::updateOrCreate(
                        ['applicant_id' => $request->applicant_id],
                        ['content' => $validatedData['essay']]
                    );

                    // Get the application and update status
                    $personalInfo = PersonalInfo::where('applicant_id', $request->applicant_id)->firstOrFail();
                    $personalInfo->update(['status' => 'pending']);

                    // Send notification to admin users
                    $admins = User::all();
                    Notification::send($admins, new ApplicationSubmitted(
                        "{$personalInfo->firstName} {$personalInfo->lastName}",
                        $personalInfo->applicant_id
                    ));

                    break;
            }

            DB::commit();
            return response()->json([
                'message' => 'Step saved successfully',
                'data' => $response ?? null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Handle validation errors specifically
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            // Log the error for debugging
            \Log::error('Application save failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to save step',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function finalizeApplication(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required|exists:personal_infos,applicant_id'
            ]);

            $personalInfo = PersonalInfo::with('essay')
                ->where('applicant_id', $request->applicant_id)
                ->firstOrFail();

            // Ensure essay exists
            if (!$personalInfo->essay || empty($personalInfo->essay->content)) {
                return response()->json([
                    'error' => 'Cannot submit application without completing the essay'
                ], 422);
            }

            DB::transaction(function () use ($personalInfo) {
                $personalInfo->update(['status' => 'pending']);

                $admins = User::all();
                Notification::send($admins, new ApplicationSubmitted(
                    "{$personalInfo->firstName} {$personalInfo->lastName}",
                    $personalInfo->applicant_id
                ));
            });

            return response()->json([
                'message' => 'Application submitted successfully',
                'status' => 'pending'
            ]);

        } catch (\Exception $e) {
            \Log::error('Finalization failed: '.$e->getMessage());
            return response()->json([
                'error' => 'Failed to submit application',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function loadApplication($applicantId)
    {
        try {
            $personalInfo = PersonalInfo::with([
                'learningObjective',
                'education',
                'workExperiences',
                'academicAwards',
                'communityAwards',
                'workAwards',
                'creativeWorks',
                'lifelongLearning',
                'essay'
            ])->where('applicant_id', $applicantId)
              ->firstOrFail();

            return response()->json([
                'data' => $personalInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load application',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function trackApplication(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required|string|exists:personal_infos,applicant_id'
            ]);

            $application = PersonalInfo::with([
                'learningObjective',
                'education',
                'workExperiences',
                'academicAwards',
                'communityAwards',
                'workAwards',
                'creativeWorks',
                'lifelongLearning',
                'essay'
            ])
            ->where('applicant_id', $request->applicant_id)
            ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'application' => [
                    'id' => $application->applicant_id,
                    'status' => $application->status,
                    'submitted_at' => $application->created_at->format('M d, Y h:i A'),
                    'personal_info' => $application,
                    'learning_objective' => $application->learningObjective,
                    'education' => $application->education,
                    'work_experience' => $application->workExperiences,
                    'academic_awards' => $application->academicAwards,
                    'community_awards' => $application->communityAwards,
                    'work_awards' => $application->workAwards,
                    'creative_works' => $application->creativeWorks,
                    'lifelong_learning' => $application->lifelongLearning,
                    'essay' => $application->essay
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Application ID'
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Tracking error: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve application status'
            ], 500);
        }
    }

    public function generateApplicantPdf($applicantId)
    {
        try {
            $record = PersonalInfo::where('applicant_id', $applicantId)
                ->with([
                    'learningObjective',
                    'education',
                    'workExperiences',
                    'academicAwards',
                    'communityAwards',
                    'workAwards',
                    'creativeWorks',
                    'lifelongLearning',
                    'essay'
                ])
                ->firstOrFail();

            $record->load([
                'lifelongLearning',
                'workExperiences',
                'academicAwards',
                'communityAwards',
                'workAwards',
                'education',
                'learningObjective',
                'creativeWorks',
                'essay'
            ]);

            $infoPdf = Pdf::loadView('pdfs.personal-info', ['record' => $record])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Poppins',
                    'chroot' => public_path(),
                ]);

            $merger = PDFMerger::init();
            $merger->addString($infoPdf->output(), 'all');

            $addDocumentToPdf = function($path, $documentType) use ($merger) {
                if (empty($path)) return;

                $fullPath = Storage::disk('public')->path($path);
                if (!file_exists($fullPath)) {
                    \Log::warning("Skipping nonexistent {$documentType}: {$fullPath}");
                    return;
                }

                // Check if the file is a valid PDF or an image
                $mimeType = mime_content_type($fullPath);
                if ($mimeType === 'application/pdf') {
                    try {
                        $merger->addString(file_get_contents($fullPath), 'all');
                    } catch (\Exception $e) {
                        \Log::error("Failed to merge PDF {$documentType}: " . $e->getMessage());
                    }
                } elseif (in_array($mimeType, ['image/png', 'image/jpeg'])) {
                    // Convert image to PDF
                    $pdfPath = $this->convertImageToPdf($fullPath);
                    if ($pdfPath) {
                        try {
                            $merger->addString(file_get_contents($pdfPath), 'all');
                        } catch (\Exception $e) {
                            \Log::error("Failed to merge converted image {$documentType}: " . $e->getMessage());
                        }
                    }
                } else {
                    \Log::warning("Skipping unsupported file type {$documentType}: {$fullPath}");
                }
            };

            \Log::info("Starting PDF generation for applicant ID: {$applicantId}");

            // Add documents (same logic as original)
            if ($record->document) {
                \Log::info("Adding personal document for applicant ID: {$applicantId}");
                $addDocumentToPdf($record->document, 'Personal Document');
            }

            foreach ($record->education as $edu) {
                $diplomaFiles = data_get($edu, 'diploma_files');
                if ($diplomaFiles) {
                    foreach ($diplomaFiles as $diplomaFile) {
                        $documentType = match(data_get($edu, 'type')) {
                            'elementary' => 'Elementary Diploma',
                            'high_school' => 'High School Diploma',
                            'post_secondary' => 'Post Secondary Diploma',
                            default => 'Diploma'
                        };
                        \Log::info("Adding {$documentType} for applicant ID: {$applicantId}");
                        $addDocumentToPdf($diplomaFile, $documentType);
                    }
                }

                if (data_get($edu, 'type') === 'non_formal') {
                    \Log::info("Adding Non-Formal Certificate for applicant ID: {$applicantId}");
                    $addDocumentToPdf(data_get($edu, 'certificate'), 'Non-Formal Certificate');
                }
            }

            foreach ($record->workExperiences as $exp) {
                if (!empty($exp['documents'])) {
                    $documents = is_array($exp['documents']) ? $exp['documents'] : [$exp['documents']];
                    foreach ($documents as $doc) {
                        \Log::info("Adding Work Experience Document for applicant ID: {$applicantId}");
                        $addDocumentToPdf($doc, 'Work Experience Document');
                    }
                }
            }

            // Add award documents
            foreach ([
                'academicAwards' => 'Academic Award',
                'communityAwards' => 'Community Award',
                'workAwards' => 'Work Award'
            ] as $collection => $label) {
                foreach ($record->$collection as $award) {
                    if (!empty($award['document'])) {
                        \Log::info("Adding {$label} for applicant ID: {$applicantId}");
                        $addDocumentToPdf($award['document'], $label);
                    }
                }
            }

            \Log::info("Merging PDF documents for applicant ID: {$applicantId}");
            $merger->merge();
            $content = $merger->output();

            \Log::info("PDF generation completed successfully for applicant ID: {$applicantId}");

            return response()->stream(
                fn () => print($content),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="applicant-' . $record->applicant_id . '.pdf"'
                ]
            );

        } catch (\Exception $e) {
            \Log::error("PDF Generation failed for applicant ID: {$applicantId}. Error: " . $e->getMessage());
            abort(500, 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    private function convertImageToPdf($imagePath)
    {
        // Initialize Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Create a blank image using GD
        $image = null;
        if (mime_content_type($imagePath) === 'image/png') {
            $image = imagecreatefrompng($imagePath);
        } elseif (mime_content_type($imagePath) === 'image/jpeg') {
            $image = imagecreatefromjpeg($imagePath);
        }

        if ($image) {
            // Get image dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Create HTML content with the image
            $html = '<html><body>';
            $html .= '<img src="data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) . '" style="width:100%; height:auto;" />';
            $html .= '</body></html>';

            // Load HTML content
            $dompdf->loadHtml($html);

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF
            $dompdf->render();

            // Save the PDF to a file
            $pdfPath = str_replace(['.png', '.jpg', '.jpeg'], '.pdf', $imagePath);
            file_put_contents($pdfPath, $dompdf->output());

            // Free up memory
            imagedestroy($image);

            return $pdfPath;
        }

        return null;
    }
}
