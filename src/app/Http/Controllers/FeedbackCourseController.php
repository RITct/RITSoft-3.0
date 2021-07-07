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
        if ($this->service->isFeedbackComplete($request->authUser, $request->course->id)) {
            abort(409, "You already completed feedback for this course");
        }

        return view("feedback.create", [
            "courseId" => $request->course->id,
            "faculties" => $request->course->faculties,
            "format" => $request->course->getFeedbackFormat()
        ]);
    }

    public function store(FeedbackCourseRequest $request)
    {
        /*
         * {
         *  faculty1_id: {
         *      <feedback data>
         *  },
         *  faculty_2_id: {
         *      <feedback data>
         *  }
         *  ...
         * }
         */
        $datas = $request->json("data");

        if (!$this->service->verifyFaculties($datas, $request->course)) {
            abort(400, "Invalid faculties");
        }

        if ($this->service->isFeedbackComplete($request->authUser, $request->course->id)) {
            abort(409, "You already completed feedback for this course");
        }

        $feedbacks = array();
        foreach ($datas as $faculty_id => $facultyFeedback) {
            try {
                $feedbackData = $this->service->combineFeedbackWithFormat($facultyFeedback, $request->course);
            } catch (IntendedException $e) {
                abort(400, $e->getMessage());
            }
            array_push($feedbacks, [
                // Eloquent isn't used for bulk insert so mutators won't word, so format manually
                "data" => json_encode($feedbackData),
                "faculty_id" => $faculty_id,
                "course_id" => $request->course->id
            ]);
        }
        // Bulk insert feedback and set feedback to complete
        Feedback::insert($feedbacks);
        $request->authUser->student->finishFeedback($request->course->id);

        return response("OK");
    }
}
