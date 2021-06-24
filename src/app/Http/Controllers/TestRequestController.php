<?php

namespace App\Http\Controllers;

use App\Enums\RequestTypes;
use App\Enums\Roles;
use App\Models\RequestSignee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware("role:" . Roles::STUDENT, ["only" => ["create", "store"]]);
    }

    public function index()
    {

    }

    public function create()
    {
        return view("testrequest.create");
    }

    public function store(Request $request)
    {
        $new_name = $request->input("name");
        $payload = json_encode([
            "name" => $new_name
        ]);
        $table_name = "students";
        $primary_key = Auth::user()->student_admission_id;
        $student = Auth::user()->student;
        $signees = [$student->classroom->staffAdvisors->first()->user, $student->department()->getHOD()->user];

        $new_request = \App\Models\Request::create([
            "payload" => $payload,
            "table_name" => $table_name,
            "primary_key" => $primary_key,
            "type" => RequestTypes::TEST_REQUEST
        ]);

        $i = 1;
        foreach ($signees as $signee) {
            $request = new RequestSignee();
            $request->user_id  = $signee->id;
            $request->request_id = $new_request->id;
            $request->position = $i;
            $request->save();
            $i++;
        }
    }

    public function show(Request $request)
    {

    }
}
