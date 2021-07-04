<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Exceptions\IntendedException;
use App\Http\Requests\FeedbackCourseRequest;
use App\Models\Feedback;
use App\Services\FeedbackService;

class FeedbackCourseController extends Controller
{
    protected FeedbackService $service;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->middleware("role:" . Roles::STUDENT, ["only" => ["create", "store"]]);
        $this->middleware("permission:feedback.list", ["only" => "index"]);
        $this->service = $feedbackService;
    }

    public function index(FeedbackCourseRequest $request)
    {
        return view("feedback.index", [
           "feedbacks" => $this->service->getAllFeedback($request->authUser, $request->course->id)
        ]);
    }

    public function create(FeedbackCourseRequest $request)
    {
        return view("feedback.create", [
            "format" => $request->course->getFeedbackFormat()
        ]);
    }

    public function store(FeedbackCourseRequest $request)
    {
        $datas = $request->input("data");

        if (!$this->service->verifyFaculties($datas, $request->course)) {
            abort(400, "Invalid faculties");
        }

        $feedbacks = array();
        foreach ($datas as $faculty_id => $facultyFeedback) {
            try {
                $feedbackData = $this->service->combineFeedbackWithFormat($facultyFeedback, $request->course);
            } catch (IntendedException $e) {
                abort(400, $e->getMessage());
            }
            array_push($feedbacks, [
                "data" => $feedbackData,
                "faculty_id" => $faculty_id,
                "course_id" => $request->course->id
            ]);
        }
        Feedback::insert($feedbacks);

        return response("OK");
    }
}
