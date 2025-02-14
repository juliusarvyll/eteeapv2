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
                        'elementaryDiplomaFile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

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

                        // Post Secondary Education - optional
                        'postSecondary' => 'nullable|array',
                        'postSecondary.*.program' => 'nullable|string',
                        'postSecondary.*.institution' => 'nullable|string',
                        'postSecondary.*.schoolYear' => 'nullable|string',

                        // Non-Formal Education - optional
                        'nonFormalEducation' => 'nullable|array',
                        'nonFormalEducation.*.title' => 'nullable|string',
                        'nonFormalEducation.*.organization' => 'nullable|string',
                        'nonFormalEducation.*.date' => 'nullable|string',
                        'nonFormalEducation.*.certificate' => 'nullable|string',
                        'nonFormalEducation.*.participation' => 'nullable|string',

                        // Certifications - optional with integer year
                        'certifications' => 'nullable|array',
                        'certifications.*.title' => 'nullable|string',
                        'certifications.*.agency' => 'nullable|string',
                        'certifications.*.dateCertified' => [
                            'nullable',
                            'integer',
                            'min:1900',
                            'max:'.(date('Y'))
                        ],
                        'certifications.*.rating' => 'nullable|string',
                        'certifications.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    ]);

                    // Convert string boolean values to actual booleans
                    $validatedData['hasElementaryDiploma'] = filter_var($request->input('hasElementaryDiploma'), FILTER_VALIDATE_BOOLEAN);
                    $validatedData['hasPEPT'] = filter_var($request->input('hasPEPT'), FILTER_VALIDATE_BOOLEAN);

                    // Handle file uploads with proper error handling
                    $files = [];
                    $fileFields = [
                        'elementaryDiplomaFile'
                    ];

                    foreach ($fileFields as $fileField) {
                        if ($request->hasFile($fileField)) {
                            try {
                                $file = $request->file($fileField);
                                $path = $file->store('diplomas', 'public');
                                $files[$fileField] = $path;
                            } catch (\Exception $e) {
                                \Log::error("Failed to upload {$fileField}: " . $e->getMessage());
                                throw new \Exception("Failed to upload {$fileField}");
                            }
                        }
                    }

                    // Delete existing education records for this applicant
                    Education::where('applicant_id', $request->applicant_id)->delete();

                    // Save Elementary
                    Education::create([
                        'applicant_id' => $request->applicant_id,
                        'type' => 'elementary',
                        'school_name' => $validatedData['elementarySchool'],
                        'address' => $validatedData['elementaryAddress'],
                        'date_from' => (int)$validatedData['elementaryDateFrom'],
                        'date_to' => (int)$validatedData['elementaryDateTo'],
                        'has_diploma' => $validatedData['hasElementaryDiploma'] ?? false,
                        'diploma_file' => isset($files['elementaryDiplomaFile']) ? $files['elementaryDiplomaFile'] : null,
                    ]);

                    // Save High Schools
                    if (!($validatedData['hasPEPT'] ?? false)) {
                        foreach ($validatedData['highSchools'] as $school) {
                            Education::create([
                                'applicant_id' => $request->applicant_id,
                                'type' => 'high_school',
                                'school_name' => $school['name'],
                                'address' => $school['address'],
                                'school_type' => $school['type'],
                                'date_from' => (int)$school['dateFrom'],
                                'date_to' => (int)$school['dateTo'],
                                'is_senior_high' => $school['type'] === 'Senior High School',
                                'strand' => $school['type'] === 'Senior High School' ? $school['strand'] : null,
                            ]);
                        }
                    }

                    // Save Post Secondary
                    if (isset($validatedData['postSecondary'])) {
                        foreach ($validatedData['postSecondary'] as $postSecondary) {
                            Education::create([
                                'applicant_id' => $request->applicant_id,
                                'type' => 'post_secondary',
                                'program' => $postSecondary['program'],
                                'institution' => $postSecondary['institution'],
                                'school_year' => $postSecondary['schoolYear'],
                            ]);
                        }
                    }

                    // Save Non-Formal Education
                    if (isset($validatedData['nonFormalEducation'])) {
                        foreach ($validatedData['nonFormalEducation'] as $nonFormal) {
                            Education::create([
                                'applicant_id' => $request->applicant_id,
                                'type' => 'non_formal',
                                'title' => $nonFormal['title'],
                                'organization' => $nonFormal['organization'],
                                'certificate' => $nonFormal['certificate'],
                                'participation' => $nonFormal['participation'],
                            ]);
                        }
                    }

                    // Save Certifications
                    if (isset($validatedData['certifications'])) {
                        foreach ($validatedData['certifications'] as $cert) {
                            Education::create([
                                'applicant_id' => $request->applicant_id,
                                'type' => 'certification',
                                'title' => $cert['title'],
                                'agency' => $cert['agency'],
                                'date_certified' => isset($cert['dateCertified']) ? (int)$cert['dateCertified'] : null,
                                'rating' => $cert['rating'],
                            ]);
                        }
                    }

                    // PEPT if applicable
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
                        foreach ($request->workExperiences as $experience) {
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

                            // Handle document upload
                            if (isset($experience['documents']) && $experience['documents'] instanceof UploadedFile) {
                                $path = $experience['documents']->store('work-documents', 'public');
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
}
