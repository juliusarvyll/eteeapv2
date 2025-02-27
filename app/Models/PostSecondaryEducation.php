<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSecondaryEducation extends Model
{
    use HasFactory;

    protected $table = 'education_post_secondary';

    protected $casts = [
        'diploma_files' => 'array',
        'school_year' => 'integer'
    ];

    protected $fillable = [
        'education_id',
        'program',
        'institution',
        'school_year',
        'diploma_files'
    ];

    /**
     * Define the relationship with the Education model.
     */
    public function education()
    {
        return $this->belongsTo(Education::class);
    }
}
