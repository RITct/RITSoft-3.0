<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct(){
        $this->middleware('permission:subject-list|subject-create|subject-delete|subject-edit', ['only' => ['index', 'show']]);
        $this->middleware('permission:subject-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:subject-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:subject-delete', ['only' => ['destroy']]);
    }

    public function create(){
        return view('subjects.create');
    }

    public function store(Request $request){
        $this->validate($request, [
           "name" => "required"
        ]);

        Subject::create($request->all());
        return redirect()->route('subjects.index')
            ->with("success", "Subject has been created successfully");
    }

    public function show($id){
        $subject = Subject::find($id);
        return view("subjects.show", compact("subject"));
    }

    public function index(Request $request){
        $data = Subject::orderBy("id", "DESC")->paginate(5);
        return view("subjects.index", compact("data"))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function destroy($id){
        Subject::destroy($id);
        return redirect()->route("subjects.index")->with("success", "Subject Deleted Successfully");
    }
}
