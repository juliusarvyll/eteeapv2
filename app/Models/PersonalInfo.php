<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'applicant_id',
        'firstName',
        'middleName',
        'lastName',
        'suffix',
        'birthDate',
        'placeOfBirth',
        'civilStatus',
        'email',
        'phoneNumber',
        'address',
        'zipCode',
        'document',
        'sex',
        'languages',
        'status'
    ];

    /**
     * Get the learning objective associated with the personal info.
     */
    public function learningObjective(): HasOne
    {
        return $this->hasOne(LearningObjective::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Get all education records for the personal info.
     */
    public function education(): HasMany
    {
        return $this->hasMany(Education::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Get all work experiences for the personal info.
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Get all academic awards for the personal info.
     */
    public function academicAwards(): HasMany
    {
        return $this->hasMany(AcademicAward::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Get all community awards for the personal info.
     */
    public function communityAwards(): HasMany
    {
        return $this->hasMany(CommunityAward::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Get all work awards for the personal info.
     */
    public function workAwards(): HasMany
    {
        return $this->hasMany(WorkAward::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Helper methods to get specific education types
     */
    public function elementaryEducation()
    {
        return $this->education()->where('type', 'elementary');
    }

    public function highSchoolEducation()
    {
        return $this->education()->where('type', 'high_school');
    }

    public function postSecondaryEducation()
    {
        return $this->education()->where('type', 'post_secondary');
    }

    public function nonFormalEducation()
    {
        return $this->education()->where('type', 'non_formal');
    }

    public function certifications()
    {
        return $this->education()->where('type', 'certification');
    }

    // Creative Works - One to Many
    public function creativeWorks(): HasMany
    {
        return $this->hasMany(CreativeWork::class, 'applicant_id', 'applicant_id');
    }

    // Lifelong Learning - One to Many
    public function lifelongLearning(): HasMany
    {
        return $this->hasMany(LifelongLearning::class, 'applicant_id', 'applicant_id');
    }

    // Essay - One to One
    public function essay(): HasOne
    {
        return $this->hasOne(Essay::class, 'applicant_id', 'applicant_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'applicant_id', 'applicant_id');
    }

    public function fullName(): string
    {
        return trim(collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix
        ])->filter()->join(' '));
    }
}
