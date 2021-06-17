<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Students
|--------------------------------------------------------------------------
|Upload photo for verification.
|Students current class details.
|Viewing attendance details.
|Semester registration.
|Enter university marks.
|View series exam marks.
|View draft/final sessional marks.
|Feedback for faculty evaluation.
|
|
*/

class StudentsController extends Controller
{
    public function dashboards()
    {
        return view('students.dashboard');
    }

    public function attendance()
    {
        return view('students.attendance');
    }

    public function semRegistrations()
    {
        return view('students.semRegistrations');
    }

    public function postSemRegistrations()
    {
        return view('students.semRegistrations');
    }

    public function universityMarks()
    {
        return view('students.universityMarks');
    }

    public function postUniversityMarks(Request $request)
    {
        return view('students.universityMarks');
    }

    public function seriesMarks()
    {
        return view('students.seriesMarks');
    }

    public function sessionMarks()
    {
        return view('students.sessionMarks');
    }

    public function facultyEvaluvations()
    {
        return view('students.facultyEvaluvations');
    }

    public function postfacultyEvaluvations(Request $request)
    {
        return view('students.facultyEvaluvations');
    }

    public function photos()
    {
        return view('students.photos');
    }

    public function postPhotos(Request $request)
    {
        return view('students.photos');
    }
}
