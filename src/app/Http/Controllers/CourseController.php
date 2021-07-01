<?php

namespace App\Http\Controllers;

use App\Enums\CourseTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Classroom;
use App\Http\Requests\SubjectRequest;

class CourseController extends Controller
{

    /*
     *Middleware for checking whether user is HOD
     */


    public function index(){
        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;

        if ($faculty && $faculty->isHOD()) {
            $courses = Course::with(['Classroom','Subject','Faculty'])->get();
//        No idea to filter hod's department related stuff from database,so basically filtering in views
            return view('courses.index',["courses" => $courses,"department_code" => $department_code]);
        }

    }

    public function create(){
        // HOD create subjects here - get request do it returns
        //  that page to HOD
        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        // print $faculty;
        $department_code = $faculty->department_code;

        $classrooms = Classroom::all();
        $faculties = Faculty::where('department_code',$department_code)->get();
        $subjects = Subject::where('department_code',$department_code)->get();
        $types = CourseTypes::getKeys();

        return view('courses.create',["classrooms"=>$classrooms,"faculties"=>$faculties,"types"=>$types,"subjects"=>$subjects]);
    }

    public function store(Request $request){
        // create new subject - POST

        /*
         * Needs to do request validation
         * Move Course creation to model
         * Javascipt function to check Course is already existing or not
         * Optimise Course creation
         */

        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;

        $data = $request->input();

        $course  = new Course();
        $course->type = CourseTypes::getValue($data["type"]);
        $course->faculty_id=$data["faculty"];
        $course->classroom_id = $data["classroom"];
        $course->subject_code = $data["subject"];
//        Here is room for improvement instead of again querying in database table,we can infer it from above
//        message/ do we need this in the first place <<Semester>>
        $course->semester = Classroom::where('id',$data["classroom"])->get('semester')->first()->semester;
        $course->save();



        return redirect()->route('courses.index');


    }


    public function edit($id){
        //get page for updating specific subject
        // check whether this hod has permission to update this course

        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;

        $classrooms = Classroom::all();
        $faculties = Faculty::where('department_code',$department_code)->get();
        $subjects = Subject::where('department_code',$department_code)->get();
        $types = CourseTypes::getKeys();
        $course = Course::where('id',$id)->first();
        $courseType = CourseTypes::getKey($course->type); // we need this as as we are displaying value of enum but store key in db
        return view('courses.edit',["classrooms"=>$classrooms,"faculties"=>$faculties,"types"=>$types,"subjects"=>$subjects,"course"=>$course,"courseType"=>$courseType]);
    }

    public function update(Request $request ,$id){
        // modifying the existing course resource

        /*
         * Needs to do request validation
         * Move Course creation to model
         * Javascipt function to check subject code is unique or not in client side
         * Optimise Course creation
         * Update method - PUT/PATCH ?
         * Check whether this HOD has power to update this course
         */

        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;

        $data = $request->input();
        $editCourse = Course::where('id',$id)->first(); // need to find better method than this
//        this queries and retreives , but we dont' need retreival here ,only updation is needed
        $editCourse->faculty_id=$data["faculty"];
        $editCourse->classroom_id = $data["classroom"];
        $editCourse->subject_code = $data["subject"];
        $editCourse->type = CourseTypes::getValue($data["type"]);
        $editCourse->semester = Classroom::where('id',$data["classroom"])->get('semester')->first()->semester;
        $editCourse->save();
        return redirect()->route('courses.index');

    }

    public function destroy($id){
        // delete specific subjects - delete request
        // check for permission to delete this stuff by current hod
        $response=Course::where('id',$id)->delete();
        return redirect()->route('courses.index');
    }
}
