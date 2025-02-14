<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use Barryvdh\DomPDF\Facade\Pdf;

class AssessmentController extends Controller
{
    public function generate(PersonalInfo $student)
    {
        $pdf = Pdf::loadView('assessment', compact('student'));
        return $pdf->stream('student-assessment.pdf');
    }
} 