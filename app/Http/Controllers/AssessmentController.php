<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use Barryvdh\DomPDF\Facade\Pdf;

class AssessmentController extends Controller
{
    public function generate(PersonalInfo $student)
    {
        $student->load('subjects');
        $pdf = Pdf::loadView('assessment', [
            'student' => $student,
            'fullName' => $student->fullName()
        ]);
        return $pdf->stream('student-assessment.pdf');
    }
}
