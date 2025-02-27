<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonFormalEducation extends Model
{
    use HasFactory;

    protected $table = 'education_non_formal';

    protected $casts = [
        'certificate_files' => 'array'
    ];

    protected $fillable = [
        'education_id',
        'title',
        'organization',
        'certificate_files',
        'participation',
    ];

    /**
     * Define the relationship with the Education model.
     */
    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }
}
