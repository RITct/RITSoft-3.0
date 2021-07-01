<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Faculty;
use App\Http\Requests\SubjectRequest;

class SubjectController extends Controller
{




    /*
     *Middleware for checking whether user is HOD
    */



    public function index(){
        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        // print $faculty;
        $department_code = $faculty->department_code;
        // print $department_code;

        if ($faculty && $faculty->isHOD()) {
            $subjects = Subject::where('department_code',$department_code)->get();
            // $faculty = Faculty::where('department_code',$department_code)->get();
            return view('subjects.index',["subjects" => $subjects]);
        }

    }

    public function create(){
        // HOD create subjects here - get request do it returns
        //  that page to HOD
        return view('subjects.create');
    }

    public function store(Request $request){
        // create new subject - POST

        /*
         * Needs to do request validation
         * Move subject creation to model
         * Javascipt function to check subject code is already existing or not
         * Optimise Subject creation
         */
        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;

        $data = $request->input();

        $subject  = new Subject();
        $subject->name = $data["name"];
        $subject->credits = $data["credit"];
        $subject->code = $data["courseId"];
        //This is the primary key check for its uniqueness and return error
        $subject->department_code = $department_code;
        $subject->save();



        return redirect()->route('subjects.index');


    }



    public function edit($subject_code){
        //get page for updating specific subject
        // check whether this hod has permission to see this subject
        $subject = Subject::where('code',$subject_code)->get()->first();
        return view('subjects.edit',["subject"=>$subject]);
    }

    public function update(Request $request){
        // modifying the existing subject configuration

        /*
         * Needs to do request validation
         * Move subject creation to model
         * Javascipt function to check subject code is unique or not in client side
         * Optimise Subject creation
         * Update method - PUT/PATCH ?
         * Check whether this HOD has power to update this subject
         */
        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;
        print $request;
//      Since subject-code is unique check whether that is existing and return error,currently we dont do that
        $data = $request->input();
        $code = $data["code"];
        $subject = Subject::where('code',$code)->first();
        $subject->name = $data["name"];
        $subject->credits = $data["credit"];
        $subject->code = $data["code"];
        $subject->save();
        return redirect()->route('subjects.index');

    }

    public function destroy($subject_code){
        // delete specific subjects - delete request
        // check for permission to delete this stuff by current hod
        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;
        $department_code = $faculty->department_code;

        $response=Subject::where('code',$subject_code)->delete();
        // use this to send response to confirm deletion
        return redirect()->route('subjects.index');

    }
}
