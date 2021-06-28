<?php

namespace App\Http\Controllers;

use App\Enums\RequestTypes;
use App\Enums\Roles;
use App\Models\Faculty;
use App\Models\RequestModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware("role:" . Roles::STUDENT);
    }

    public function create()
    {
        return view("testrequest.create");
    }

    public function index()
    {
        return redirect(route("testRequestCreate"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required"
        ]);

        $name = $request->input("name");
        $auth_user = Auth::user();
        $student = $auth_user->student;

        $signees = [
            $student->classroom->getRandomAdvisor()->user_id,
            $student->classroom->department->getHOD()->user_id,
            Faculty::getPrincipal()->user_id
        ];

        $request_id = RequestModel::createNewRequest(
            RequestTypes::TEST_REQUEST,
            new Student(),
            $student->admission_id,
            ["name" => $name],
            $signees
        );

        if ($request_id == null) {
            abort(400, "The last request is still pending, please try again later");
        }

        return response(json_encode(["id" => $request_id]));
    }
}
